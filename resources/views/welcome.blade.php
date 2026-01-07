@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Tư Vấn Sức Khỏe Cá Nhân Hóa với AI</h1>
                    <p class="text-xl mb-8">Nhận lời khuyên sức khỏe chuyên nghiệp được tùy chỉnh theo nhu cầu độc đáo của bạn bằng công nghệ AI tiên tiến của chúng tôi.</p>
                    <div class="flex space-x-4">
                        @auth
                            <a href="#contact" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors duration-200">Bắt đầu</a>
                        @else
                            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors duration-200">Bắt đầu</a>
                        @endauth
                        <a href="#services" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors duration-200">Tìm hiểu thêm</a>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                        <img src="https://pub-07cce266cb7a4eff97bc6503d84b6470.r2.dev/unnamed.jpg" alt="HealthBot AI Assistant" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Dịch Vụ Sức Khỏe Được Hỗ Trợ Bởi AI</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Giải pháp chăm sóc sức khỏe cách mạng được tùy chỉnh theo nhu cầu cá nhân của bạn</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Service Card 1 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="p-6">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Kế Hoạch Sức Khỏe Cá Nhân Hóa</h3>
                        <p class="text-gray-600 mb-6">AI của chúng tôi phân tích dữ liệu sức khỏe của bạn để tạo một kế hoạch tùy chỉnh phù hợp với nhu cầu và mục tiêu độc đáo của bạn.</p>
                        <div class="text-center">
                            @auth
                                <a href="{{ route('health-plans.index') }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">Tạo Kế Hoạch</a>
                            @else
                                <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">Bắt đầu</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Service Card 4 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="p-6">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Tư Vấn Dinh Dưỡng</h3>
                        <p class="text-gray-600 mb-6">Các chuyên gia dinh dưỡng AI của chúng tôi tạo kế hoạch bữa ăn cá nhân hóa dựa trên mục tiêu sức khỏe, sở thích ăn uống và nhu cầu dinh dưỡng của bạn.</p>
                        <div class="text-center">
                            @auth
                                <a href="{{ route('nutrition.index') }}" class="text-green-600 font-medium hover:text-green-800 transition-colors duration-200">Tìm hiểu thêm</a>
                            @else
                                <a href="{{ route('register') }}" class="text-green-600 font-medium hover:text-green-800 transition-colors duration-200">Bắt đầu</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Service Card 2 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="p-6">
                        <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Chẩn Đoán AI</h3>
                        <p class="text-gray-600 mb-6">Các thuật toán AI tiên tiến phân tích triệu chứng và dữ liệu sức khỏe của bạn để cung cấp chẩn đoán chính xác và khuyến nghị điều trị.</p>
                        <div class="text-center">
                            @auth
                                <a href="{{ route('ai-consultation.index') }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">Bắt đầu Chat</a>
                            @else
                                <a href="{{ route('register') }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors duration-200">Bắt đầu</a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Service Card 3 -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="p-6">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Nhật Ký Sức Khỏe</h3>
                        <p class="text-gray-600 mb-6">Theo dõi sức khỏe hàng ngày, triệu chứng, thức ăn, tập thể dục và tâm trạng của bạn với những hiểu biết và khuyến nghị được hỗ trợ bởi AI.</p>
                        <div class="text-center">
                            @auth
                                <a href="{{ route('health-journal.index') }}" class="text-yellow-600 font-medium hover:text-yellow-800 transition-colors duration-200">Bắt đầu Nhật Ký</a>
                            @else
                                <a href="{{ route('register') }}" class="text-yellow-600 font-medium hover:text-yellow-800 transition-colors duration-200">Bắt đầu</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                        <img src="https://pub-07cce266cb7a4eff97bc6503d84b6470.r2.dev/unnamed.jpg" alt="HealthBot AI Assistant" class="w-full h-auto">
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Về AI HealthBot</h2>
                    <p class="text-gray-600 mb-6">AI HealthBot đang cách mạng hóa ngành chăm sóc sức khỏe với công nghệ AI tiên tiến của chúng tôi. Đội ngũ chuyên gia của chúng tôi kết hợp trí tuệ nhân tạo tiên tiến với kiến thức y tế để cung cấp các giải pháp sức khỏe cá nhân hóa phù hợp với nhu cầu độc đáo của bạn.</p>
                    <p class="text-gray-600 mb-6">Được thành lập vào năm 2023, chúng tôi đã nhanh chóng trở thành người dẫn đầu trong các giải pháp chăm sóc sức khỏe được hỗ trợ bởi AI. Sứ mệnh của chúng tôi là làm cho chăm sóc sức khỏe chất lượng có thể tiếp cận được với mọi người, ở mọi nơi, thông qua sức mạnh của trí tuệ nhân tạo.</p>
                    <div class="flex space-x-6">
                        <div>
                            <h3 class="text-2xl font-bold text-blue-600">100+</h3>
                            <p class="text-gray-600">Khách hàng hài lòng</p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-blue-600">50+</h3>
                            <p class="text-gray-600">Mô hình AI</p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-blue-600">98%</h3>
                            <p class="text-gray-600">Tỷ lệ chính xác</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-20 bg-gradient-to-br from-blue-50 to-indigo-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Khách Hàng Nói Gì Về Chúng Tôi</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Lắng nghe từ những người đã thay đổi sức khỏe của họ với AI HealthBot</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($testimonials ?? [] as $testimonial)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="p-8">
                        <div class="flex items-center mb-6">
                            <div class="bg-blue-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $testimonial['name'] }}</h4>
                                <p class="text-gray-600">{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 italic mb-6">"{{ $testimonial['message'] }}"</p>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial['rating'])
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-gray-500">Chưa có đánh giá nào.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Liên Hệ Với Chúng Tôi</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">Sẵn sàng thay đổi sức khỏe của bạn với các giải pháp được hỗ trợ bởi AI? Liên hệ với chúng tôi ngay hôm nay để đặt lịch tư vấn.</p>
            </div>

            <div class="max-w-3xl mx-auto">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST" class="bg-gray-50 rounded-xl shadow-md p-8 border border-gray-100">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('name') border-red-500 @enderror" 
                                   placeholder="Tên của bạn">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Địa chỉ email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('email') border-red-500 @enderror" 
                                   placeholder="Email của bạn">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="subject" class="block text-gray-700 font-medium mb-2">Chủ đề <span class="text-red-500">*</span></label>
                        <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('subject') border-red-500 @enderror" 
                               placeholder="Chúng tôi có thể giúp gì cho bạn?">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-gray-700 font-medium mb-2">Tin nhắn <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="5" required
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-200 @error('message') border-red-500 @enderror" 
                                  placeholder="Hãy cho chúng tôi biết về mục tiêu sức khỏe của bạn...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200">Gửi tin nhắn</button>
                    </div>
                </form>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Điện thoại</h3>
                    <p class="text-gray-600">(123) 456-7890</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Email</h3>
                    <p class="text-gray-600">info@aihealthbot.com</p>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Địa chỉ</h3>
                    <p class="text-gray-600">123 Đường Sức Khỏe, Thành phố Sức Khỏe, HC 10001</p>
                </div>
            </div>
        </div>
    </section>
@endsection