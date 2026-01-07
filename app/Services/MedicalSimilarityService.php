<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MedicalSimilarityService
{
    protected $medicalData = [];
    protected $csvPath;

    public function __construct()
    {
        // Tìm file ViMedical.csv trong các thư mục có thể
        $possiblePaths = [
            storage_path('app/ViMedical.csv'),
            storage_path('app/public/ViMedical.csv'),
            public_path('ViMedical.csv'),
            base_path('ViMedical.csv'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->csvPath = $path;
                $this->loadMedicalData();
                break;
            }
        }
    }

    /**
     * Load medical data from CSV file
     */
    protected function loadMedicalData(): void
    {
        if (!$this->csvPath || !file_exists($this->csvPath)) {
            Log::warning('ViMedical.csv file not found');
            return;
        }

        try {
            $handle = fopen($this->csvPath, 'r');
            if ($handle === false) {
                Log::error('Cannot open ViMedical.csv file');
                return;
            }

            // Read header
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return;
            }

            // Read data rows
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 2) {
                    // Giả sử cột đầu là tên bệnh/triệu chứng, cột thứ 2 là mô tả
                    $this->medicalData[] = [
                        'name' => $row[0] ?? '',
                        'description' => $row[1] ?? '',
                        'full_text' => trim(($row[0] ?? '') . ' ' . ($row[1] ?? ''))
                    ];
                }
            }

            fclose($handle);
            Log::info('Loaded medical data from CSV', ['count' => count($this->medicalData)]);
        } catch (\Exception $e) {
            Log::error('Error loading ViMedical.csv: ' . $e->getMessage());
        }
    }

    /**
     * Calculate similarity percentage between two texts
     */
    protected function calculateSimilarity(string $text1, string $text2): float
    {
        // Normalize texts
        $text1 = $this->normalizeText($text1);
        $text2 = $this->normalizeText($text2);

        // Use multiple similarity methods and average them
        $similarities = [];

        // 1. Jaccard similarity (word-based)
        $similarities[] = $this->jaccardSimilarity($text1, $text2);

        // 2. Cosine similarity (word-based)
        $similarities[] = $this->cosineSimilarity($text1, $text2);

        // 3. Levenshtein-based similarity
        $similarities[] = $this->levenshteinSimilarity($text1, $text2);

        // Return average
        return array_sum($similarities) / count($similarities) * 100;
    }

    /**
     * Normalize text for comparison
     */
    protected function normalizeText(string $text): string
    {
        // Convert to lowercase
        $text = mb_strtolower($text, 'UTF-8');
        
        // Remove special characters but keep Vietnamese characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        
        // Remove extra spaces
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Jaccard similarity (intersection over union)
     */
    protected function jaccardSimilarity(string $text1, string $text2): float
    {
        $words1 = array_unique(explode(' ', $text1));
        $words2 = array_unique(explode(' ', $text2));

        $intersection = count(array_intersect($words1, $words2));
        $union = count(array_unique(array_merge($words1, $words2)));

        return $union > 0 ? $intersection / $union : 0;
    }

    /**
     * Cosine similarity
     */
    protected function cosineSimilarity(string $text1, string $text2): float
    {
        $words1 = explode(' ', $text1);
        $words2 = explode(' ', $text2);
        $allWords = array_unique(array_merge($words1, $words2));

        $vector1 = [];
        $vector2 = [];

        foreach ($allWords as $word) {
            $vector1[] = count(array_keys($words1, $word));
            $vector2[] = count(array_keys($words2, $word));
        }

        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($allWords); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Levenshtein-based similarity
     */
    protected function levenshteinSimilarity(string $text1, string $text2): float
    {
        $maxLength = max(mb_strlen($text1), mb_strlen($text2));
        if ($maxLength == 0) {
            return 1.0;
        }

        $distance = levenshtein($text1, $text2);
        return 1 - ($distance / $maxLength);
    }

    /**
     * Find most similar medical condition for a prompt
     * Returns similarity percentage if between 90-95%, null otherwise
     */
    public function findSimilarity(string $prompt): ?float
    {
        if (empty($this->medicalData)) {
            return null;
        }

        $maxSimilarity = 0;
        $normalizedPrompt = $this->normalizeText($prompt);

        foreach ($this->medicalData as $medical) {
            $similarity = $this->calculateSimilarity($normalizedPrompt, $medical['full_text']);
            
            if ($similarity > $maxSimilarity) {
                $maxSimilarity = $similarity;
            }
        }

        // Chỉ trả về nếu trong khoảng 90-95%
        if ($maxSimilarity >= 90 && $maxSimilarity <= 95) {
            return round($maxSimilarity, 2);
        }

        return null;
    }

    /**
     * Get medical data count
     */
    public function getDataCount(): int
    {
        return count($this->medicalData);
    }
}









