@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- User Statistics Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Users</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['users'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Active</span>
                    <span class="font-medium text-gray-900">{{ $stats['active_users'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    @php
                        $activePercentage = $stats['users'] > 0 ? round(($stats['active_users'] ?? 0) / $stats['users'] * 100) : 0;
                    @endphp
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $activePercentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- AI Sessions Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">AI Sessions</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['ai_sessions'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Avg. Duration</span>
                    <span class="font-medium text-gray-900">{{ $stats['avg_duration'] ?? '0:00' }} min</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: 65%"></div>
                </div>
            </div>
        </div>

        <!-- AI Performance Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">AI Performance</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['ai_performance']['accuracy'] }}%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Response Time</span>
                    <span class="font-medium text-gray-900">{{ $stats['ai_performance']['response_time'] }}s</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: 85%"></div>
                </div>
            </div>
        </div>

        <!-- User Satisfaction Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">User Satisfaction</h3>
                    <div class="flex items-center mt-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['ai_performance']['user_satisfaction'] }}</p>
                        <span class="ml-1 text-xl text-yellow-500">★</span>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Feedback</span>
                    <span class="font-medium text-gray-900">92%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 92%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Popular Health Topics -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Popular Health Topics</h3>
            <div class="space-y-4">
                @foreach($stats['popular_topics'] as $topic)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-blue-600 mr-3 text-lg">•</span>
                            <span class="text-gray-700 font-medium">{{ $topic['name'] }}</span>
                        </div>
                        <span class="text-gray-500 text-sm">{{ $topic['count'] }} searches</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Health Trend Analysis -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Health Trend Analysis</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-green-600 mr-3 text-lg">↑</span>
                        <span class="text-gray-700 font-medium">Flu Cases</span>
                    </div>
                    <span class="text-gray-500 text-sm">+15% last month</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-green-600 mr-3 text-lg">↑</span>
                        <span class="text-gray-700 font-medium">Mental Health Awareness</span>
                    </div>
                    <span class="text-gray-500 text-sm">+22% last quarter</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-red-600 mr-3 text-lg">↓</span>
                        <span class="text-gray-700 font-medium">Smoking Rates</span>
                    </div>
                    <span class="text-gray-500 text-sm">-8% last year</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-green-600 mr-3 text-lg">↑</span>
                        <span class="text-gray-700 font-medium">Exercise Adoption</span>
                    </div>
                    <span class="text-gray-500 text-sm">+18% last year</span>
                </div>
            </div>
        </div>

        <!-- AI Consultation Performance -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">AI Consultation Performance</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Accuracy</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $stats['ai_performance']['accuracy'] }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Response Time</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $stats['ai_performance']['response_time'] }}s</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">User Satisfaction</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $stats['ai_performance']['user_satisfaction'] }}★</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Emergency Alerts</span>
                    </div>
                    <span class="text-gray-500 text-sm">98% accurate</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection