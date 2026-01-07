@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Plan Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $plan->title }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>Duration: {{ $plan->duration_days }} days</span>
                        <span>•</span>
                        <span>Start: {{ $plan->start_date->format('M d, Y') }}</span>
                        <span>•</span>
                        <span>End: {{ $plan->end_date->format('M d, Y') }}</span>
                        <span>•</span>
                        <span class="px-2 py-1 rounded-full 
                            {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $plan->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $plan->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                            {{ ucfirst($plan->status) }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('health-plans.status', $plan->id) }}" method="POST" class="inline">
                        @csrf
                        <select name="status" onchange="this.form.submit()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="active" {{ $plan->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ $plan->status === 'paused' ? 'selected' : '' }}>Pause</option>
                            <option value="completed" {{ $plan->status === 'completed' ? 'selected' : '' }}>Complete</option>
                            <option value="cancelled" {{ $plan->status === 'cancelled' ? 'selected' : '' }}>Cancel</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-600 font-medium">Overall Progress</span>
                    <span class="font-semibold text-blue-600">{{ $plan->completion_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $plan->completion_percentage }}%"></div>
                </div>
            </div>

            <div class="text-sm text-gray-600">
                <span>Days Remaining: <strong>{{ $plan->getDaysRemaining() }}</strong></span>
            </div>
        </div>

        <!-- Daily Plans -->
        @if($plan->plan_data && isset($plan->plan_data['daily_plans']))
        <!-- Day Selector and Filter -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <!-- Day Selector Tabs -->
            <div class="mb-6">
                <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                    <button onclick="selectDay('all')" id="day-tab-all" class="day-tab px-4 py-2 rounded-lg font-medium transition-all active">
                        Tất cả các ngày
                    </button>
                    @foreach($plan->plan_data['daily_plans'] as $index => $dayPlan)
                        @php
                            $dayNumber = $dayPlan['day'] ?? ($index + 1);
                            $dayDate = $plan->start_date->copy()->addDays($dayNumber - 1);
                            $progress = $plan->progress_data['daily_progress'][$dayNumber - 1] ?? null;
                            $isCompleted = $progress['completed'] ?? false;
                        @endphp
                        <button onclick="selectDay({{ $dayNumber }})" id="day-tab-{{ $dayNumber }}" class="day-tab px-4 py-2 rounded-lg font-medium transition-all {{ $isCompleted ? 'bg-green-50' : '' }}">
                            Ngày {{ $dayNumber }}
                            @if($isCompleted)
                                <span class="text-xs text-green-600 block">✓</span>
                            @endif
                            <span class="text-xs text-gray-500 block">{{ $dayDate->format('d/m') }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Activity Type Filter -->
            <div class="mb-4 flex flex-wrap gap-2">
                <button onclick="filterActivities('all')" id="activity-filter-all" class="activity-filter px-4 py-2 bg-blue-600 text-white rounded-lg font-medium transition-all active">
                    Tất cả hoạt động
                </button>
                <button onclick="filterActivities('meals')" id="activity-filter-meals" class="activity-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    🍽️ Bữa ăn
                </button>
                <button onclick="filterActivities('exercises')" id="activity-filter-exercises" class="activity-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    💪 Tập luyện
                </button>
                <button onclick="filterActivities('lifestyle')" id="activity-filter-lifestyle" class="activity-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    🌱 Lối sống
                </button>
                <button onclick="filterActivities('notes')" id="activity-filter-notes" class="activity-filter px-4 py-2 bg-gray-200 text-gray-700 rounded-lg font-medium transition-all hover:bg-gray-300">
                    📝 Ghi chú
                </button>
            </div>
        </div>

        <div class="space-y-6" id="plans-container">
            @php
                $currentWeek = 0;
                $weekStartDay = 1;
            @endphp
            @foreach($plan->plan_data['daily_plans'] as $dayPlan)
                @php
                    $dayNumber = $dayPlan['day'] ?? $loop->iteration;
                    $dayDate = $plan->start_date->copy()->addDays($dayNumber - 1);
                    $progress = $plan->progress_data['daily_progress'][$dayNumber - 1] ?? null;
                    $isCompleted = $progress['completed'] ?? false;
                    $isToday = $dayNumber == $plan->start_date->diffInDays(now()) + 1;
                    
                    // Calculate week number
                    $weekNumber = ceil($dayNumber / 7);
                    $weekStartDay = ($weekNumber - 1) * 7 + 1;
                    $weekEndDay = min($weekNumber * 7, $plan->duration_days);
                    
                    // Check if this is the first day of a new week
                    $isFirstDayOfWeek = ($dayNumber - 1) % 7 == 0;
                    
                    // Check if all days in this week are completed
                    $allWeekDaysCompleted = true;
                    $weekProcessed = false;
                    if ($isFirstDayOfWeek) {
                        for ($d = $weekStartDay; $d <= $weekEndDay; $d++) {
                            $dayIdx = $d - 1;
                            $dayProgress = $plan->progress_data['daily_progress'][$dayIdx] ?? null;
                            if (!isset($dayProgress['completed']) || !$dayProgress['completed']) {
                                $allWeekDaysCompleted = false;
                                break;
                            }
                        }
                        // Check if week has been processed
                        $weekProcessed = isset($plan->progress_data['weekly_progress'][$weekNumber]['processed']) && 
                                       $plan->progress_data['weekly_progress'][$weekNumber]['processed'];
                    }
                @endphp
                
                @if($isFirstDayOfWeek && $allWeekDaysCompleted && !$weekProcessed)
                <!-- Week Completion Button -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-xl shadow-lg p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">🎉 Tuần {{ $weekNumber }} đã hoàn thành!</h3>
                            <p class="text-gray-700">Bạn đã hoàn thành tất cả {{ $weekEndDay - $weekStartDay + 1 }} ngày (Ngày {{ $weekStartDay }} - {{ $weekEndDay }})</p>
                        </div>
                        <button onclick="showWeekCompletionModal({{ $weekNumber }})" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold shadow-md">
                            ✓ Đã hoàn thành tất cả
                        </button>
                    </div>
                </div>
                @endif
                
                <div class="day-content bg-white rounded-xl shadow-lg p-6 {{ $isToday ? 'ring-2 ring-blue-500' : '' }}" data-day="{{ $dayNumber }}">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Ngày {{ $dayNumber }}</h2>
                            <p class="text-gray-600 mt-1">{{ $dayDate->format('l, d/m/Y') }}</p>
                            @if(isset($progress['adjustment_reason']))
                                <p class="text-sm text-amber-600 mt-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Đã điều chỉnh: {{ $progress['adjustment_reason'] }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            @if($isCompleted)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">✓ Hoàn thành</span>
                            @else
                                <button onclick="completeAllActivities({{ $dayNumber }})" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm shadow-md flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Hoàn thành tất cả
                                </button>
                            @endif
                            @if($dayNumber < $plan->duration_days)
                                <button onclick="showAdjustPlanModal({{ $dayNumber }})" 
                                        class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors font-medium text-sm shadow-md flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Điều chỉnh kế hoạch
                                </button>
                            @endif
                            @if($isToday && !$isCompleted)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Hôm nay</span>
                            @endif
                        </div>
                    </div>

                    <!-- Meals -->
                    @if(isset($dayPlan['meals']))
                    <div class="activity-section mb-6" data-activity-type="meals">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Bữa ăn
                        </h3>
                        <div class="space-y-4">
                            @foreach($dayPlan['meals'] as $mealIndex => $meal)
                                @php
                                    $mealKey = 'meal_' . $dayNumber . '_' . $mealIndex;
                                    $mealCompleted = $progress['meals'][$mealKey] ?? false;
                                    $hasOptions = isset($meal['options']) && is_array($meal['options']);
                                    $selectedOption = $progress['meal_selections'][$mealKey] ?? 0;
                                @endphp
                                
                                @if($hasOptions)
                                    <!-- Meal with multiple options -->
                                    <div class="meal-group bg-orange-50 rounded-lg border border-orange-200 p-4" data-activity-type="meals">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                                                <input type="checkbox" 
                                                       class="meal-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all"
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
                                                <label class="flex items-center justify-between p-2 rounded-lg border border-orange-100 hover:border-orange-300 cursor-pointer transition-all {{ $selectedOption == $optionIndex ? 'ring-2 ring-orange-400 bg-orange-50' : 'bg-white' }}" 
                                                       data-option-index="{{ $optionIndex }}">
                                                    <div class="flex items-center gap-3 flex-1">
                                                        <input type="radio" 
                                                               name="meal_option_{{ $mealKey }}" 
                                                               value="{{ $optionIndex }}"
                                                               {{ $selectedOption == $optionIndex ? 'checked' : '' }}
                                                               onchange="saveMealSelection('{{ $mealKey }}', {{ $optionIndex }})"
                                                               class="h-4 w-4 text-orange-600 focus:ring-orange-500">
                                                        <span class="text-sm {{ $selectedOption == $optionIndex ? 'text-orange-900 font-semibold' : 'text-gray-700' }}">{{ $option['food'] ?? 'N/A' }}</span>
                                                    </div>
                                                    @if(isset($option['calories']))
                                                        <span class="text-xs text-orange-600 font-medium bg-orange-100 px-2 py-1 rounded">{{ $option['calories'] }} cal</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <!-- Single meal option (backward compatibility) -->
                                    <div class="activity-card flex justify-between items-center p-3 bg-orange-50 rounded-lg border border-orange-200 hover:shadow-md transition-shadow {{ $mealCompleted ? 'opacity-75' : '' }}" data-activity-type="meals">
                                        <div class="flex items-center gap-3 flex-1">
                                            <label class="flex items-center cursor-pointer">
                                                <input type="checkbox" 
                                                       class="meal-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all"
                                                       data-day="{{ $dayNumber }}"
                                                       data-meal-index="{{ $mealIndex }}"
                                                       data-meal-key="{{ $mealKey }}"
                                                       {{ $mealCompleted ? 'checked' : '' }}
                                                       onchange="toggleMealCompletion(this)">
                                                <span class="ml-3 {{ $mealCompleted ? 'line-through text-gray-500' : 'text-gray-800' }}">
                                                    <span class="font-medium">{{ $meal['time'] ?? 'Meal' }}:</span>
                                                    <span class="ml-2 font-medium">{{ $meal['food'] ?? 'N/A' }}</span>
                                                </span>
                                            </label>
                                        </div>
                                        @if(isset($meal['calories']))
                                            <span class="text-sm text-orange-600 font-medium bg-orange-100 px-2 py-1 rounded">{{ $meal['calories'] }} cal</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Exercises -->
                    @if(isset($dayPlan['exercises']))
                    <div class="activity-section mb-6" data-activity-type="exercises">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Tập luyện
                        </h3>
                        <div class="space-y-2">
                            @foreach($dayPlan['exercises'] as $exerciseIndex => $exercise)
                                @php
                                    $exerciseKey = 'exercise_' . $dayNumber . '_' . $exerciseIndex;
                                    $exerciseCompleted = $progress['exercises'][$exerciseKey] ?? false;
                                @endphp
                                <div class="activity-card flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200 hover:shadow-md transition-shadow {{ $exerciseCompleted ? 'opacity-75' : '' }}" data-activity-type="exercises">
                                    <div class="flex items-center gap-3 flex-1">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="exercise-checkbox h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all"
                                                   data-day="{{ $dayNumber }}"
                                                   data-exercise-index="{{ $exerciseIndex }}"
                                                   data-exercise-key="{{ $exerciseKey }}"
                                                   {{ $exerciseCompleted ? 'checked' : '' }}
                                                   onchange="toggleExerciseCompletion(this)">
                                            <span class="ml-3 {{ $exerciseCompleted ? 'line-through text-gray-500' : 'text-gray-800' }}">
                                                <span class="font-medium">{{ $exercise['type'] ?? 'Exercise' }}:</span>
                                                <span class="ml-2 font-medium">{{ $exercise['name'] ?? 'N/A' }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    @if(isset($exercise['duration']))
                                        <span class="text-sm text-blue-600 font-medium bg-blue-100 px-2 py-1 rounded">{{ $exercise['duration'] }} phút</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Lifestyle -->
                    @if(isset($dayPlan['lifestyle']))
                    <div class="activity-section mb-6" data-activity-type="lifestyle">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Lối sống
                        </h3>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200" data-activity-type="lifestyle">
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                @foreach($dayPlan['lifestyle'] as $item)
                                    <li class="activity-card">{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if(isset($dayPlan['notes']))
                    <div class="activity-section mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200" data-activity-type="notes">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Ghi chú
                        </h3>
                        <p class="text-sm text-gray-700 activity-card">{{ $dayPlan['notes'] }}</p>
                    </div>
                    @endif

                    <!-- Weekly AI Advice Display -->
                    @php
                        $weekNumber = ceil($dayNumber / 7);
                        $weeklyProgress = $plan->progress_data['weekly_progress'][$weekNumber] ?? null;
                    @endphp
                    @if($weeklyProgress && isset($weeklyProgress['ai_advice']))
                    <div class="border-t pt-4 mt-4 mb-4">
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                Tư vấn từ AI - Tuần {{ $weekNumber }}
                            </h4>
                            <p class="text-gray-700 whitespace-pre-line">{{ $weeklyProgress['ai_advice'] }}</p>
                            @if(isset($weeklyProgress['ai_advice_generated_at']))
                                <p class="text-xs text-gray-500 mt-2">Được tạo: {{ \Carbon\Carbon::parse($weeklyProgress['ai_advice_generated_at'])->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Progress Update Form -->
                    <div class="border-t pt-4 mt-4">
                        <form action="{{ route('health-plans.progress', $plan->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="day" value="{{ $dayNumber }}">
                            
                            <div class="flex items-center gap-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="completed" value="1" 
                                           {{ $isCompleted ? 'checked' : '' }}
                                           onchange="this.form.querySelector('button[type=submit]').disabled = false"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-gray-700">Mark as completed</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="weight_{{ $dayNumber }}" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                    <input type="number" id="weight_{{ $dayNumber }}" name="weight" 
                                           value="{{ $progress['weight'] ?? '' }}" 
                                           step="0.1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="mood_{{ $dayNumber }}" class="block text-sm font-medium text-gray-700 mb-1">Mood</label>
                                    <select id="mood_{{ $dayNumber }}" name="mood" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select...</option>
                                        <option value="excellent" {{ ($progress['mood'] ?? '') === 'excellent' ? 'selected' : '' }}>Excellent</option>
                                        <option value="good" {{ ($progress['mood'] ?? '') === 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="okay" {{ ($progress['mood'] ?? '') === 'okay' ? 'selected' : '' }}>Okay</option>
                                        <option value="poor" {{ ($progress['mood'] ?? '') === 'poor' ? 'selected' : '' }}>Poor</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="notes_{{ $dayNumber }}" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea id="notes_{{ $dayNumber }}" name="notes" rows="2" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                          placeholder="Add your notes for this day...">{{ $progress['notes'] ?? '' }}</textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $isCompleted ? '' : 'disabled' }}>
                                Update Progress
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Overall Recommendations -->
        @if($plan->plan_data && isset($plan->plan_data['overall_recommendations']))
        <div class="bg-white rounded-xl shadow-lg p-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Overall Recommendations</h2>
            <ul class="space-y-2">
                @foreach($plan->plan_data['overall_recommendations'] as $recommendation)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">{{ $recommendation }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Milestones -->
        @if($plan->plan_data && isset($plan->plan_data['milestones']))
        <div class="bg-white rounded-xl shadow-lg p-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Milestones</h2>
            <div class="space-y-3">
                @foreach($plan->plan_data['milestones'] as $milestone)
                    <div class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                            {{ $milestone['day'] ?? '' }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Day {{ $milestone['day'] ?? '' }}</p>
                            <p class="text-gray-600">{{ $milestone['goal'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('health-plans.index') }}" class="inline-block px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                ← Back to Plans
            </a>
        </div>
    </div>
</div>

<!-- Adjust Plan Modal -->
<div id="adjustPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">
                    <svg class="w-6 h-6 inline mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Điều chỉnh kế hoạch - Ngày <span id="adjustDayNumber"></span>
                </h3>
                <button onclick="closeAdjustPlanModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
                    Nếu bạn không thể theo kế hoạch hôm nay, hãy cho chúng tôi biết tình hình thực tế. AI sẽ điều chỉnh lại kế hoạch các ngày còn lại phù hợp hơn.
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
                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Bữa ăn thực tế hôm nay
                    <span class="ml-2 text-xs text-gray-500 font-normal">(Có thể để trống nếu không nhớ)</span>
                </label>
                <div class="space-y-4">
                    <!-- Bữa sáng -->
                    <div class="meal-group bg-orange-50 rounded-lg border border-orange-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="">Bữa sáng</span>
                        </h4>
                        <textarea name="actual_meals[breakfast]" rows="2"
                                  class="w-full px-3 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white"
                                  placeholder="Mô tả bữa sáng bạn đã ăn..."></textarea>
                    </div>
                    
                    <!-- Bữa trưa -->
                    <div class="meal-group bg-orange-50 rounded-lg border border-orange-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="">Bữa trưa</span>
                        </h4>
                        <textarea name="actual_meals[lunch]" rows="2"
                                  class="w-full px-3 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white"
                                  placeholder="Mô tả bữa trưa bạn đã ăn..."></textarea>
                    </div>
                    
                    <!-- Bữa tối -->
                    <div class="meal-group bg-orange-50 rounded-lg border border-orange-200 p-4">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="">Bữa tối</span>
                        </h4>
                        <textarea name="actual_meals[dinner]" rows="2"
                                  class="w-full px-3 py-2 border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white"
                                  placeholder="Mô tả bữa tối bạn đã ăn..."></textarea>
                    </div>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Tập luyện thực tế hôm nay
                    <span class="ml-2 text-xs text-gray-500 font-normal">(Có thể để trống nếu không có)</span>
                </label>
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-4 space-y-4">
                    <div>
                        <label for="adjustExerciseType" class="block text-sm font-medium text-gray-700 mb-1">Loại tập luyện</label>
                        <input type="text" id="adjustExerciseType" name="actual_exercises[type]"
                               class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                               placeholder="Ví dụ: Cardio, Sức mạnh, Yoga...">
                    </div>
                    <div>
                        <label for="adjustExerciseName" class="block text-sm font-medium text-gray-700 mb-1">Tên bài tập</label>
                        <input type="text" id="adjustExerciseName" name="actual_exercises[name]"
                               class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                               placeholder="Ví dụ: Đi bộ, Chạy bộ, Tập tạ...">
                    </div>
                    <div>
                        <label for="adjustExerciseDuration" class="block text-sm font-medium text-gray-700 mb-1">Thời gian (phút)</label>
                        <input type="number" id="adjustExerciseDuration" name="actual_exercises[duration]" min="0"
                               class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                               placeholder="Ví dụ: 30, 45, 60...">
                    </div>
                    <div>
                        <label for="adjustExerciseNotes" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú (tùy chọn)</label>
                        <textarea id="adjustExerciseNotes" name="actual_exercises[notes]" rows="2"
                                  class="w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white"
                                  placeholder="Mô tả thêm về buổi tập..."></textarea>
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

<!-- Week Completion Modal -->
<div id="weekCompletionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">🎉 Chúc mừng! Bạn đã hoàn thành Tuần <span id="modalWeekNumber"></span></h3>
                <button onclick="closeWeekCompletionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="weekCompletionForm" class="p-6 space-y-4">
            <input type="hidden" id="modalWeekNumberInput" name="week_number">
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <p class="text-blue-800 font-medium">Vui lòng cập nhật tình hình sức khỏe của bạn sau tuần này để nhận tư vấn từ AI:</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="modalWeight" class="block text-sm font-medium text-gray-700 mb-1">Cân nặng (kg)</label>
                    <input type="number" id="modalWeight" name="weight" 
                           step="0.1" min="0" max="500"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="modalMood" class="block text-sm font-medium text-gray-700 mb-1">Tâm trạng</label>
                    <select id="modalMood" name="mood" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Chọn...</option>
                        <option value="excellent">Tuyệt vời</option>
                        <option value="good">Tốt</option>
                        <option value="okay">Ổn</option>
                        <option value="poor">Không tốt</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label for="modalNotes" class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                <textarea id="modalNotes" name="notes" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="Ghi chú về ngày hôm nay..."></textarea>
            </div>
            
            <div id="aiAdviceSection" class="hidden mt-4">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Tư vấn từ AI
                    </h4>
                    <div id="aiAdviceContent" class="text-gray-700 whitespace-pre-line"></div>
                </div>
                
                <!-- Action Buttons After AI Advice -->
                <div id="actionButtonsSection" class="hidden border-t pt-4">
                    <p class="text-gray-700 font-medium mb-3">Bạn muốn làm gì tiếp theo?</p>
                    <div class="flex gap-3">
                        <a href="{{ route('health-plans.index') }}" 
                           class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-center">
                            ✨ Tạo kế hoạch mới
                        </a>
                        <button type="button" 
                                onclick="closeWeekCompletionModal()"
                                class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Tiếp tục
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="submitButtonSection" class="flex gap-3 pt-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Cập nhật & Nhận tư vấn
                </button>
                <button type="button" 
                        onclick="closeWeekCompletionModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Đóng
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.day-tab.active {
    background-color: #2563eb;
    color: white;
}

.day-tab:not(.active) {
    background-color: #e5e7eb;
    color: #374151;
}

.day-tab:hover:not(.active) {
    background-color: #d1d5db;
}

.activity-filter.active {
    background-color: #2563eb !important;
    color: white !important;
}

.activity-filter:not(.active) {
    background-color: #e5e7eb;
    color: #374151;
}

.activity-filter:hover:not(.active) {
    background-color: #d1d5db;
}

.activity-card {
    transition: all 0.3s ease;
}

.activity-card:hover {
    transform: translateY(-2px);
}

.meal-checkbox {
    cursor: pointer;
    accent-color: #2563eb;
}

.meal-checkbox:checked {
    background-color: #2563eb;
    border-color: #2563eb;
}

.activity-card.opacity-75 {
    background-color: #fef3c7;
    border-color: #fbbf24;
}

.meal-group {
    transition: all 0.3s ease;
}

.meal-group:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.meal-group label:hover {
    background-color: #fff7ed;
    border-color: #fb923c;
}

.meal-group label.ring-2 {
    background-color: #fff7ed !important;
    border-color: #fb923c !important;
}

.meal-group input[type="radio"]:checked ~ span {
    font-weight: 600;
    color: #ea580c;
}
</style>

<script>
let selectedDay = 'all';
let selectedActivityType = 'all';

function selectDay(day) {
    selectedDay = day;
    
    // Update tab styles
    document.querySelectorAll('.day-tab').forEach(tab => {
        tab.classList.remove('active', 'bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeTab = document.getElementById('day-tab-' + day);
    if (activeTab) {
        activeTab.classList.add('active', 'bg-blue-600', 'text-white');
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
    
    // Reapply activity filter
    filterActivities(selectedActivityType);
}

function filterActivities(activityType) {
    selectedActivityType = activityType;
    
    // Update filter button styles
    document.querySelectorAll('.activity-filter').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeFilter = document.getElementById('activity-filter-' + activityType);
    if (activeFilter) {
        activeFilter.classList.add('active', 'bg-blue-600', 'text-white');
        activeFilter.classList.remove('bg-gray-200', 'text-gray-700');
    }
    
    // Show/hide activity sections based on filter
    document.querySelectorAll('.activity-section').forEach(section => {
        const sectionType = section.dataset.activityType;
        if (activityType === 'all' || sectionType === activityType) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
}

// Initialize: show all days and all activities
document.addEventListener('DOMContentLoaded', function() {
    selectDay('all');
    filterActivities('all');
});

// Toggle meal completion
function toggleMealCompletion(checkbox) {
    const day = checkbox.dataset.day;
    const mealIndex = checkbox.dataset.mealIndex;
    const mealKey = checkbox.dataset.mealKey;
    const isCompleted = checkbox.checked;
    
    // Update UI immediately
    const mealCard = checkbox.closest('.activity-card');
    const mealText = mealCard.querySelector('span');
    
    if (isCompleted) {
        mealCard.classList.add('opacity-75');
        mealCard.style.backgroundColor = '#fef3c7';
        mealCard.style.borderColor = '#fbbf24';
        mealText.classList.add('line-through', 'text-gray-500');
        mealText.classList.remove('text-gray-800');
    } else {
        mealCard.classList.remove('opacity-75');
        mealCard.style.backgroundColor = '';
        mealCard.style.borderColor = '';
        mealText.classList.remove('line-through', 'text-gray-500');
        mealText.classList.add('text-gray-800');
    }
    
    // Save to server
    fetch('{{ route("health-plans.meal-completion", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            day: parseInt(day),
            meal_index: parseInt(mealIndex),
            meal_key: mealKey,
            completed: isCompleted
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update progress bar if needed
            if (data.completion_percentage !== undefined) {
                updateProgressBar(data.completion_percentage);
            }
            // Check if week completed
            if (data.week_completed) {
                // Don't auto-show modal, let user click the button
                // The button will be shown in the UI
            }
        } else {
            // Revert on error
            checkbox.checked = !isCompleted;
            toggleMealCompletion(checkbox);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert on error
        checkbox.checked = !isCompleted;
        toggleMealCompletion(checkbox);
    });
}

// Toggle exercise completion
function toggleExerciseCompletion(checkbox) {
    const day = checkbox.dataset.day;
    const exerciseIndex = checkbox.dataset.exerciseIndex;
    const exerciseKey = checkbox.dataset.exerciseKey;
    const isCompleted = checkbox.checked;
    
    // Update UI immediately
    const exerciseCard = checkbox.closest('.activity-card');
    const exerciseText = exerciseCard.querySelector('span');
    
    if (isCompleted) {
        exerciseCard.classList.add('opacity-75');
        exerciseCard.style.backgroundColor = '#dbeafe';
        exerciseCard.style.borderColor = '#60a5fa';
        exerciseText.classList.add('line-through', 'text-gray-500');
        exerciseText.classList.remove('text-gray-800');
    } else {
        exerciseCard.classList.remove('opacity-75');
        exerciseCard.style.backgroundColor = '';
        exerciseCard.style.borderColor = '';
        exerciseText.classList.remove('line-through', 'text-gray-500');
        exerciseText.classList.add('text-gray-800');
    }
    
    // Save to server (similar to meal completion)
    fetch('{{ route("health-plans.meal-completion", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            day: parseInt(day),
            meal_index: parseInt(exerciseIndex),
            meal_key: exerciseKey,
            completed: isCompleted,
            is_exercise: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.completion_percentage !== undefined) {
                updateProgressBar(data.completion_percentage);
            }
            if (data.all_activities_completed) {
                setTimeout(() => {
                    showDayCompletionModal(data.day);
                }, 500);
            }
        } else {
            checkbox.checked = !isCompleted;
            toggleExerciseCompletion(checkbox);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        checkbox.checked = !isCompleted;
        toggleExerciseCompletion(checkbox);
    });
}

// Show week completion modal
function showWeekCompletionModal(weekNumber) {
    document.getElementById('modalWeekNumberInput').value = weekNumber;
    document.getElementById('modalWeekNumber').textContent = weekNumber;
    document.getElementById('weekCompletionModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close week completion modal
function closeWeekCompletionModal() {
    document.getElementById('weekCompletionModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('aiAdviceSection').classList.add('hidden');
    document.getElementById('actionButtonsSection').classList.add('hidden');
    document.getElementById('submitButtonSection').classList.remove('hidden');
    document.getElementById('weekCompletionForm').reset();
    
    // Reload page to show updated data
    window.location.reload();
}

// Handle form submission
document.getElementById('weekCompletionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        week_number: parseInt(formData.get('week_number')),
        weight: formData.get('weight') ? parseFloat(formData.get('weight')) : null,
        mood: formData.get('mood'),
        notes: formData.get('notes')
    };
    
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.textContent = 'Đang xử lý...';
    
    fetch('{{ route("health-plans.week-completion-advice", $plan->id) }}', {
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
            document.getElementById('aiAdviceContent').textContent = result.ai_advice;
            document.getElementById('aiAdviceSection').classList.remove('hidden');
            document.getElementById('actionButtonsSection').classList.remove('hidden');
            
            // Hide submit button section
            document.getElementById('submitButtonSection').classList.add('hidden');
            
            submitButton.disabled = false;
            submitButton.textContent = 'Đã cập nhật!';
            submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
        } else {
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            submitButton.disabled = false;
            submitButton.textContent = 'Cập nhật & Nhận tư vấn';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
        submitButton.disabled = false;
        submitButton.textContent = 'Cập nhật & Nhận tư vấn';
    });
});

// Complete all activities for a day
function completeAllActivities(dayNumber) {
    if (!confirm('Bạn có chắc chắn muốn hoàn thành tất cả hoạt động cho ngày ' + dayNumber + '?')) {
        return;
    }

    // Find the day content container
    const dayContent = document.querySelector(`.day-content[data-day="${dayNumber}"]`);
    if (!dayContent) {
        return;
    }

    // Find and disable button
    const dayHeader = dayContent.querySelector('.flex.justify-between.items-center');
    const button = dayHeader ? dayHeader.querySelector('button') : null;
    let originalText = '';
    if (button) {
        originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...';
    }

    fetch('{{ route("health-plans.complete-all", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            day: parseInt(dayNumber)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all meal checkboxes and their UI
            dayContent.querySelectorAll('.meal-checkbox').forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    const mealCard = checkbox.closest('.activity-card');
                    const mealText = mealCard.querySelector('span');
                    if (mealCard && mealText) {
                        mealCard.classList.add('opacity-75');
                        mealCard.style.backgroundColor = '#fef3c7';
                        mealCard.style.borderColor = '#fbbf24';
                        mealText.classList.add('line-through', 'text-gray-500');
                        mealText.classList.remove('text-gray-800');
                    }
                }
            });

            // Update all exercise checkboxes and their UI
            dayContent.querySelectorAll('.exercise-checkbox').forEach(checkbox => {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    const exerciseCard = checkbox.closest('.activity-card');
                    const exerciseText = exerciseCard.querySelector('span');
                    if (exerciseCard && exerciseText) {
                        exerciseCard.classList.add('opacity-75');
                        exerciseCard.style.backgroundColor = '#dbeafe';
                        exerciseCard.style.borderColor = '#60a5fa';
                        exerciseText.classList.add('line-through', 'text-gray-500');
                        exerciseText.classList.remove('text-gray-800');
                    }
                }
            });

            // Update progress bar
            if (data.completion_percentage !== undefined) {
                updateProgressBar(data.completion_percentage);
            }

            // Update day tab
            const dayTab = document.getElementById('day-tab-' + dayNumber);
            if (dayTab) {
                dayTab.classList.add('bg-green-50');
                if (!dayTab.querySelector('.text-xs.text-green-600')) {
                    const checkmarkSpan = document.createElement('span');
                    checkmarkSpan.className = 'text-xs text-green-600 block';
                    checkmarkSpan.textContent = '✓';
                    dayTab.appendChild(checkmarkSpan);
                }
            }

            // Update day header - replace button with completed badge
            if (dayHeader) {
                const buttonContainer = dayHeader.querySelector('.flex.items-center.gap-2');
                if (buttonContainer) {
                    buttonContainer.innerHTML = '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">✓ Hoàn thành</span>';
                }
            }

            // Show success message and reload after a short delay to ensure UI is updated
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            alert('Có lỗi xảy ra. Vui lòng thử lại.');
            if (button && originalText) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
        if (button && originalText) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });
}

// Save meal selection (which option user chose)
function saveMealSelection(mealKey, optionIndex) {
    // Remove highlight from all options in this meal group
    const mealGroup = document.querySelector(`input[name="meal_option_${mealKey}"]`).closest('.meal-group');
    if (mealGroup) {
        const allLabels = mealGroup.querySelectorAll('label');
        allLabels.forEach(label => {
            label.classList.remove('ring-2', 'ring-orange-400', 'bg-orange-50');
            label.classList.add('bg-white');
        });
    }
    
    // Highlight the selected option
    const selectedRadio = document.querySelector(`input[name="meal_option_${mealKey}"][value="${optionIndex}"]`);
    if (selectedRadio) {
        const selectedLabel = selectedRadio.closest('label');
        selectedLabel.classList.add('ring-2', 'ring-orange-400', 'bg-orange-50');
        selectedLabel.classList.remove('bg-white');
    }
    
    // Store selection in localStorage for persistence
    const selections = JSON.parse(localStorage.getItem('meal_selections_{{ $plan->id }}') || '{}');
    selections[mealKey] = optionIndex;
    localStorage.setItem('meal_selections_{{ $plan->id }}', JSON.stringify(selections));
    
    // Send to server to save permanently
    fetch('{{ route("health-plans.meal-selection", $plan->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            meal_key: mealKey,
            option_index: optionIndex
        })
    }).then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Meal selection saved successfully');
        }
    })
    .catch(error => {
        console.error('Error saving meal selection:', error);
    });
}

