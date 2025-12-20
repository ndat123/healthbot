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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
        </div>

        <!-- Daily Meal Plans -->
        @if($plan->plan_data && isset($plan->plan_data['daily_meals']))
        <div class="space-y-6">
            @foreach($plan->plan_data['daily_meals'] as $dayPlan)
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Day {{ $dayPlan['day'] ?? $loop->iteration }}</h2>

                    <!-- Meals -->
                    @if(isset($dayPlan['meals']))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        @foreach($dayPlan['meals'] as $meal)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-semibold text-gray-800">{{ $meal['time'] ?? 'Meal' }}</h3>
                                    @if(isset($meal['calories']))
                                        <span class="text-sm text-gray-600">{{ $meal['calories'] }} kcal</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 mb-2">{{ $meal['food'] ?? 'N/A' }}</p>
                                @if(isset($meal['portion']))
                                    <p class="text-sm text-gray-600 mb-2">{{ $meal['portion'] }}</p>
                                @endif
                                @if(isset($meal['protein']) || isset($meal['carbs']) || isset($meal['fats']))
                                    <div class="flex gap-4 text-xs text-gray-500 mt-2">
                                        @if(isset($meal['protein']))<span>Protein: {{ $meal['protein'] }}g</span>@endif
                                        @if(isset($meal['carbs']))<span>Carbs: {{ $meal['carbs'] }}g</span>@endif
                                        @if(isset($meal['fats']))<span>Fats: {{ $meal['fats'] }}g</span>@endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <!-- Nutrition Summary -->
                    @if(isset($dayPlan['nutrition']))
                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Daily Nutrition Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            @if(isset($dayPlan['nutrition']['total_calories']))
                                <div>
                                    <p class="text-gray-600">Total Calories</p>
                                    <p class="font-semibold text-gray-800">{{ $dayPlan['nutrition']['total_calories'] }} kcal</p>
                                </div>
                            @endif
                            @if(isset($dayPlan['nutrition']['protein']))
                                <div>
                                    <p class="text-gray-600">Protein</p>
                                    <p class="font-semibold text-gray-800">{{ $dayPlan['nutrition']['protein'] }}g</p>
                                </div>
                            @endif
                            @if(isset($dayPlan['nutrition']['carbs']))
                                <div>
                                    <p class="text-gray-600">Carbs</p>
                                    <p class="font-semibold text-gray-800">{{ $dayPlan['nutrition']['carbs'] }}g</p>
                                </div>
                            @endif
                            @if(isset($dayPlan['nutrition']['fats']))
                                <div>
                                    <p class="text-gray-600">Fats</p>
                                    <p class="font-semibold text-gray-800">{{ $dayPlan['nutrition']['fats'] }}g</p>
                                </div>
                            @endif
                        </div>
                        @if(isset($dayPlan['nutrition']['vitamins']))
                            <p class="text-sm text-gray-600 mt-2">{{ $dayPlan['nutrition']['vitamins'] }}</p>
                        @endif
                    </div>
                    @endif

                    <!-- Shopping List -->
                    @if(isset($dayPlan['shopping_list']) && count($dayPlan['shopping_list']) > 0)
                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-gray-800 mb-2">Shopping List</h3>
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
@endsection

