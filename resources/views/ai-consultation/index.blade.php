@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">AI Health Consultation</h1>
            <p class="text-xl text-gray-600">Chat with AI HealthBot for personalized health advice</p>
        </div>

        <!-- Medical Disclaimer Banner -->
        <div id="disclaimer-banner" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-yellow-800">Medical Disclaimer</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p class="mb-2">
                            <strong>Important:</strong> The information provided by AI HealthBot is for educational and informational purposes only. 
                            It is not intended to be a substitute for professional medical advice, diagnosis, or treatment.
                        </p>
                        <p class="mb-3">
                            Always seek the advice of your physician or other qualified health provider with any questions you may have regarding a medical condition. 
                            Never disregard professional medical advice or delay in seeking it because of information provided by this AI.
                        </p>
                        <p class="mb-3">
                            <strong>For medical emergencies, call emergency services immediately.</strong>
                        </p>
                        <label class="flex items-center">
                            <input type="checkbox" id="disclaimer-checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm">I understand and acknowledge this disclaimer</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chat Interface -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg h-[600px] flex flex-col">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold">AI HealthBot</h2>
                                    <p class="text-xs text-blue-100">Online • Ready to help</p>
                                </div>
                            </div>
                            <button id="new-chat-btn" class="text-white hover:text-blue-100 transition-colors" title="New Chat">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-100 rounded-lg p-3">
                                    <p class="text-sm text-gray-700" id="welcome-message">Starting conversation...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Input -->
                    <div class="border-t p-4">
                        <form id="chat-form" class="flex space-x-2">
                            <input type="text" 
                                   id="message-input" 
                                   placeholder="Type your health question here..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   disabled>
                            <button type="submit" 
                                    id="send-btn"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Questions</h3>
                    <div class="space-y-2">
                        <button class="quick-question w-full text-left px-4 py-2 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            What are the symptoms of flu?
                        </button>
                        <button class="quick-question w-full text-left px-4 py-2 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            How to improve my sleep quality?
                        </button>
                        <button class="quick-question w-full text-left px-4 py-2 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            What foods are good for heart health?
                        </button>
                        <button class="quick-question w-full text-left px-4 py-2 bg-gray-50 hover:bg-blue-50 rounded-lg text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            When should I see a doctor?
                        </button>
                    </div>
                </div>

                <!-- Recent Consultations -->
                @if($recentConsultations->count() > 0)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Consultations</h3>
                    <div class="space-y-3">
                        @foreach($recentConsultations->take(5) as $consultation)
                            <div class="border-l-4 border-blue-500 pl-3 py-2">
                                <p class="text-sm font-medium text-gray-800">{{ $consultation->topic ?? 'General' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($consultation->user_message, 50) }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $consultation->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Health Profile Link -->
                @if(!$profile)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Personalize Your Experience</h3>
                    <p class="text-sm text-blue-700 mb-4">Create your health profile to get more personalized AI responses.</p>
                    <a href="{{ route('health-plans.profile') }}" class="block text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Create Profile
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let sessionId = null;
    let disclaimerAcknowledged = false;

    // Check disclaimer
    $('#disclaimer-checkbox').change(function() {
        disclaimerAcknowledged = $(this).is(':checked');
        if (disclaimerAcknowledged) {
            $('#message-input, #send-btn').prop('disabled', false);
            $('#disclaimer-banner').fadeOut();
        } else {
            $('#message-input, #send-btn').prop('disabled', true);
        }
    });

    // Start new session
    function startSession() {
        $.ajax({
            url: '{{ route("ai-consultation.start") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                sessionId = response.session_id;
                $('#chat-messages').html('');
                $('#empty-state').remove();
                addMessage(response.message, 'ai');
            },
            error: function() {
                addMessage('Sorry, there was an error starting the session. Please refresh the page.', 'ai');
            }
        });
    }

    // Send message
    $('#chat-form').submit(function(e) {
        e.preventDefault();
        
        if (!disclaimerAcknowledged) {
            alert('Please acknowledge the medical disclaimer first.');
            return;
        }

        const message = $('#message-input').val().trim();
        if (!message) return;

        // Add user message to chat
        addMessage(message, 'user');
        $('#message-input').val('');
        $('#send-btn').prop('disabled', true);

        // Show typing indicator
        const typingId = addMessage('Thinking...', 'ai', true);

        // Send to server
        $.ajax({
            url: '{{ route("ai-consultation.send") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                message: message,
                session_id: sessionId,
                disclaimer_acknowledged: disclaimerAcknowledged ? 1 : 0
            },
            success: function(response) {
                // Remove typing indicator
                $(`#msg-${typingId}`).remove();
                
                // Add AI response with specialists if available
                let responseText = response.response;
                if (response.suggested_specialists && response.suggested_specialists.length > 0) {
                    responseText += '\n\n**Suggested Specialists:**\n';
                    response.suggested_specialists.forEach(function(spec) {
                        responseText += `• ${spec.charAt(0).toUpperCase() + spec.slice(1).replace('_', ' ')}\n`;
                    });
                }
                
                const aiMessageId = addMessage(responseText, 'ai');
                
                // Highlight emergency level if high
                if (response.emergency_level === 'critical' || response.emergency_level === 'high') {
                    $(`#msg-${aiMessageId}`).find('.bg-gray-100').addClass('border-2 border-red-300 bg-red-50');
                }

                sessionId = response.session_id;
                $('#send-btn').prop('disabled', false);
            },
            error: function(xhr) {
                $(`#msg-${typingId}`).remove();
                let errorMessage = 'Sorry, there was an error processing your request. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 422) {
                    errorMessage = 'Validation error. Please check your input.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please try again later.';
                }
                
                addMessage('❌ ' + errorMessage, 'ai');
                $('#send-btn').prop('disabled', false);
                
                console.error('Error details:', xhr.responseJSON || xhr.responseText);
            }
        });
    });

    // Add message to chat
    function addMessage(message, type, isTyping = false) {
        const messageId = Date.now() + Math.random();
        const isUser = type === 'user';
        const bgColor = isUser ? 'bg-blue-600 text-white' : 'bg-gray-100';
        const alignClass = isUser ? 'justify-end' : 'justify-start';
        const iconBg = isUser ? 'bg-blue-500' : 'bg-blue-100';
        const iconColor = isUser ? 'text-white' : 'text-blue-600';

        const messageHtml = `
            <div id="msg-${messageId}" class="flex items-start space-x-3 ${alignClass}">
                ${!isUser ? `
                <div class="w-8 h-8 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                ` : ''}
                <div class="flex-1 ${isUser ? 'flex justify-end' : ''}">
                    <div class="${bgColor} rounded-lg p-3 max-w-[80%]">
                        <div class="text-sm whitespace-pre-line prose prose-sm max-w-none">${formatMessage(message)}</div>
                    </div>
                </div>
                ${isUser ? `
                <div class="w-8 h-8 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                ` : ''}
            </div>
        `;

        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

        return messageId;
    }

    // Format message (markdown support)
    function formatMessage(message) {
        // Convert markdown bold to HTML
        message = message.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Convert markdown lists
        message = message.replace(/^•\s(.+)$/gm, '<li>$1</li>');
        message = message.replace(/(<li>.*<\/li>)/s, '<ul class="list-disc list-inside mt-2 space-y-1">$1</ul>');
        // Convert line breaks
        message = message.replace(/\n/g, '<br>');
        return message;
    }

    // Quick questions
    $('.quick-question').click(function() {
        const question = $(this).text();
        $('#message-input').val(question);
        $('#chat-form').submit();
    });

    // New chat button
    $('#new-chat-btn').click(function() {
        if (confirm('Start a new conversation?')) {
            sessionId = null;
            startSession();
        }
    });

    // Auto-start session when disclaimer is acknowledged
    $('#disclaimer-checkbox').on('change', function() {
        if ($(this).is(':checked') && !sessionId) {
            startSession();
        }
    });

    // Allow Enter key to send message
    $('#message-input').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            if (disclaimerAcknowledged && !$(this).prop('disabled')) {
                $('#chat-form').submit();
            }
        }
    });
});
</script>
@endsection

