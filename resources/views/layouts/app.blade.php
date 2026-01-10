<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI HealthBot - Tư Vấn Sức Khỏe Cá Nhân Hóa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#10B981',
                        accent: '#8B5CF6',
                        dark: '#1E293B',
                        light: '#F8FAFC'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Merriweather', 'serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .dropdown-container {
            position: relative;
        }
        .dropdown-container .absolute {
            transform: translateY(-10px);
        }
        .dropdown-container.group:hover .absolute {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-50 font-sans text-gray-800">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-10 h-10 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm0 0l9-5-4.5-2.5" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800">AI HealthBot</span>
            </a>
            <nav class="hidden md:flex space-x-8 items-center">
                <div class="relative dropdown-container group">
                    <a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center">
                        Trang chủ
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <div class="absolute top-full left-0 mt-2 w-auto min-w-fit bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                        <div class="py-2">
                            <a href="#services" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Dịch vụ</span>
                            </a>
                            <a href="#about" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Về chúng tôi</span>
                            </a>
                            <a href="#testimonials" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <span>Đánh giá</span>
                            </a>
                            <a href="#contact" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Liên hệ</span>
                            </a>
                        </div>
                    </div>
                </div>
                @auth
                    <div class="flex items-center space-x-4">
                        <!-- Services Dropdown -->
                        <div class="relative dropdown-container group">
                            <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors duration-200 flex items-center">
                                Dịch vụ
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>
                            <div class="absolute top-full left-0 mt-2 w-auto min-w-fit bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="py-2">
                                    <a href="{{ route('ai-consultation.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span>Chat AI</span>
                                    </a>
                                    <a href="{{ route('nutrition.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                        </svg>
                                        <span>Dinh dưỡng</span>
                                    </a>
                                    <a href="{{ route('health-tracking.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span>Theo dõi</span>
                                    </a>
                                    <a href="{{ route('health-journal.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span>Nhật ký</span>
                                    </a>
                                    <a href="{{ route('health-plans.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                        <span>Kế hoạch</span>
                                    </a>
                                    <a href="{{ route('doctor.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Bác sĩ</span>
                                    </a>
                                    <a href="{{ route('medical-content.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span>Kiến thức y tế</span>
                                    </a>
                                    <a href="{{ route('medical-content.bookmarks') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                                        </svg>
                                        <span>Đánh dấu của tôi</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notification Bell -->
                        <div class="relative notification-dropdown" id="notificationDropdown">
                            <button class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors focus:outline-none" id="notificationBell">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center font-bold hidden" id="notificationBadge">0</span>
                            </button>
                            
                            <!-- Notification Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all duration-300 z-50 max-h-96 overflow-hidden flex flex-col" id="notificationMenu">
                                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-gray-800">Thông báo</h3>
                                    <div class="flex items-center space-x-2">
                                        <button class="text-sm text-blue-600 hover:text-blue-700 font-medium" id="markAllReadBtn">Đánh dấu tất cả đã đọc</button>
                                        <button class="text-sm text-red-600 hover:text-red-700 font-medium" id="clearAllBtn">Xóa tất cả</button>
                                    </div>
                                </div>
                                <div class="overflow-y-auto flex-1" id="notificationList">
                                    <div class="p-8 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p>Không có thông báo nào</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Avatar Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-blue-500">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold border-2 border-blue-500">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                                <div class="py-2">
                                    @if(Auth::user()->role === 'doctor')
                                        <a href="{{ route('doctor.conversations') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-600 transition-colors">
                                            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <span>Trò chuyện</span>
                                        </a>
                                        <div class="border-t border-gray-200 my-1"></div>
                                    @endif
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-yellow-50 hover:text-yellow-600 transition-colors">
                                            <svg class="w-5 h-5 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>Quản trị</span>
                                        </a>
                                        <div class="border-t border-gray-200 my-1"></div>
                                    @endif
                                    <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Hồ sơ</span>
                                    </a>
                                        <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Lưu trữ</span>
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Đăng xuất</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">Đăng ký</a>
                    </div>
                @endauth
            </nav>
            <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none" aria-label="Toggle menu">
                <svg id="menu-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-200 shadow-lg">
            <div class="container mx-auto px-4 py-4 space-y-4">
                <a href="#services" class="block text-gray-600 hover:text-blue-600 transition-colors py-2">Trang chủ</a>
                @auth
                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-800 mb-2">Dịch vụ</p>
                        <a href="{{ route('ai-consultation.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Chat AI</a>
                        <a href="{{ route('nutrition.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Dinh dưỡng</a>
                        <a href="{{ route('health-tracking.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Theo dõi</a>
                        <a href="{{ route('health-journal.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Nhật ký</a>
                        <a href="{{ route('health-plans.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Kế hoạch</a>
                        <a href="{{ route('doctor.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Bác sĩ</a>
                        <a href="{{ route('medical-content.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Kiến thức y tế</a>
                        <a href="{{ route('medical-content.bookmarks') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2 pl-4">Đánh dấu của tôi</a>
                    </div>
                    @if(Auth::user()->role === 'doctor')
                        <a href="{{ route('doctor.conversations') }}" class="block text-gray-600 hover:text-green-600 transition-colors py-2">Trò chuyện</a>
                    @endif
                    <a href="{{ route('profile.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2">Hồ sơ</a>
                    <a href="{{ route('settings.index') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2">Lưu trữ</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="block w-full text-left text-gray-600 hover:text-red-600 transition-colors py-2">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-600 hover:text-blue-600 transition-colors py-2">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">AI HealthBot</h3>
                    <p class="text-blue-100">Cách mạng hóa chăm sóc sức khỏe với tư vấn cá nhân hóa được hỗ trợ bởi AI.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liên kết nhanh</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-blue-100 hover:text-white transition-colors duration-200">Trang chủ</a></li>
                        <li><a href="#services" class="text-blue-100 hover:text-white transition-colors duration-200">Dịch vụ</a></li>
                        <li><a href="#about" class="text-blue-100 hover:text-white transition-colors duration-200">Về chúng tôi</a></li>
                        <li><a href="#testimonials" class="text-blue-100 hover:text-white transition-colors duration-200">Đánh giá</a></li>
                        <li><a href="#contact" class="text-blue-100 hover:text-white transition-colors duration-200">Liên hệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Dịch vụ của chúng tôi</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Kế hoạch sức khỏe cá nhân hóa</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Chẩn đoán AI</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Tư vấn dinh dưỡng</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Chương trình thể dục</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Liên hệ với chúng tôi</h4>
                    <address class="text-blue-100 not-italic">
                        <p>123 Đường Sức Khỏe</p>
                        <p>Thành phố Sức Khỏe, HC 10001</p>
                        <p>Email: info@aihealthbot.com</p>
                        <p>Điện thoại: (123) 456-7890</p>
                    </address>
                </div>
            </div>
            <div class="border-t border-blue-500 mt-8 pt-8 text-center text-blue-100">
                <p>© {{ date('Y') }} AI HealthBot. Bảo lưu mọi quyền.</p>
                <p class="mt-2">Được tạo với ❤️ bởi LaraCopilot</p>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('#mobile-menu-button').click(function() {
                $('#mobile-menu').toggleClass('hidden');
                $('#menu-icon').toggleClass('hidden');
                $('#close-icon').toggleClass('hidden');
            });
            
            // Close mobile menu when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('#mobile-menu-button, #mobile-menu').length) {
                    $('#mobile-menu').addClass('hidden');
                    $('#menu-icon').removeClass('hidden');
                    $('#close-icon').addClass('hidden');
                }
            });

            // Smooth scrolling for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = this.hash;
                var $target = $(target);

                if ($target.length) {
                    $('html, body').stop().animate({
                        'scrollTop': $target.offset().top - 80
                    }, 900, 'swing', function() {
                        window.location.hash = target;
                    });
                } else {
                    // If target not found and it's Home link, navigate to home page
                    if ($(this).hasClass('home-link')) {
                        window.location.href = '/#services';
                    }
                }
            });

            // Handle hash in URL on page load (for direct links like /#services)
            if (window.location.hash) {
                var hash = window.location.hash;
                var $target = $(hash);
                if ($target.length) {
                    setTimeout(function() {
                        $('html, body').stop().animate({
                            'scrollTop': $target.offset().top - 80
                        }, 500);
                    }, 100);
                }
            }

            // Coming soon alerts
            $('a[href="#"]').click(function(e) {
                if (!$(this).data('scroll-to')) {
                    e.preventDefault();
                    alert('Tính năng này sắp ra mắt!');
                }
            });

            // Notification Bell Functionality
            @auth
            const notificationBell = $('#notificationBell');
            const notificationMenu = $('#notificationMenu');
            const notificationList = $('#notificationList');
            const notificationBadge = $('#notificationBadge');
            const markAllReadBtn = $('#markAllReadBtn');
            const clearAllBtn = $('#clearAllBtn');

            // Toggle notification dropdown
            notificationBell.on('click', function(e) {
                e.stopPropagation();
                notificationMenu.toggleClass('opacity-0 invisible opacity-100 visible');
                if (!notificationMenu.hasClass('opacity-0')) {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.notification-dropdown').length) {
                    notificationMenu.addClass('opacity-0 invisible').removeClass('opacity-100 visible');
                }
            });

            // Load notifications
            function loadNotifications() {
                $.ajax({
                    url: '{{ route("notifications.index") }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        updateNotificationBadge(response.unread_count);
                        renderNotifications(response.notifications);
                    },
                    error: function(xhr) {
                        console.error('Error loading notifications:', xhr);
                    }
                });
            }

            // Update notification badge
            function updateNotificationBadge(count) {
                if (count > 0) {
                    notificationBadge.text(count > 99 ? '99+' : count).removeClass('hidden');
                } else {
                    notificationBadge.addClass('hidden');
                }
            }

            // Render notifications
            function renderNotifications(notifications) {
                if (notifications.length === 0) {
                    notificationList.html(`
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <p>Không có thông báo nào</p>
                        </div>
                    `);
                    return;
                }

                // Get notification type colors and icons
                function getNotificationStyle(type) {
                    const styles = {
                        'reminder': {
                            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            bgColor: 'bg-purple-50',
                            borderColor: 'border-purple-200',
                            iconColor: 'text-purple-600',
                            badgeColor: 'bg-purple-500'
                        },
                        'appointment': {
                            icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                            bgColor: 'bg-blue-50',
                            borderColor: 'border-blue-200',
                            iconColor: 'text-blue-600',
                            badgeColor: 'bg-blue-500'
                        },
                        'health': {
                            icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                            bgColor: 'bg-green-50',
                            borderColor: 'border-green-200',
                            iconColor: 'text-green-600',
                            badgeColor: 'bg-green-500'
                        },
                        'general': {
                            icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                            bgColor: 'bg-gray-50',
                            borderColor: 'border-gray-200',
                            iconColor: 'text-gray-600',
                            badgeColor: 'bg-gray-500'
                        }
                    };
                    return styles[type] || styles['general'];
                }

                // Parse message để extract badges
                function parseNotificationMessage(message, metadata) {
                    let parsedMessage = message;
                    let badges = [];
                    
                    // Extract reminder type từ message hoặc metadata
                    if (metadata && metadata.reminder_type) {
                        const reminderTypeLabels = {
                            'medication': { text: 'Thuốc', color: 'bg-purple-100 text-purple-700 border-purple-300' },
                            'water': { text: 'Nước', color: 'bg-blue-100 text-blue-700 border-blue-300' },
                            'exercise': { text: 'Tập thể dục', color: 'bg-green-100 text-green-700 border-green-300' },
                            'meal': { text: 'Bữa ăn', color: 'bg-orange-100 text-orange-700 border-orange-300' },
                            'appointment': { text: 'Cuộc hẹn', color: 'bg-indigo-100 text-indigo-700 border-indigo-300' },
                            'other': { text: 'Khác', color: 'bg-gray-100 text-gray-700 border-gray-300' }
                        };
                        
                        const typeInfo = reminderTypeLabels[metadata.reminder_type];
                        if (typeInfo) {
                            badges.push(typeInfo);
                            // Remove từ message nếu có
                            parsedMessage = parsedMessage.replace(new RegExp(`\\(${typeInfo.text}\\)`, 'g'), '');
                        }
                    }
                    
                    // Extract "Lặp lại" từ message
                    if (message.includes('Lặp lại') || message.includes('(Lặp lại)')) {
                        badges.push({
                            text: '🔄 Lặp lại',
                            color: 'bg-cyan-100 text-cyan-700 border-cyan-300'
                        });
                        parsedMessage = parsedMessage.replace(/\(Lặp lại\)/g, '').trim();
                    }
                    
                    // Extract thời gian từ message
                    let timeBadge = null;
                    const timeMatch = message.match(/Thời gian:\s*(\d{2}:\d{2})/);
                    if (timeMatch) {
                        timeBadge = {
                            text: `⏰ ${timeMatch[1]}`,
                            color: 'bg-amber-100 text-amber-700 border-amber-300'
                        };
                        parsedMessage = parsedMessage.replace(/Thời gian:\s*\d{2}:\d{2}/g, '').trim();
                    }
                    
                    return {
                        message: parsedMessage,
                        badges: badges,
                        timeBadge: timeBadge
                    };
                }

                let html = '';
                notifications.forEach(function(notification) {
                    const isRead = notification.is_read;
                    const timeAgo = getTimeAgo(notification.created_at);
                    const style = getNotificationStyle(notification.type);
                    const iconPath = getNotificationIcon(notification.type);
                    
                    // Parse message để extract badges
                    const parsed = parseNotificationMessage(notification.message, notification.metadata);
                    
                    html += `
                        <div class="border-l-4 ${style.borderColor} ${!isRead ? style.bgColor : 'bg-white'} notification-item transition-all duration-200 hover:shadow-md" data-id="${notification.id}">
                            <div class="p-4 hover:bg-opacity-80 transition-colors ${notification.action_url ? 'cursor-pointer' : ''}" ${notification.action_url ? `onclick="window.location.href='${notification.action_url}'"` : ''}>
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-10 h-10 ${style.bgColor} rounded-full flex items-center justify-center ${style.borderColor} border-2">
                                            <svg class="w-5 h-5 ${style.iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="flex-1">
                                                <h4 class="text-base font-semibold text-gray-900 ${!isRead ? 'font-bold' : 'font-medium'} leading-tight">
                                                    ${notification.title}
                                                </h4>
                                            </div>
                                            ${!isRead ? `<span class="flex-shrink-0 w-2.5 h-2.5 ${style.badgeColor} rounded-full animate-pulse"></span>` : ''}
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed mb-3">
                                            ${parsed.message}
                                        </p>
                                        ${parsed.badges.length > 0 || parsed.timeBadge ? `
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            ${parsed.badges.map(badge => `
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border ${badge.color}">
                                                    ${badge.text}
                                                </span>
                                            `).join('')}
                                            ${parsed.timeBadge ? `
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold border ${parsed.timeBadge.color}">
                                                    ${parsed.timeBadge.text}
                                                </span>
                                            ` : ''}
                                        </div>
                                        ` : ''}
                                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                                            <span class="text-xs text-gray-500 font-medium">${timeAgo}</span>
                                            <div class="flex items-center gap-2">
                                                ${!isRead ? `<button class="text-xs px-2 py-1 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded mark-read-btn transition-colors" data-id="${notification.id}">Đánh dấu đã đọc</button>` : ''}
                                                <button class="text-xs px-2 py-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded delete-notification-btn transition-colors" data-id="${notification.id}">Xóa</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                notificationList.html(html);
            }

            // Get notification icon path
            function getNotificationIcon(type) {
                const icons = {
                    'reminder': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                    'appointment': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                    'health': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'newsletter': 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                    'system': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
                    'default': 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
                };
                return icons[type] || icons['default'];
            }

            // Get time ago
            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInSeconds = Math.floor((now - date) / 1000);
                
                if (diffInSeconds < 60) return 'Vừa xong';
                if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' phút trước';
                if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' giờ trước';
                if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' ngày trước';
                return date.toLocaleDateString('vi-VN');
            }

            // Mark notification as read
            $(document).on('click', '.mark-read-btn', function(e) {
                e.stopPropagation();
                const notificationId = $(this).data('id');
                $.ajax({
                    url: `/notifications/${notificationId}/read`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        updateNotificationBadge(response.unread_count);
                        loadNotifications();
                    },
                    error: function(xhr) {
                        console.error('Error marking notification as read:', xhr);
                    }
                });
            });

            // Delete notification
            $(document).on('click', '.delete-notification-btn', function(e) {
                e.stopPropagation();
                const notificationId = $(this).data('id');
                $.ajax({
                    url: `/notifications/${notificationId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        updateNotificationBadge(response.unread_count);
                        loadNotifications();
                    },
                    error: function(xhr) {
                        console.error('Error deleting notification:', xhr);
                    }
                });
            });

            // Mark all as read
            markAllReadBtn.on('click', function(e) {
                e.stopPropagation();
                $.ajax({
                    url: '{{ route("notifications.mark-all-read") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        updateNotificationBadge(0);
                        loadNotifications();
                    },
                    error: function(xhr) {
                        console.error('Error marking all as read:', xhr);
                    }
                });
            });

            // Clear all notifications
            clearAllBtn.on('click', function(e) {
                e.stopPropagation();
                if (confirm('Bạn có chắc chắn muốn xóa tất cả thông báo?')) {
                    $.ajax({
                        url: '{{ route("notifications.destroy-all") }}',
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            updateNotificationBadge(0);
                            loadNotifications();
                        },
                        error: function(xhr) {
                            console.error('Error clearing all notifications:', xhr);
                        }
                    });
                }
            });

            // Load unread count on page load
            function loadUnreadCount() {
                $.ajax({
                    url: '{{ route("notifications.unread-count") }}',
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        updateNotificationBadge(response.count);
                    },
                    error: function(xhr) {
                        console.error('Error loading unread count:', xhr);
                    }
                });
            }

            // Load unread count on page load
            loadUnreadCount();

            // Load unread count only once on page load
            loadUnreadCount();

            // Smart polling for real-time notifications - OPTIMIZED
            let lastUnreadCount = 0;
            let pollingInterval = null;
            
            function startPolling() {
                // Slower polling (30 seconds) to reduce server load
                function checkAndPoll() {
                    $.ajax({
                        url: '{{ route("notifications.unread-count") }}',
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            const currentCount = response.count;
                            
                            // Only update if count changed
                            if (currentCount !== lastUnreadCount) {
                                updateNotificationBadge(currentCount);
                                lastUnreadCount = currentCount;
                                
                                // Only refresh notifications if menu is open
                                if (!notificationMenu.hasClass('opacity-0')) {
                                    loadNotifications();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Error checking unread count:', xhr);
                        }
                    });
                }
                
                // Poll every 30 seconds (reduced from 2-10 seconds)
                pollingInterval = setInterval(checkAndPoll, 30000);
                checkAndPoll();
            }
            
            // Start polling
            startPolling();
            @endauth
        });
    </script>

    @if(file_exists(public_path('assets/js/mobile-menu.js')))
    <script src="{{ asset('assets/js/mobile-menu.js') }}" defer></script>
    @endif
</body>
</html>