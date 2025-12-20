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
        <div class="space-y-6">
            @foreach($plan->plan_data['daily_plans'] as $dayPlan)
                @php
                    $dayNumber = $dayPlan['day'] ?? $loop->iteration;
                    $progress = $plan->progress_data['daily_progress'][$dayNumber - 1] ?? null;
                    $isCompleted = $progress['completed'] ?? false;
                    $isToday = $dayNumber == $plan->start_date->diffInDays(now()) + 1;
                @endphp
                
                <div class="bg-white rounded-xl shadow-lg p-6 {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Day {{ $dayNumber }}</h2>
                        @if($isCompleted)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">✓ Completed</span>
                        @elseif($isToday)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Today</span>
                        @endif
                    </div>

                    <!-- Meals -->
                    @if(isset($dayPlan['meals']))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">🍽️ Meals</h3>
                        <div class="space-y-2">
                            @foreach($dayPlan['meals'] as $meal)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-800">{{ $meal['time'] ?? 'Meal' }}:</span>
                                        <span class="text-gray-600 ml-2">{{ $meal['food'] ?? 'N/A' }}</span>
                                    </div>
                                    @if(isset($meal['calories']))
                                        <span class="text-sm text-gray-500">{{ $meal['calories'] }} cal</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Exercises -->
                    @if(isset($dayPlan['exercises']))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">💪 Exercises</h3>
                        <div class="space-y-2">
                            @foreach($dayPlan['exercises'] as $exercise)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="font-medium text-gray-800">{{ $exercise['type'] ?? 'Exercise' }}:</span>
                                        <span class="text-gray-600 ml-2">{{ $exercise['name'] ?? 'N/A' }}</span>
                                    </div>
                                    @if(isset($exercise['duration']))
                                        <span class="text-sm text-gray-500">{{ $exercise['duration'] }} min</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Lifestyle -->
                    @if(isset($dayPlan['lifestyle']))
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">🌱 Lifestyle</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            @foreach($dayPlan['lifestyle'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if(isset($dayPlan['notes']))
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-gray-700">{{ $dayPlan['notes'] }}</p>
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
@endsection

