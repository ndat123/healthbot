@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">AI Performance Metrics</h1>
        <a href="{{ route('admin.ai-management') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to AI Management
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Accuracy -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 mb-2">Accuracy</h3>
            @if($metrics['accuracy'] !== null)
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ $metrics['accuracy'] }}%</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $metrics['accuracy'] }}%"></div>
                </div>
            @else
                <div class="text-4xl font-bold text-gray-400 mb-2">N/A</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-gray-300 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
            @endif
            <p class="text-sm text-gray-500 mt-2">Overall accuracy of AI responses</p>
        </div>

        <!-- Response Time -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 mb-2">Response Time</h3>
            @if($metrics['response_time'] !== null)
                <div class="text-4xl font-bold text-green-600 mb-2">{{ $metrics['response_time'] }}s</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ min(100, 100 - ($metrics['response_time'] * 10)) }}%"></div>
                </div>
            @else
                <div class="text-4xl font-bold text-gray-400 mb-2">N/A</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-gray-300 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
            @endif
            <p class="text-sm text-gray-500 mt-2">Average response time for AI</p>
        </div>

        <!-- User Satisfaction -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-lg font-medium text-gray-800 mb-2">User Satisfaction</h3>
            @if($metrics['user_satisfaction'] !== null)
                <div class="flex items-center">
                    <div class="text-4xl font-bold text-yellow-600 mr-2">{{ $metrics['user_satisfaction'] }}</div>
                    <span class="text-xl text-yellow-500">★</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-yellow-600 h-2.5 rounded-full" style="width: {{ $metrics['user_satisfaction'] * 20 }}%"></div>
                </div>
            @else
                <div class="flex items-center">
                    <div class="text-4xl font-bold text-gray-400 mr-2">N/A</div>
                    <span class="text-xl text-gray-400">★</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-gray-300 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
            @endif
            <p class="text-sm text-gray-500 mt-2">User satisfaction rating</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Emergency Cases -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Emergency Cases Distribution</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Low</span>
                    <span class="font-bold text-green-600">{{ $metrics['low_emergency'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Medium</span>
                    <span class="font-bold text-yellow-600">{{ $metrics['medium_emergency'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">High</span>
                    <span class="font-bold text-orange-600">{{ $metrics['high_emergency'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Critical</span>
                    <span class="font-bold text-red-600">{{ $metrics['critical_emergency'] }}</span>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Sessions</span>
                    <span class="font-bold text-gray-900">{{ number_format($metrics['total_sessions']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Consultations</span>
                    <span class="font-bold text-gray-900">{{ number_format($metrics['total_consultations']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Emergency Cases</span>
                    <span class="font-bold text-red-600">{{ number_format($metrics['emergency_cases']) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




