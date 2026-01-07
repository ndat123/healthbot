<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    /**
     * Get Gemini API key from environment
     */
    private function getApiKey(): string
    {
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            Log::error('GEMINI_API_KEY not found in environment');
            throw new \RuntimeException('GEMINI_API_KEY chưa được cấu hình trong file .env. Vui lòng thêm API key của bạn.');
        }
        
        // Trim whitespace
        $apiKey = trim($apiKey);
        
        // Validate API key format (Gemini API keys typically start with AIza)
        if (strlen($apiKey) < 20) {
            Log::error('GEMINI_API_KEY appears to be invalid (too short)', ['length' => strlen($apiKey)]);
            throw new \RuntimeException('GEMINI_API_KEY có vẻ không hợp lệ. Vui lòng kiểm tra lại API key.');
        }
        
        return $apiKey;
    }

    /**
     * Generate content using Gemini API
     * 
     * @param string $prompt User prompt
     * @param string $systemInstruction System instruction (optional)
     * @param array $conversationHistory Conversation history (optional)
     * @param array $options Additional options (temperature, maxTokens, etc.)
     * @return string AI response
     */
    public function generateContent(
        string $prompt,
        ?string $systemInstruction = null,
        array $conversationHistory = [],
        array $options = []
    ): string {
        $apiKey = $this->getApiKey();
        
        // Log API key status (first 10 chars only for security)
        Log::info('Gemini API Request', [
            'api_key_prefix' => substr($apiKey, 0, 10) . '...',
            'api_key_length' => strlen($apiKey),
            'model' => $options['model'] ?? 'gemini-pro'
        ]);
        
        try {
            set_time_limit($options['timeout'] ?? 120);
            
            // Build contents array for Gemini API
            $contents = [];
            
            // Add system instruction as first user message if provided (v1 doesn't support systemInstruction field)
            if ($systemInstruction) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => "System Instructions: " . $systemInstruction . "\n\nPlease follow these instructions for all responses."]]
                ];
                $contents[] = [
                    'role' => 'model',
                    'parts' => [['text' => "Understood. I will follow these instructions."]]
                ];
            }
            
            // Add conversation history
            foreach ($conversationHistory as $history) {
                if (isset($history['user_message'])) {
                    $contents[] = [
                        'role' => 'user',
                        'parts' => [['text' => $history['user_message']]]
                    ];
                }
                if (isset($history['ai_response'])) {
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [['text' => $history['ai_response']]]
                    ];
                }
            }
            
            // Add current prompt
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ];
            
            // Build request payload
            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.7,
                    'maxOutputTokens' => $options['max_tokens'] ?? 16384, // Increase to max for gemini-2.5-flash
                    'topP' => $options['top_p'] ?? 0.95,
                    'topK' => $options['top_k'] ?? 40,
                ],
            ];
            
            // Determine model - use gemini-2.5-flash (available model)
            // Available models: gemini-2.5-flash, gemini-2.5-pro, gemini-2.0-flash, gemini-flash-latest
            $model = $options['model'] ?? 'gemini-2.5-flash';
            
            // URL encode API key to handle special characters
            $encodedApiKey = urlencode($apiKey);
            
            // Try v1 API instead of v1beta (v1beta may not support all models)
            $apiVersion = 'v1';
            
            // Build URL - use v1 instead of v1beta
            $url = "https://generativelanguage.googleapis.com/{$apiVersion}/models/{$model}:generateContent?key={$encodedApiKey}";
            
            Log::info('Gemini API Request URL', ['url' => str_replace($apiKey, '***', $url)]);
            
            // Make API request with retry logic
            $maxRetries = $options['retry'] ?? 1;
            $retryDelay = 2; // seconds
            $lastException = null;
            $response = null;
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    if ($attempt > 1) {
                        Log::info("Gemini API Retry attempt {$attempt}", ['max_retries' => $maxRetries]);
                        sleep($retryDelay * ($attempt - 1)); // Exponential backoff
                    }
                    
                    $response = Http::timeout($options['http_timeout'] ?? 120)
                        ->withOptions(['verify' => false])
                        ->post($url, $payload);
                    
                    // If successful, break out of retry loop
                    if ($response->successful()) {
                        break;
                    }
                    
                    // If it's not a timeout/connection error, don't retry
                    if ($response->status() !== 0 && $response->status() < 500) {
                        break;
                    }
                    
                    $lastException = new \RuntimeException("HTTP Status: {$response->status()}");
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $lastException = $e;
                    if ($attempt < $maxRetries) {
                        Log::warning("Gemini API Connection error, will retry", [
                            'attempt' => $attempt,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                } catch (\Exception $e) {
                    $lastException = $e;
                    // Don't retry for non-connection errors
                    break;
                }
            }
            
            // If we exhausted retries and still have an exception, throw it
            if ($lastException && !$response) {
                throw $lastException;
            }
            
            // If no response after all retries, throw error
            if (!$response) {
                throw new \RuntimeException('Không thể kết nối đến Gemini API sau ' . $maxRetries . ' lần thử');
            }
            
            // Log response for debugging
            if (!$response->successful()) {
                $errorData = $response->json();
                Log::error('Gemini API Error Response', [
                    'status' => $response->status(),
                    'error' => $errorData
                ]);
            }
            
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Extract text from Gemini response
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $responseData['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Clean up markdown code blocks if present
                    $content = preg_replace('/^```json\s*/i', '', $content);
                    $content = preg_replace('/^```\s*/i', '', $content);
                    $content = preg_replace('/\s*```$/i', '', $content);
                    $content = trim($content);
                    
                    Log::info('Gemini API successful', ['model' => $model]);
                    return $content;
                } else {
                    Log::error('Gemini API invalid response structure', ['response' => $responseData]);
                    throw new \RuntimeException('Gemini trả về phản hồi không hợp lệ');
                }
            } else {
                $errorData = $response->json();
                $error = $errorData['error']['message'] ?? 'Unknown API error';
                $errorCode = $errorData['error']['code'] ?? 'UNKNOWN';
                
                // More detailed error logging
                Log::error('Gemini API Error Details', [
                    'status_code' => $response->status(),
                    'error_code' => $errorCode,
                    'error_message' => $error,
                    'full_error' => $errorData
                ]);
                
                throw new \RuntimeException("Lỗi Gemini API ({$errorCode}): {$error}");
            }
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Không thể lấy phản hồi từ Gemini: ' . $e->getMessage());
        }
    }

    /**
     * Generate JSON content using Gemini API
     * 
     * @param string $prompt User prompt
     * @param string $systemInstruction System instruction
     * @param array $conversationHistory Conversation history
     * @param array $options Additional options
     * @return string JSON response
     */
    public function generateJsonContent(
        string $prompt,
        ?string $systemInstruction = null,
        array $conversationHistory = [],
        array $options = []
    ): string {
        // Add JSON format instruction to system prompt
        if ($systemInstruction) {
            $systemInstruction .= "\n\nQUAN TRỌNG: Trả về CHỈ JSON thuần túy, KHÔNG có ```json, KHÔNG có markdown, KHÔNG có giải thích thêm. Chỉ JSON object bắt đầu với { và kết thúc với }. Đảm bảo JSON HOÀN CHỈNH và hợp lệ.";
        } else {
            $systemInstruction = "Trả về CHỈ JSON thuần túy, KHÔNG có ```json, KHÔNG có markdown, KHÔNG có giải thích thêm. Chỉ JSON object bắt đầu với { và kết thúc với }. Đảm bảo JSON HOÀN CHỈNH và hợp lệ.";
        }
        
        return $this->generateContent($prompt, $systemInstruction, $conversationHistory, $options);
    }
}

