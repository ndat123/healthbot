@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('doctor.show', $doctor->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Quay lại Hồ sơ Bác sĩ
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Doctor Info Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                    <div class="text-center mb-4">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl border-4 border-white shadow-lg mx-auto mb-3">
                            {{ strtoupper(substr($doctor->name, 0, 1)) }}
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">{{ $doctor->name }}</h3>
                        <p class="text-sm text-blue-600 font-medium">{{ $doctor->specialization }}</p>
                    </div>
                    <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $doctor->years_of_experience }} năm kinh nghiệm
                        </div>
                        @if($doctor->email)
                        <div class="flex items-center text-gray-600">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $doctor->email }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Chat Interface -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg h-[600px] flex flex-col">
                    <!-- Chat Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="font-semibold">{{ $doctor->name }}</h2>
                                    <p class="text-xs text-blue-100" id="doctor-status">Đang hoạt động</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
                        @if($messages->count() > 0)
                            @foreach($messages as $message)
                                <div class="flex items-start space-x-3 {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }}">
                                    @if($message->sender_type === 'doctor')
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1 {{ $message->sender_type === 'user' ? 'flex justify-end' : '' }}">
                                        <div class="{{ $message->sender_type === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg p-3 max-w-[80%]">
                                            <div class="text-sm whitespace-pre-line">{{ $message->message }}</div>
                                            <div class="text-xs mt-1 {{ $message->sender_type === 'user' ? 'text-blue-100' : 'text-gray-500' }}">
                                                {{ $message->created_at->format('g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($message->sender_type === 'user')
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <p class="text-gray-600">Bắt đầu trò chuyện với {{ $doctor->name }}</p>
                                    <p class="text-sm text-gray-500 mt-2">Gửi tin nhắn để bắt đầu</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Chat Input -->
                    <div class="border-t p-4">
                        <form id="chat-form" class="flex space-x-2">
                            @csrf
                            <input type="text" 
                                   id="message-input" 
                                   placeholder="Nhập tin nhắn của bạn..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                            <button type="submit" 
                                    id="send-btn"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const doctorId = {{ $doctor->id }};
    let lastMessageId = {{ $messages->count() > 0 ? $messages->last()->id : 0 }};
    let isPolling = false;

    // Scroll to bottom on load
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

    // Send message
    $('#chat-form').submit(function(e) {
        e.preventDefault();
        
        const message = $('#message-input').val().trim();
        if (!message) return;

        // Add user message to chat immediately
        addMessage(message, 'user');
        $('#message-input').val('');
        $('#send-btn').prop('disabled', true);

        // Send to server
        $.ajax({
            url: '{{ route("doctor.chat.send", $doctor->id) }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                message: message
            },
            success: function(response) {
                if (response.success) {
                    lastMessageId = response.message.id;
                    $('#send-btn').prop('disabled', false);
                    $('#message-input').focus();
                }
            },
            error: function(xhr) {
                $('#send-btn').prop('disabled', false);
                alert('Không thể gửi tin nhắn. Vui lòng thử lại.');
            }
        });
    });

    // Add message to chat
    function addMessage(message, senderType) {
        const isUser = senderType === 'user';
        const bgColor = isUser ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900';
        const alignClass = isUser ? 'justify-end' : 'justify-start';
        const iconBg = isUser ? 'bg-blue-500' : 'bg-blue-100';
        const iconColor = isUser ? 'text-white' : 'text-blue-600';
        const timeColor = isUser ? 'text-blue-100' : 'text-gray-500';
        const currentTime = new Date().toLocaleTimeString('vi-VN', { hour: 'numeric', minute: '2-digit' });

        const messageHtml = `
            <div class="flex items-start space-x-3 ${alignClass}">
                ${!isUser ? `
                <div class="w-8 h-8 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                ` : ''}
                <div class="flex-1 ${isUser ? 'flex justify-end' : ''}">
                    <div class="${bgColor} rounded-lg p-3 max-w-[80%]">
                        <div class="text-sm whitespace-pre-line">${escapeHtml(message)}</div>
                        <div class="text-xs mt-1 ${timeColor}">${currentTime}</div>
                    </div>
                </div>
                ${isUser ? `
                <div class="w-8 h-8 ${iconBg} rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                ` : ''}
            </div>
        `;

        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }

    // Escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Poll for new messages
    function pollMessages() {
        if (isPolling) return;
        isPolling = true;

        $.ajax({
            url: '{{ route("doctor.chat.messages", $doctor->id) }}',
            method: 'GET',
            data: {
                last_message_id: lastMessageId
            },
            success: function(response) {
                if (response.success && response.messages.length > 0) {
                    response.messages.forEach(function(msg) {
                        addMessage(msg.message, msg.sender_type);
                        lastMessageId = msg.id;
                    });
                }
            },
            complete: function() {
                isPolling = false;
            }
        });
    }

    // Poll every 3 seconds
    setInterval(pollMessages, 3000);

    // Allow Enter key to send message
    $('#message-input').on('keypress', function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $('#chat-form').submit();
        }
    });
});
</script>
@endsection