// Load saved meal selections on page load
document.addEventListener('DOMContentLoaded', function() {
    const selections = JSON.parse(localStorage.getItem('meal_selections_{{ $plan->id }}') || '{}');
    Object.keys(selections).forEach(mealKey => {
        const optionIndex = selections[mealKey];
        const radio = document.querySelector(`input[name="meal_option_${mealKey}"][value="${optionIndex}"]`);
        if (radio) {
            radio.checked = true;
            const label = radio.closest('label');
            if (label) {
                label.classList.add('ring-2', 'ring-orange-400', 'bg-orange-50');
                label.classList.remove('bg-white');
            }
        }
    });
    
    // Also load from server if available (for cross-device sync)
    // This will be handled by the PHP code that sets $selectedOption
});

// Update progress bar
function updateProgressBar(percentage) {
    const progressBar = document.querySelector('.bg-gradient-to-r.from-blue-600');
    const progressText = document.querySelector('.font-semibold.text-blue-600');
    if (progressBar) {
        progressBar.style.width = percentage + '%';
    }
    if (progressText) {
        progressText.textContent = percentage + '%';
    }
}

// Show adjust plan modal
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

// Close adjust plan modal
function closeAdjustPlanModal() {
    document.getElementById('adjustPlanModal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Handle adjust plan form submission
document.getElementById('adjustPlanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        day_number: parseInt(formData.get('day_number')),
        reason: formData.get('reason'),
        weight: formData.get('weight') ? parseFloat(formData.get('weight')) : null,
        mood: formData.get('mood'),
        actual_meals: formData.get('actual_meals'),
        actual_exercises: formData.get('actual_exercises'),
        notes: formData.get('notes')
    };
    
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = '<svg class="animate-spin h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Đang xử lý...';
    
    fetch('{{ route("health-plans.adjust-plan", $plan->id) }}', {
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
            // Clear localStorage meal selections for adjusted days
            const dayNumber = parseInt(formData.get('day_number'));
            const selections = JSON.parse(localStorage.getItem('meal_selections_{{ $plan->id }}') || '{}');
            
            // Remove selections for adjusted day and all days after
            Object.keys(selections).forEach(key => {
                const match = key.match(/meal_(\d+)_/);
                if (match && parseInt(match[1]) >= dayNumber) {
                    delete selections[key];
                }
            });
            
            localStorage.setItem('meal_selections_{{ $plan->id }}', JSON.stringify(selections));
            
            document.getElementById('adjustResultContent').textContent = result.adjustment_summary || 'Kế hoạch đã được điều chỉnh thành công!';
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
                    // Force reload with cache busting to ensure fresh data
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
</script>
@endsection

