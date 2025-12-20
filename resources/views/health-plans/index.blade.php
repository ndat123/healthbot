@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Personalized Health Plans</h1>
            <p class="text-xl text-gray-600">Create a customized health plan tailored to your unique needs and goals</p>
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
                <h2 class="text-2xl font-bold text-gray-800">Your Health Profile</h2>
                <a href="{{ route('health-plans.profile') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    {{ $profile ? 'Update Profile' : 'Create Profile' }}
                </a>
            </div>

            @if($profile)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Age</p>
                        <p class="text-lg font-semibold">{{ $profile->age ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Gender</p>
                        <p class="text-lg font-semibold">{{ ucfirst($profile->gender ?? 'Not set') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">BMI</p>
                        <p class="text-lg font-semibold">{{ $profile->bmi ?? 'Not calculated' }}</p>
                    </div>
                    @if($profile->height && $profile->weight)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Height</p>
                        <p class="text-lg font-semibold">{{ $profile->height }} cm</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Weight</p>
                        <p class="text-lg font-semibold">{{ $profile->weight }} kg</p>
                    </div>
                    @endif
                    @if($profile->health_goals)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Goals</p>
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
                    <p class="text-gray-600 mb-4">You haven't created your health profile yet.</p>
                    <a href="{{ route('health-plans.profile') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Create Your Profile
                    </a>
                </div>
            @endif
        </div>

        <!-- Generate Plan Section -->
        @if($profile)
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Generate New Health Plan</h2>
            <form action="{{ route('health-plans.generate') }}" method="POST" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Plan Duration (7-30 days)
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
                    Generate Plan
                </button>
            </form>
        </div>
        @endif

        <!-- Existing Plans -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Health Plans</h2>
            
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
                                    <span class="font-medium">Duration:</span> {{ $plan->duration_days }} days
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Start:</span> {{ $plan->start_date->format('M d, Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">End:</span> {{ $plan->end_date->format('M d, Y') }}
                                </p>
                                <div class="mt-3">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">Progress</span>
                                        <span class="font-semibold">{{ $plan->completion_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $plan->completion_percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('health-plans.show', $plan->id) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                View Plan
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 mb-4">You don't have any health plans yet.</p>
                    @if($profile)
                        <p class="text-sm text-gray-500">Generate your first personalized health plan above!</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

