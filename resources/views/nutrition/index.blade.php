@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Tư Vấn Dinh Dưỡng</h1>
            <p class="text-xl text-gray-600">Chuyên gia dinh dưỡng AI của chúng tôi tạo kế hoạch bữa ăn cá nhân hóa dựa trên mục tiêu sức khỏe, sở thích ăn uống và nhu cầu dinh dưỡng của bạn.</p>
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

        @if(!$profile)
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-lg">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-yellow-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Cần Có Hồ Sơ Sức Khỏe</h3>
                        <p class="mt-2 text-sm text-yellow-700">Vui lòng tạo hồ sơ sức khỏe trước để nhận kế hoạch dinh dưỡng cá nhân hóa.</p>
                        <a href="{{ route('health-plans.profile') }}" class="mt-3 inline-block bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors text-sm">
                            Tạo Hồ Sơ Sức Khỏe
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Generate Plan Form -->
        @if($profile)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Tạo Kế Hoạch Bữa Ăn Cá Nhân Hóa</h2>
            <form action="{{ route('nutrition.generate') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Thời Gian Kế Hoạch</label>
                        <select id="duration_days" name="duration_days" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="7">7 ngày</option>
                            <option value="14" selected>14 ngày</option>
                            <option value="21">21 ngày</option>
                            <option value="30">30 ngày</option>
                        </select>
                    </div>
                    <div>
                        <label for="dietary_preferences" class="block text-sm font-medium text-gray-700 mb-2">Sở Thích Ăn Uống</label>
                        <input type="text" id="dietary_preferences" name="dietary_preferences"
                               placeholder="Ví dụ: Ăn chay, Low-carb, Địa Trung Hải"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="md:col-span-2">
                        <label for="allergies_restrictions" class="block text-sm font-medium text-gray-700 mb-2">Dị Ứng & Hạn Chế</label>
                        <textarea id="allergies_restrictions" name="allergies_restrictions" rows="3"
                                  placeholder="Liệt kê các dị ứng thực phẩm hoặc hạn chế ăn uống..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-3 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors font-semibold">
                        Tạo Kế Hoạch Bữa Ăn
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Existing Plans -->
        @if($plans->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Kế Hoạch Dinh Dưỡng Của Bạn</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $plan->title }}</h3>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $plan->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                {{ $plan->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm text-gray-600 mb-4">
                            <p><strong>Thời gian:</strong> {{ $plan->duration_days }} ngày</p>
                            <p><strong>Bắt đầu:</strong> {{ $plan->start_date->format('d/m/Y') }}</p>
                            <p><strong>Kết thúc:</strong> {{ $plan->end_date->format('d/m/Y') }}</p>
                            @if($plan->daily_calories)
                                <p><strong>Calo hàng ngày:</strong> {{ $plan->daily_calories }} kcal</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('nutrition.show', $plan->id) }}" 
                               class="flex-1 text-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Xem Kế Hoạch
                            </a>
                            
                            @if(optional(auth()->user()->settings)->selected_nutrition_plan_id == $plan->id)
                                <button disabled class="px-4 py-2 bg-blue-400 text-white rounded-lg cursor-not-allowed flex items-center justify-center" title="Đang sử dụng">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            @else
                                <form action="{{ route('profile.settings.nutrition-plan') }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="nutrition_plan_id" value="{{ $plan->id }}">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center" title="Chọn làm kế hoạch chính">
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
        </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có kế hoạch dinh dưỡng nào</h3>
                <p class="mt-1 text-sm text-gray-500">Tạo kế hoạch bữa ăn cá nhân hóa đầu tiên của bạn để bắt đầu.</p>
            </div>
        @endif
    </div>
</div>

<script>
function deletePlan(planId, planTitle) {
    if (!confirm(`Bạn có chắc chắn muốn xóa kế hoạch dinh dưỡng "${planTitle}"?\n\nHành động này không thể hoàn tác.`)) {
        return;
    }
    
    // Create form for DELETE request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('/nutrition') }}/${planId}`;
    
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

