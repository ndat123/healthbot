@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Hồ Sơ Của Tôi</h1>
            <p class="text-xl text-gray-600">Quản lý thông tin tài khoản và cài đặt của bạn</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Thông Tin Cơ Bản</h2>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="inline-block relative">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                                @else
                                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-500">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </label>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="handleAvatarUpload(this)">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Họ và tên</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Ngày sinh</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Giới tính</label>
                                <select id="gender" name="gender"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Chọn...</option>
                                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                                <textarea id="address" name="address" rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Cập nhật hồ sơ
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Đổi Mật Khẩu</h2>
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu hiện tại</label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Mật khẩu mới</label>
                                <input type="password" id="password" name="password" required minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Xác nhận mật khẩu mới</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Cập nhật mật khẩu
                            </button>
                        </div>
                    </form>
                </div>


            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Thông Tin Tài Khoản</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Thành viên từ</p>
                            <p class="font-semibold text-gray-800">{{ $user->created_at->format('M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Đăng nhập lần cuối</p>
                            <p class="font-semibold text-gray-800">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Chưa bao giờ' }}</p>
                        </div>
                        @if($user->role)
                        <div>
                            <p class="text-gray-600">Vai trò</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($user->role) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Liên Kết Nhanh</h3>
                    <div class="space-y-2">
                        <a href="{{ route('health-plans.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Kế hoạch sức khỏe</a>
                        <a href="{{ route('health-journal.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Nhật ký sức khỏe</a>
                        <a href="{{ route('health-tracking.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Theo dõi sức khỏe</a>
                        <a href="{{ route('nutrition.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Kế hoạch dinh dưỡng</a>
                        <a href="{{ route('ai-consultation.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Tư vấn AI</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function handleAvatarUpload(input) {
    if (input.files && input.files[0]) {
        // Show loading state
        const form = input.closest('form');
        const avatarContainer = input.closest('.inline-block');
        
        // Disable form during upload
        form.style.opacity = '0.6';
        form.style.pointerEvents = 'none';
        
        // Show upload indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center z-10';
        loadingIndicator.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>';
        avatarContainer.appendChild(loadingIndicator);
        
        // Submit form
        form.submit();
    }
}

// Show success message if upload was successful
@if(session('success'))
    setTimeout(() => {
        const successMsg = document.createElement('div');
        successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        successMsg.innerHTML = '{{ session("success") }}';
        document.body.appendChild(successMsg);
        
        setTimeout(() => {
            successMsg.remove();
        }, 3000);
    }, 100);
@endif
</script>
@endsection

