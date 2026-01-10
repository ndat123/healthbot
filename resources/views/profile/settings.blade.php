@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Lưu Trữ</h1>
            <p class="text-xl text-gray-600">Quản lý các kế hoạch và dữ liệu đã lưu</p>
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

        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kế Hoạch Của Bạn</h2>
            
            <!-- Health Plans -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kế Hoạch Sức Khỏe</h3>
                <div class="bg-green-50 border border-green-100 rounded-lg p-4">
                    @if(optional($settings)->selectedHealthPlan)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-green-900 font-medium text-lg">{{ $settings->selectedHealthPlan->title }}</p>
                                <p class="text-sm text-green-700 mt-1">
                                    {{ $settings->selectedHealthPlan->duration_days }} ngày | 
                                    {{ \Carbon\Carbon::parse($settings->selectedHealthPlan->start_date)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($settings->selectedHealthPlan->end_date)->format('d/m/Y') }}
                                </p>
                            </div>
                            <a href="{{ route('health-plans.show', $settings->selected_health_plan_id) }}" class="bg-white text-green-600 px-4 py-2 rounded border border-green-200 hover:bg-green-50 transition-colors text-sm font-medium">
                                Xem Chi Tiết
                            </a>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <p class="text-gray-500 italic mb-2">Chưa chọn kế hoạch sức khỏe nào làm mặc định.</p>
                            <a href="{{ route('health-plans.index') }}" class="text-green-600 hover:text-green-800 font-medium">
                                Chọn kế hoạch ngay &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Nutrition Plans -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kế Hoạch Dinh Dưỡng</h3>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                    @if(optional($settings)->selectedNutritionPlan)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-blue-900 font-medium text-lg">{{ $settings->selectedNutritionPlan->title }}</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    {{ $settings->selectedNutritionPlan->duration_days }} ngày | 
                                    {{ number_format($settings->selectedNutritionPlan->daily_calories) }} kcal/ngày
                                </p>
                            </div>
                            <a href="{{ route('nutrition.show', $settings->selected_nutrition_plan_id) }}" class="bg-white text-blue-600 px-4 py-2 rounded border border-blue-200 hover:bg-blue-50 transition-colors text-sm font-medium">
                                Xem Chi Tiết
                            </a>
                        </div>
                    @else
                        <div class="text-center py-2">
                            <p class="text-gray-500 italic mb-2">Chưa chọn kế hoạch dinh dưỡng nào làm mặc định.</p>
                            <a href="{{ route('nutrition.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Chọn kế hoạch ngay &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reminders -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Nhắc Nhở Của Bạn</h3>
                    <a href="{{ route('health-tracking.index') }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        + Thêm Nhắc Nhở
                    </a>
                </div>
                
                @if($reminders->count() > 0)
                    <div class="space-y-3">
                        @foreach($reminders as $reminder)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $reminder->title }}</h3>
                                        <p class="text-sm text-purple-600 font-medium mt-1">
                                            ⏰ {{ \Carbon\Carbon::parse($reminder->reminder_time)->format('H:i') }}
                                        </p>
                                    </div>
                                    <form action="{{ route('health-tracking.reminder.update', $reminder->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_active" value="{{ $reminder->is_active ? '0' : '1' }}">
                                        <button type="submit" class="text-sm px-3 py-1 rounded {{ $reminder->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} font-medium">
                                            {{ $reminder->is_active ? 'Đang bật' : 'Đã tắt' }}
                                        </button>
                                    </form>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="inline-block px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                        {{ ucfirst($reminder->reminder_type) }}
                                    </span>
                                    <form action="{{ route('health-tracking.reminder.delete', $reminder->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhắc nhở này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 bg-gray-50 rounded-lg border border-gray-100 dashed">
                        <p class="text-gray-500 italic mb-2">Chưa có nhắc nhở nào được tạo.</p>
                        <a href="{{ route('health-tracking.index') }}" class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                            Tạo nhắc nhở ngay &rarr;
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="mt-6">
                <a href="{{ route('profile.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    &larr; Quay lại hồ sơ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
