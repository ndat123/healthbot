@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Cuộc Trò Chuyện</h1>
            <p class="text-gray-600">Quản lý các cuộc trò chuyện với bệnh nhân của bạn</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($conversations->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($conversations as $conversation)
                        <a href="{{ route('doctor.conversation', $conversation['user_id']) }}" 
                           class="block p-6 hover:bg-blue-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($conversation['user_name'], 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $conversation['user_name'] }}</h3>
                                            @if($conversation['unread_count'] > 0)
                                                <span class="bg-red-500 text-white text-xs font-bold rounded-full px-2 py-1">
                                                    {{ $conversation['unread_count'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 truncate mt-1">
                                            {{ $conversation['last_message'] ?? 'Chưa có tin nhắn' }}
                                        </p>
                                        @if($conversation['last_message_time'])
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ \Carbon\Carbon::parse($conversation['last_message_time'])->diffForHumans() }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-gray-600 text-lg">Chưa có cuộc trò chuyện nào</p>
                    <p class="text-sm text-gray-500 mt-2">Bệnh nhân sẽ có thể bắt đầu trò chuyện với bạn từ trang hồ sơ của bạn</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

