@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Kế Hoạch Sức Khỏe Cá Nhân Hóa</h1>
            <p class="text-xl text-gray-600">Tạo kế hoạch sức khỏe tùy chỉnh phù hợp với nhu cầu và mục tiêu riêng của bạn</p>
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

        <!-- Health Profile Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Hồ Sơ Sức Khỏe Của Bạn</h2>
                <a href="{{ route('health-plans.profile') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $profile ? 'Cập Nhật Hồ Sơ' : 'Tạo Hồ Sơ' }}
                </a>
            </div>

            @if($profile)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tuổi</p>
                        <p class="text-lg font-semibold">{{ $profile->age ?? 'Chưa đặt' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Giới tính</p>
                        <p class="text-lg font-semibold">{{ ucfirst($profile->gender ?? 'Chưa đặt') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">BMI</p>
                        <p class="text-lg font-semibold">{{ $profile->bmi ?? 'Chưa tính' }}</p>
                    </div>
                    @if($profile->height && $profile->weight)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Chiều cao</p>
                        <p class="text-lg font-semibold">{{ $profile->height }} cm</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Cân nặng</p>
                        <p class="text-lg font-semibold">{{ $profile->weight }} kg</p>
                    </div>
                    @endif
                    @if($profile->health_goals)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Mục tiêu</p>
                        <p class="text-lg font-semibold">
                            @if(is_array($profile->health_goals))
                                {{ implode(', ', $profile->health_goals) }}
                            @else
                                {{ $profile->health_goals }}
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 mb-4">Bạn chưa tạo hồ sơ sức khỏe.</p>
                    <a href="{{ route('health-plans.profile') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Tạo Hồ Sơ Của Bạn
                    </a>
                </div>
            @endif
        </div>

        <!-- Generate Plan Section -->
        @if($profile)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tạo Kế Hoạch Sức Khỏe Mới</h2>
            <form action="{{ route('health-plans.generate') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Thời Gian Kế Hoạch (7-30 ngày)
                    </label>
                    <input type="number" 
                           id="duration_days" 
                           name="duration_days" 
                           value="7" 
                           min="7" 
                           max="30" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-semibold">
                    Tạo Kế Hoạch
                </button>
            </form>
        </div>
        @endif

        <!-- Existing Plans -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kế Hoạch Sức Khỏe Của Bạn</h2>
            
            @if($plans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($plans as $plan)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $plan->title }}</h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $plan->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $plan->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $plan->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Thời gian:</span> {{ $plan->duration_days }} ngày
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Bắt đầu:</span> {{ $plan->start_date->format('d/m/Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Kết thúc:</span> {{ $plan->end_date->format('d/m/Y') }}
                                </p>
                                <div class="mt-3">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Tiến độ</span>
                                        <span class="font-semibold">{{ $plan->completion_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $plan->completion_percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('health-plans.show', $plan->id) }}" class="flex-1 text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Xem Kế Hoạch
                                </a>

                                @if(optional(auth()->user()->settings)->selected_health_plan_id == $plan->id)
                                    <button disabled class="px-4 py-2 bg-green-100 text-green-600 rounded-lg border border-green-200 cursor-default" title="Đang được chọn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                @else
                                    <form action="{{ route('profile.settings.health-plan') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="health_plan_id" value="{{ $plan->id }}">
                                        <button type="submit" class="px-4 py-2 bg-white text-gray-400 border border-gray-200 rounded-lg hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-colors" title="Chọn làm kế hoạch chính">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                <button onclick="deletePlan({{ $plan->id }}, '{{ addslashes($plan->title) }}')" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center"
                                        title="Xóa kế hoạch">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 mb-4">Bạn chưa có kế hoạch sức khỏe nào.</p>
                    @if($profile)
                        <p class="text-sm text-gray-500">Tạo kế hoạch sức khỏe cá nhân hóa đầu tiên của bạn ở trên!</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deletePlan(planId, planTitle) {
    if (!confirm(`Bạn có chắc chắn muốn xóa kế hoạch "${planTitle}"?\n\nHành động này không thể hoàn tác.`)) {
        return;
    }
    
    // Create form for DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('/health-plans') }}/${planId}`;
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add method spoofing for DELETE
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    // Submit form
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

