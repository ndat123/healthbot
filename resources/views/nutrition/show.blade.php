@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('nutrition.index') }}" class="text-green-600 hover:text-green-800 mb-4 inline-block">← Back to Plans</a>
            <h1 class="text-4xl font-bold text-gray-900">{{ $plan->title }}</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Plan Info -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Duration</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $plan->duration_days }} days</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Period</p>
                    <p class="text-lg font-semibold text-gray-800">
                        {{ $plan->start_date->format('M d') }} - {{ $plan->end_date->format('M d, Y') }}
                    </p>
                </div>
                @if($plan->daily_calories)
                <div>
                    <p class="text-sm text-gray-600">Daily Calories</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $plan->daily_calories }} kcal</p>
                </div>
                @endif
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Tiến độ hoàn thành</span>
                    <span class="text-sm font-semibold text-green-600" id="completion-percentage">{{ $plan->completion_percentage ?? 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500 ease-out" 
                         id="progress-bar"
                         style="width: {{ $plan->completion_percentage ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Daily Meal Plans -->
        @if($plan->plan_data && isset($plan->plan_data['daily_meals']))
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <!-- Day Selector Tabs -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                    <button onclick="selectDay('all')" id="day-tab-all" class="day-tab px-4 py-2 rounded-lg font-medium transition-all active">
                        Tất cả các ngày
                    </button>
                    @foreach($plan->plan_data['daily_meals'] as $index => $dayPlan)
                        @php
                            $dayNumber = $dayPlan['day'] ?? ($index + 1);
                            $dayDate = $plan->start_date->copy()->addDays($dayNumber - 1);
                        @endphp
                        <button onclick="selectDay({{ $dayNumber }})" id="day-tab-{{ $dayNumber }}" class="day-tab px-4 py-2 rounded-lg font-medium transition-all">
                            Ngày {{ $dayNumber }}<br>
                            <span class="text-xs text-gray-500">{{ $dayDate->format('d/m') }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Meal Type Filter -->
            <div class="mb-6 flex flex-wrap gap-2">
                <button onclick="filterMeals('all')" id="meal-filter-all" class="meal-filter px-4 py-2 bg-green-600 text-white rounded-lg font-medium transition-all active">
                    Tất cả bữa ăn
                </button>
                <button onclick="filterMeals('breakfast')" id="meal-filter-breakfast" class="meal-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    Bữa sáng
                </button>
                <button onclick="filterMeals('lunch')" id="meal-filter-lunch" class="meal-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    Bữa trưa
                </button>
                <button onclick="filterMeals('dinner')" id="meal-filter-dinner" class="meal-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    Bữa tối
                </button>
                <button onclick="filterMeals('snack')" id="meal-filter-snack" class="meal-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    Bữa phụ
                </button>
            </div>
        </div>

        <div class="space-y-6" id="meals-container">
            @foreach($plan->plan_data['daily_meals'] as $index => $dayPlan)
                @php
                    $dayNumber = $dayPlan['day'] ?? ($index + 1);
                    $dayDate = $plan->start_date->copy()->addDays($dayNumber - 1);
                @endphp
                <div class="day-content bg-white rounded-xl shadow-lg p-8" data-day="{{ $dayNumber }}">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Ngày {{ $dayNumber }}</h2>
                            <p class="text-gray-600 mt-1">{{ $dayDate->format('l, d/m/Y') }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                @if(isset($dayPlan['nutrition']['total_calories']))
                                    <p class="text-sm text-gray-600">Tổng calo</p>
                                    <p class="text-lg font-semibold text-green-600">{{ $dayPlan['nutrition']['total_calories'] ?? 'N/A' }} kcal</p>
                                @endif
                            </div>
                            <button onclick="showAdjustPlanModal({{ $dayNumber }})" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium text-sm shadow-md flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Điều chỉnh kế hoạch
                            </button>
                        </div>
                    </div>

                    <!-- Meals by Type -->
                    @if(isset($dayPlan['meals']))
                    <div class="space-y-6">
                        @php
                            $mealsByType = [
                                'breakfast' => [],
                                'lunch' => [],
                                'dinner' => [],
                                'snack' => [],
                                'other' => []
                            ];
                            
                            $calculatedCalories = 0;
                            $calculatedProtein = 0;
                            $calculatedCarbs = 0;
                            $calculatedFats = 0;
                            $progress = $plan->progress_data['daily_progress'][$dayNumber - 1] ?? null;
                            
                            foreach($dayPlan['meals'] as $mealIndex => $meal) {
                                $time = strtolower($meal['time'] ?? '');
                                $mealType = 'other';
                                
                                if (strpos($time, 'sáng') !== false || strpos($time, 'breakfast') !== false || strpos($time, 'morning') !== false) {
                                    $mealType = 'breakfast';
                                } elseif (strpos($time, 'trưa') !== false || strpos($time, 'lunch') !== false || strpos($time, 'noon') !== false) {
                                    $mealType = 'lunch';
                                } elseif (strpos($time, 'tối') !== false || strpos($time, 'dinner') !== false || strpos($time, 'evening') !== false) {
                                    $mealType = 'dinner';
                                } elseif (strpos($time, 'phụ') !== false || strpos($time, 'snack') !== false) {
                                    $mealType = 'snack';
                                }
                                
                                $mealsByType[$mealType][] = $meal;
                                
                                // Calculate nutrition from selected options or first option
                                $mealKey = 'meal_' . $dayNumber . '_' . $mealIndex;
                                $selectedOption = $plan->progress_data['meal_selections'][$mealKey] ?? 0;
                                
                                if (isset($meal['options']) && is_array($meal['options'])) {
                                    // Meal with options - use selected option or first one
                                    $selectedMeal = $meal['options'][$selectedOption] ?? $meal['options'][0] ?? null;
                                    if ($selectedMeal) {
                                        $calculatedCalories += $selectedMeal['calories'] ?? 0;
                                        $calculatedProtein += $selectedMeal['protein'] ?? 0;
                                        $calculatedCarbs += $selectedMeal['carbs'] ?? 0;
                                        $calculatedFats += $selectedMeal['fat'] ?? 0;
                                    }
                                } else {
                                    // Single meal (backward compatibility)
                                    $calculatedCalories += $meal['calories'] ?? 0;
                                    $calculatedProtein += $meal['protein'] ?? 0;
                                    $calculatedCarbs += $meal['carbs'] ?? 0;
                                    $calculatedFats += $meal['fat'] ?? $meal['fats'] ?? 0;
                                }
                            }
                        @endphp
                        
                        @foreach($dayPlan['meals'] as $mealIndex => $meal)
                            @php
                                $mealKey = 'meal_' . $dayNumber . '_' . $mealIndex;
                                $mealCompleted = $progress['meals'][$mealKey] ?? false;
                                $hasOptions = isset($meal['options']) && is_array($meal['options']);
                                $selectedOption = $plan->progress_data['meal_selections'][$mealKey] ?? 0;
                                
                                // Determine meal type for filtering
                                $time = strtolower($meal['time'] ?? '');
                                $mealType = 'other';
                                if (strpos($time, 'sáng') !== false) $mealType = 'breakfast';
                                elseif (strpos($time, 'trưa') !== false) $mealType = 'lunch';
                                elseif (strpos($time, 'tối') !== false) $mealType = 'dinner';
                                elseif (strpos($time, 'phụ') !== false) $mealType = 'snack';
                            @endphp
                            
                            @if($hasOptions)
                                <!-- Meal with multiple options -->
                                <div class="meal-type-section meal-group bg-green-50 rounded-lg border border-green-200 p-4 mb-4" data-meal-type="{{ $mealType }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                            <input type="checkbox" 
                                                   class="meal-checkbox h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded transition-all"
                                                   data-day="{{ $dayNumber }}"
                                                   data-meal-index="{{ $mealIndex }}"
                                                   data-meal-key="{{ $mealKey }}"
                                                   {{ $mealCompleted ? 'checked' : '' }}
                                                   onchange="toggleMealCompletion(this)">
                                            <span class="{{ $mealCompleted ? 'line-through text-gray-500' : '' }}">{{ $meal['time'] ?? 'Meal' }}</span>
                                        </h4>
                                        <span class="text-xs text-gray-500">Chọn 1 trong {{ count($meal['options']) }} lựa chọn</span>
                                    </div>
                                    
                                    <div class="space-y-2 ml-7">
                                        @foreach($meal['options'] as $optionIndex => $option)
                                            <label class="flex items-center justify-between p-2 rounded-lg border border-green-100 hover:border-green-300 cursor-pointer transition-all {{ $selectedOption == $optionIndex ? 'ring-2 ring-green-400 bg-green-50' : 'bg-white' }}">
                                                <div class="flex items-center gap-3 flex-1">
                                                    <input type="radio" 
                                                           name="meal_option_{{ $mealKey }}" 
                                                           value="{{ $optionIndex }}"
                                                           {{ $selectedOption == $optionIndex ? 'checked' : '' }}
                                                           onchange="saveMealSelection('{{ $mealKey }}', {{ $optionIndex }}, {{ $dayNumber }}, {{ $option['calories'] ?? 0 }}, {{ $option['protein'] ?? 0 }}, {{ $option['carbs'] ?? 0 }}, {{ $option['fat'] ?? 0 }}, {{ $mealIndex }})"
                                                           class="h-4 w-4 text-green-600 focus:ring-green-500"
                                                           data-calories="{{ $option['calories'] ?? 0 }}"
                                                           data-protein="{{ $option['protein'] ?? 0 }}"
                                                           data-carbs="{{ $option['carbs'] ?? 0 }}"
                                                           data-fat="{{ $option['fat'] ?? 0 }}">
                                                    <div class="flex-1">
                                                        <span class="text-sm {{ $selectedOption == $optionIndex ? 'text-green-900 font-semibold' : 'text-gray-700' }}">{{ $option['food'] ?? 'N/A' }}</span>
                                                        @if(isset($option['protein']) || isset($option['carbs']) || isset($option['fat']))
                                                            <div class="flex gap-2 text-xs text-gray-500 mt-1">
                                                                @if(isset($option['protein']))
                                                                    <span>P: {{ $option['protein'] }}g</span>
                                                                @endif
                                                                @if(isset($option['carbs']))
                                                                    <span>C: {{ $option['carbs'] }}g</span>
                                                                @endif
                                                                @if(isset($option['fat']))
                                                                    <span>F: {{ $option['fat'] }}g</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(isset($option['calories']))
                                                    <span class="text-xs text-green-600 font-medium bg-green-100 px-2 py-1 rounded">{{ $option['calories'] }} cal</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <!-- Single meal option (backward compatibility) -->
                                <div class="meal-type-section meal-card border border-gray-200 rounded-lg p-4 mb-4 {{ $mealCompleted ? 'opacity-75' : '' }}" data-meal-type="{{ $mealType }}">
                                    <div class="flex items-center gap-3 mb-2">
                                        <input type="checkbox" 
                                               class="meal-checkbox h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded transition-all"
                                               data-day="{{ $dayNumber }}"
                                               data-meal-index="{{ $mealIndex }}"
                                               data-meal-key="{{ $mealKey }}"
                                               {{ $mealCompleted ? 'checked' : '' }}
                                               onchange="toggleMealCompletion(this)">
                                        <div class="flex-1">
                                            <div class="flex justify-between items-start">
                                                <h4 class="font-semibold text-gray-800 {{ $mealCompleted ? 'line-through text-gray-500' : '' }}">{{ $meal['time'] ?? 'Meal' }}</h4>
                                                @if(isset($meal['calories']))
                                                    <span class="text-sm text-green-600 font-medium">{{ $meal['calories'] }} kcal</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-700 font-medium {{ $mealCompleted ? 'line-through text-gray-500' : '' }}">{{ $meal['food'] ?? 'N/A' }}</p>
                                            @if(isset($meal['protein']) || isset($meal['carbs']) || isset($meal['fat']))
                                                <div class="flex gap-4 text-xs text-gray-500 mt-2">
                                                    @if(isset($meal['protein']))
                                                        <span>Protein: {{ $meal['protein'] }}g</span>
                                                    @endif
                                                    @if(isset($meal['carbs']))
                                                        <span>Carbs: {{ $meal['carbs'] }}g</span>
                                                    @endif
                                                    @if(isset($meal['fat']))
                                                        <span>Fat: {{ $meal['fat'] }}g</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endif

                    <!-- Nutrition Summary -->
                    <div class="bg-green-50 rounded-lg p-4 mt-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Tóm tắt dinh dưỡng ngày</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Tổng calo</p>
                                <p class="font-semibold text-gray-800" id="day-{{ $dayNumber }}-calories">
                                    {{ $calculatedCalories > 0 ? $calculatedCalories : ($dayPlan['nutrition']['total_calories'] ?? 0) }} kcal
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Protein</p>
                                <p class="font-semibold text-gray-800" id="day-{{ $dayNumber }}-protein">
                                    {{ $calculatedProtein > 0 ? round($calculatedProtein) : ($dayPlan['nutrition']['protein'] ?? 0) }}g
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Carbs</p>
                                <p class="font-semibold text-gray-800" id="day-{{ $dayNumber }}-carbs">
                                    {{ $calculatedCarbs > 0 ? round($calculatedCarbs) : ($dayPlan['nutrition']['carbs'] ?? 0) }}g
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Fats</p>
                                <p class="font-semibold text-gray-800" id="day-{{ $dayNumber }}-fats">
                                    {{ $calculatedFats > 0 ? round($calculatedFats) : ($dayPlan['nutrition']['fats'] ?? $dayPlan['nutrition']['fat'] ?? 0) }}g
                                </p>
                            </div>
                        </div>
                        @if(isset($dayPlan['nutrition']['vitamins']))
                            <p class="text-sm text-gray-600 mt-2">{{ $dayPlan['nutrition']['vitamins'] }}</p>
                        @endif
                    </div>

                    <!-- Shopping List -->
                    @if(isset($dayPlan['shopping_list']) && count($dayPlan['shopping_list']) > 0)
                    <div class="border-t pt-4 mt-6">
                        <h3 class="font-semibold text-gray-800 mb-3">Danh sách mua sắm</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($dayPlan['shopping_list'] as $item)
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <!-- Tips -->
        @if($plan->plan_data && isset($plan->plan_data['tips']))
        <div class="bg-white rounded-xl shadow-lg p-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Nutrition Tips</h2>
            <ul class="space-y-2">
                @foreach($plan->plan_data['tips'] as $tip)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-gray-700">{{ $tip }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<!-- Adjust Plan Modal -->
<div id="adjustPlanModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">
                    <svg class="w-6 h-6 inline mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Điều chỉnh kế hoạch dinh dưỡng - Ngày <span id="adjustDayNumber"></span>
                </h3>
                <button onclick="closeAdjustPlanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="adjustPlanForm" class="p-6 space-y-4">
            <input type="hidden" id="adjustDayNumberInput" name="day_number">
            
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                <p class="text-amber-800 font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Nếu bạn không thể theo kế hoạch dinh dưỡng hôm nay, hãy cho chúng tôi biết tình hình thực tế. AI sẽ điều chỉnh lại kế hoạch các ngày còn lại phù hợp hơn.
                </p>
            </div>
            
            <div>
                <label for="adjustReason" class="block text-sm font-medium text-gray-700 mb-1">
                    Lý do không theo được kế hoạch <span class="text-red-500">*</span>
                </label>
                <textarea id="adjustReason" name="reason" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                          placeholder="Ví dụ: Bận công việc, đi công tác, ốm, có sự kiện gia đình..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="adjustWeight" class="block text-sm font-medium text-gray-700 mb-1">Cân nặng hiện tại (kg)</label>
                    <input type="number" id="adjustWeight" name="weight" 
                           step="0.1" min="0" max="500"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label for="adjustMood" class="block text-sm font-medium text-gray-700 mb-1">Tâm trạng</label>
                    <select id="adjustMood" name="mood" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Chọn...</option>
                        <option value="excellent">Tuyệt vời</option>
                        <option value="good">Tốt</option>
                        <option value="okay">Ổn</option>
                        <option value="poor">Không tốt</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Bữa ăn thực tế hôm nay
                    <span class="ml-2 text-xs text-gray-500 font-normal">(Có thể để trống nếu không nhớ)</span>
                </label>
                <div class="space-y-4">
                    <div class="meal-group bg-green-50 rounded-lg border border-green-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Bữa sáng</h4>
                        <textarea name="actual_meals[breakfast]" rows="2"
                                  class="w-full px-3 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white"
                                  placeholder="Mô tả bữa sáng bạn đã ăn..."></textarea>
                    </div>
                    
                    <div class="meal-group bg-green-50 rounded-lg border border-green-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Bữa trưa</h4>
                        <textarea name="actual_meals[lunch]" rows="2"
                                  class="w-full px-3 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white"
                                  placeholder="Mô tả bữa trưa bạn đã ăn..."></textarea>
                    </div>
                    
                    <div class="meal-group bg-green-50 rounded-lg border border-green-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">Bữa tối</h4>
                        <textarea name="actual_meals[dinner]" rows="2"
                                  class="w-full px-3 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white"
                                  placeholder="Mô tả bữa tối bạn đã ăn..."></textarea>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="adjustNotes" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú thêm</label>
                <textarea id="adjustNotes" name="notes" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                          placeholder="Bất kỳ thông tin nào khác..."></textarea>
            </div>
            
            <div id="adjustResultSection" class="hidden mt-4">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Kế hoạch đã được điều chỉnh
                    </h4>
                    <div id="adjustResultContent" class="text-gray-700 whitespace-pre-line"></div>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeAdjustPlanModal(); window.location.reload();"
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        Xem kế hoạch mới
                    </button>
                </div>
            </div>
            
            <div id="adjustSubmitSection" class="flex gap-3 pt-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium">
                    Điều chỉnh kế hoạch
                </button>
                <button type="button" 
                        onclick="closeAdjustPlanModal()" 
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Hủy
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.day-tab.active {
    background-color: #16a34a;
    color: white;
}

.day-tab:not(.active) {
    background-color: #e5e7eb;
    color: #374151;
}

.day-tab:hover:not(.active) {
    background-color: #d1d5db;
}

.meal-filter.active {
    background-color: #16a34a !important;
    color: white !important;
}

.meal-filter:not(.active) {
    background-color: #e5e7eb;
    color: #374151;
}

.meal-filter:hover:not(.active) {
    background-color: #d1d5db;
}

.meal-card {
    transition: all 0.3s ease;
}

.meal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>

<script>
let selectedDay = 'all';
let selectedMealType = 'all';

function selectDay(day) {
    selectedDay = day;
    
    // Update tab styles
    document.querySelectorAll('.day-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-green-600', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeTab = document.getElementById('day-tab-' + day);
    if (activeTab) {
        activeTab.classList.add('active', 'bg-green-600', 'text-white');
        activeTab.classList.remove('bg-gray-200', 'text-gray-700');
    }
    
    // Show/hide day content
    document.querySelectorAll('.day-content').forEach(content => {
        if (day === 'all' || content.dataset.day == day) {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });
    
    // Reapply meal filter
    filterMeals(selectedMealType);
}

function filterMeals(mealType) {
    selectedMealType = mealType;
    
    // Update filter button styles
    document.querySelectorAll('.meal-filter').forEach(btn => {
        btn.classList.remove('active', 'bg-green-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeFilter = document.getElementById('meal-filter-' + mealType);
    if (activeFilter) {
        activeFilter.classList.add('active', 'bg-green-600', 'text-white');
        activeFilter.classList.remove('bg-gray-200', 'text-gray-700');
    }
    
    // Show/hide meal type sections
    document.querySelectorAll('.meal-type-section').forEach(section => {
        if (mealType === 'all' || section.dataset.mealType === mealType) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
    
    // Show/hide meal cards
    document.querySelectorAll('.meal-card').forEach(card => {
        if (mealType === 'all' || card.dataset.mealType === mealType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Initialize: show all days and all meals
document.addEventListener('DOMContentLoaded', function() {
    selectDay('all');
    filterMeals('all');
});

// Adjust plan modal functions
function showAdjustPlanModal(dayNumber) {
    document.getElementById('adjustDayNumberInput').value = dayNumber;
    document.getElementById('adjustDayNumber').textContent = dayNumber;
    document.getElementById('adjustPlanModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset form
    document.getElementById('adjustPlanForm').reset();
    document.getElementById('adjustResultSection').classList.add('hidden');
    document.getElementById('adjustSubmitSection').classList.remove('hidden');
}

function closeAdjustPlanModal() {
    document.getElementById('adjustPlanModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Handle adjust plan form submission
document.getElementById('adjustPlanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Collect meals data
    const meals = [];
    const breakfast = formData.get('actual_meals[breakfast]');
    const lunch = formData.get('actual_meals[lunch]');
    const dinner = formData.get('actual_meals[dinner]');
    
    if (breakfast) meals.push(`Bữa sáng: ${breakfast}`);
    if (lunch) meals.push(`Bữa trưa: ${lunch}`);
    if (dinner) meals.push(`Bữa tối: ${dinner}`);
    
    const data = {
        day_number: parseInt(formData.get('day_number')),
        reason: formData.get('reason'),
        weight: formData.get('weight') ? parseFloat(formData.get('weight')) : null,
        mood: formData.get('mood'),
        actual_meals: meals.length > 0 ? meals.join('\n') : null,
        notes: formData.get('notes')
    };
    
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...';
    
    fetch('{{ route("nutrition.adjust-plan", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            document.getElementById('adjustResultContent').textContent = result.adjustment_summary || 'Kế hoạch dinh dưỡng đã được điều chỉnh thành công!';
            document.getElementById('adjustResultSection').classList.remove('hidden');
            document.getElementById('adjustSubmitSection').classList.add('hidden');
            
            submitButton.disabled = false;
            submitButton.innerHTML = 'Điều chỉnh kế hoạch';
            
            // Auto reload after 2 seconds to show updated plan
            setTimeout(() => {
                document.getElementById('adjustResultContent').innerHTML = 
                    result.adjustment_summary + 
                    '<div class="mt-4 flex items-center justify-center text-purple-600"><svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang tải kế hoạch mới...</div>';
                
                setTimeout(() => {
                    // Force reload with cache busting
                    window.location.href = window.location.href.split('?')[0] + '?t=' + Date.now();
                }, 1500);
            }, 2000);
        } else {
            alert(result.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Điều chỉnh kế hoạch';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
        submitButton.disabled = false;
        submitButton.innerHTML = 'Điều chỉnh kế hoạch';
    });
});

// Meal completion toggle
function toggleMealCompletion(checkbox) {
    const day = checkbox.dataset.day;
    const mealIndex = checkbox.dataset.mealIndex;
    const mealKey = checkbox.dataset.mealKey;
    const completed = checkbox.checked;
    
    fetch('{{ route("nutrition.meal-completion", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            day: parseInt(day),
            meal_index: parseInt(mealIndex),
            meal_key: mealKey,
            completed: completed
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Update progress bar
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('completion-percentage');
            if (progressBar && progressText) {
                progressBar.style.width = result.completion_percentage + '%';
                progressText.textContent = result.completion_percentage + '%';
            }
            
            // Visual feedback
            const mealCard = checkbox.closest('.meal-card, .meal-group');
            if (mealCard) {
                if (completed) {
                    mealCard.classList.add('opacity-75');
                    mealCard.querySelectorAll('span, p, h4').forEach(el => {
                        if (!el.classList.contains('text-xs') && !el.classList.contains('text-sm')) {
                            el.classList.add('line-through', 'text-gray-500');
                        }
                    });
                } else {
                    mealCard.classList.remove('opacity-75');
                    mealCard.querySelectorAll('span, p, h4').forEach(el => {
                        el.classList.remove('line-through', 'text-gray-500');
                    });
                }
            }
        } else {
            checkbox.checked = !completed; // Revert on error
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !completed; // Revert on error
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

// Save meal selection (radio button) and recalculate nutrition
function saveMealSelection(mealKey, optionIndex, dayNumber, calories, protein, carbs, fat, mealIndex) {
    fetch('{{ route("nutrition.meal-selection", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            meal_key: mealKey,
            option_index: parseInt(optionIndex)
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Visual feedback - highlight selected option
            const radio = document.querySelector(`input[name="meal_option_${mealKey}"][value="${optionIndex}"]`);
            if (radio) {
                const label = radio.closest('label');
                // Remove highlight from all options in this group
                label.parentElement.querySelectorAll('label').forEach(l => {
                    l.classList.remove('ring-2', 'ring-green-400', 'bg-green-50');
                    l.classList.add('bg-white');
                    const span = l.querySelector('span');
                    if (span) {
                        span.classList.remove('text-green-900', 'font-semibold');
                        span.classList.add('text-gray-700');
                    }
                });
                // Highlight selected option
                label.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
                label.classList.remove('bg-white');
                const span = label.querySelector('span');
                if (span) {
                    span.classList.add('text-green-900', 'font-semibold');
                    span.classList.remove('text-gray-700');
                }
            }
            
            // Recalculate nutrition for the day
            recalculateDayNutrition(dayNumber);
        } else {
            alert('Có lỗi xảy ra khi lưu lựa chọn.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi lưu lựa chọn.');
    });
}

// Recalculate nutrition summary for a day based on selected options
function recalculateDayNutrition(dayNumber) {
    const dayContent = document.querySelector(`.day-content[data-day="${dayNumber}"]`);
    if (!dayContent) return;
    
    let totalCalories = 0;
    let totalProtein = 0;
    let totalCarbs = 0;
    let totalFats = 0;
    
    // Get all meal options in this day
    const mealGroups = dayContent.querySelectorAll('.meal-group');
    mealGroups.forEach(group => {
        const selectedRadio = group.querySelector('input[type="radio"]:checked');
        if (selectedRadio) {
            const calories = parseFloat(selectedRadio.dataset.calories || 0);
            const protein = parseFloat(selectedRadio.dataset.protein || 0);
            const carbs = parseFloat(selectedRadio.dataset.carbs || 0);
            const fat = parseFloat(selectedRadio.dataset.fat || 0);
            
            totalCalories += calories;
            totalProtein += protein;
            totalCarbs += carbs;
            totalFats += fat;
        }
    });
    
    // Also check single meals (backward compatibility)
    const singleMeals = dayContent.querySelectorAll('.meal-card');
    singleMeals.forEach(mealCard => {
        const checkbox = mealCard.querySelector('.meal-checkbox');
        if (checkbox && !checkbox.checked) {
            // Only count if not completed (or count all, depending on logic)
            // For now, we'll count all single meals
        }
    });
    
    // Update nutrition summary display
    const caloriesEl = document.getElementById(`day-${dayNumber}-calories`);
    const proteinEl = document.getElementById(`day-${dayNumber}-protein`);
    const carbsEl = document.getElementById(`day-${dayNumber}-carbs`);
    const fatsEl = document.getElementById(`day-${dayNumber}-fats`);
    
    if (caloriesEl) caloriesEl.textContent = Math.round(totalCalories) + ' kcal';
    if (proteinEl) proteinEl.textContent = Math.round(totalProtein) + 'g';
    if (carbsEl) carbsEl.textContent = Math.round(totalCarbs) + 'g';
    if (fatsEl) fatsEl.textContent = Math.round(totalFats) + 'g';
}
</script>
@endsection

