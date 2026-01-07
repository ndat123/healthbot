@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex justify-center">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 w-16 h-16 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Tạo tài khoản của bạn
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Hoặc
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    đăng nhập vào tài khoản hiện có
                </a>
            </p>
        </div>
        <form class="mt-8 space-y-6 bg-white rounded-xl shadow-lg p-8 border border-gray-100" action="{{ route('register') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                    <input id="name" name="name" type="text" autocomplete="name" required 
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('name') border-red-500 @enderror" 
                           placeholder="Nhập họ và tên đầy đủ" value="{{ old('name') }}">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ email</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                           placeholder="Nhập địa chỉ email" value="{{ old('email') }}">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại (Tùy chọn)</label>
                    <input id="phone" name="phone" type="tel" autocomplete="tel" 
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('phone') border-red-500 @enderror" 
                           placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required 
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                           placeholder="Xác nhận mật khẩu">
                </div>
            </div>

            <div class="flex items-center">
                <input id="terms" name="terms" type="checkbox" required
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="terms" class="ml-2 block text-sm text-gray-900">
                    Tôi đồng ý với 
                    <a href="#" class="text-blue-600 hover:text-blue-500">Điều khoản và Điều kiện</a>
                    và 
                    <a href="#" class="text-blue-600 hover:text-blue-500">Chính sách Bảo mật</a>
                </label>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-300 group-hover:text-blue-200" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                    </span>
                    Tạo tài khoản
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Đã có tài khoản? 
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Đăng nhập tại đây
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

