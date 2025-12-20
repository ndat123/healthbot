<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI HealthBot - Personalized Health Consultation</title>
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
                        Home
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
                                <span>Services</span>
                            </a>
                            <a href="#about" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>About</span>
                            </a>
                            <a href="#testimonials" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                                <span>Testimonials</span>
                            </a>
                            <a href="#contact" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors whitespace-nowrap">
                                <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Contact</span>
                            </a>
                        </div>
                    </div>
                </div>
                @auth
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('ai-consultation.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">AI Chat</a>
                        <a href="{{ route('nutrition.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Nutrition</a>
                        <a href="{{ route('health-tracking.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Tracking</a>
                        <a href="{{ route('health-journal.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Journal</a>
                        <a href="{{ route('health-plans.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Plans</a>
                        
                        <!-- User Avatar Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-blue-500">
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
                                    <a href="{{ route('profile.index') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Profile</span>
                                    </a>
                                    <a href="{{ route('profile.index') }}#settings" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-5 h-5 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>Settings</span>
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">Sign Up</a>
                    </div>
                @endauth
                <a href="/admin/login" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">Admin</a>
            </nav>
            <button class="md:hidden text-gray-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
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
                    <p class="text-blue-100">Revolutionizing healthcare with AI-powered personalized consultations.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-blue-100 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="#services" class="text-blue-100 hover:text-white transition-colors duration-200">Services</a></li>
                        <li><a href="#about" class="text-blue-100 hover:text-white transition-colors duration-200">About</a></li>
                        <li><a href="#testimonials" class="text-blue-100 hover:text-white transition-colors duration-200">Testimonials</a></li>
                        <li><a href="#contact" class="text-blue-100 hover:text-white transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Our Services</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Personalized Health Plans</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">AI Diagnostics</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Nutrition Consultations</a></li>
                        <li><a href="#" class="text-blue-100 hover:text-white transition-colors duration-200">Fitness Programs</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Us</h4>
                    <address class="text-blue-100 not-italic">
                        <p>123 Health Street</p>
                        <p>Wellness City, HC 10001</p>
                        <p>Email: info@aihealthbot.com</p>
                        <p>Phone: (123) 456-7890</p>
                    </address>
                </div>
            </div>
            <div class="border-t border-blue-500 mt-8 pt-8 text-center text-blue-100">
                <p>© {{ date('Y') }} AI HealthBot. All rights reserved.</p>
                <p class="mt-2">Made with ❤️ by LaraCopilot</p>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            // Mobile menu toggle
            $('button[class*="md:hidden"]').click(function() {
                $('nav[class*="hidden md:flex"]').toggleClass('hidden');
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
                    alert('This feature is coming soon!');
                }
            });
        });
    </script>

    <script src="{{ asset('assets/js/mobile-menu.js') }}"></script>
</body>
</html>