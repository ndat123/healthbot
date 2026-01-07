@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Analytics & Reports</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Total Users</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $analytics['user_stats']['total'] }}</p>
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
                    <span class="font-medium text-gray-900">{{ $analytics['user_stats']['active'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($analytics['user_stats']['active'] / $analytics['user_stats']['total']) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- AI Sessions Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">AI Sessions</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $analytics['ai_sessions']['total'] }}</p>
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
                    <span class="font-medium text-gray-900">{{ $analytics['ai_sessions']['average_duration'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: 65%"></div>
                </div>
            </div>
        </div>

        <!-- Premium Users Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Premium Users</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $analytics['user_stats']['premium'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Conversion Rate</span>
                    <span class="font-medium text-gray-900">{{ $analytics['user_stats']['conversion_rate'] ?? 0 }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    @php
                        $conversionRate = $analytics['user_stats']['conversion_rate'] ?? 0;
                    @endphp
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, $conversionRate) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Doctors Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Doctors</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $analytics['user_stats']['doctors'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Active</span>
                    <span class="font-medium text-gray-900">{{ $analytics['user_stats']['active_doctors'] ?? 0 }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    @php
                        $doctors = $analytics['user_stats']['doctors'] ?? 0;
                        $activeDoctors = $analytics['user_stats']['active_doctors'] ?? 0;
                        $activeDoctorsPercent = $doctors > 0 ? round(($activeDoctors / $doctors) * 100) : 0;
                    @endphp
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ $activeDoctorsPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">User Growth</h2>
                <div class="flex space-x-2">
                    <button onclick="loadUserGrowth('week')" 
                            class="px-3 py-1 text-sm font-medium rounded-md transition duration-300 {{ ($analytics['current_period'] ?? 'year') == 'week' ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                        Week
                    </button>
                    <button onclick="loadUserGrowth('month')" 
                            class="px-3 py-1 text-sm font-medium rounded-md transition duration-300 {{ ($analytics['current_period'] ?? 'year') == 'month' ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                        Month
                    </button>
                    <button onclick="loadUserGrowth('year')" 
                            class="px-3 py-1 text-sm font-medium rounded-md transition duration-300 {{ ($analytics['current_period'] ?? 'year') == 'year' ? 'text-blue-700 bg-blue-100' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                        Year
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Common Health Issues -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Common Health Issues</h2>
            <div class="space-y-4">
                @forelse($analytics['ai_sessions']['common_issues'] as $issue)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="text-blue-600 mr-3 text-lg">•</span>
                            <span class="text-gray-700 font-medium">{{ $issue['issue'] }}</span>
                        </div>
                        <span class="text-gray-500 text-sm">{{ $issue['count'] }} sessions</span>
                    </div>
                @empty
                    <div class="text-center text-gray-500 text-sm py-4">
                        No common issues data available yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Health Trend Analysis -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Health Trend Analysis</h2>
            <div class="space-y-4">
                @forelse($analytics['health_trends'] as $trend)
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150">
                        <h3 class="text-lg font-medium text-gray-900">{{ $trend['trend'] }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $trend['period'] }}</p>
                        @if(isset($trend['growth']) && $trend['growth'])
                            <p class="mt-1 text-sm font-medium text-green-600">{{ $trend['growth'] }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-4 border border-gray-100 rounded-xl text-center text-gray-500">
                        No trends data available yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- AI Performance -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">AI Performance</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Accuracy</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['ai_performance']['accuracy'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Response Time</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['ai_performance']['response_time'] ?? 0 }}s</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">User Satisfaction</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['ai_performance']['user_satisfaction'] ?? 0 }}★</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">✓</span>
                        <span class="text-gray-700 font-medium">Emergency Alerts</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['ai_performance']['emergency_alerts_accuracy'] ?? 0 }}% accurate</span>
                </div>
            </div>
        </div>

        <!-- User Demographics -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">User Demographics</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">•</span>
                        <span class="text-gray-700 font-medium">Age 18-34</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['demographics']['age_distribution']['18-34'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">•</span>
                        <span class="text-gray-700 font-medium">Age 35-54</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['demographics']['age_distribution']['35-54'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">•</span>
                        <span class="text-gray-700 font-medium">Age 55+</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['demographics']['age_distribution']['55+'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-blue-600 mr-3 text-lg">•</span>
                        <span class="text-gray-700 font-medium">Gender</span>
                    </div>
                    <span class="text-gray-500 text-sm">{{ $analytics['demographics']['gender_distribution'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Detailed Reports</h2>
            <div class="flex items-center space-x-3">
                <form action="{{ route('admin.reports.generate') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="user_activity">
                    <input type="hidden" name="format" value="csv">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tạo Báo Cáo
                    </button>
                </form>
                <a href="{{ route('admin.reports.download-all') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Tải Tất Cả
                </a>
            </div>
        </div>
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Generated</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $report->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $report->category }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $report->generated_at ? $report->generated_at->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($report->status === 'ready')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Sẵn sàng
                                    </span>
                                @elseif($report->status === 'pending')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Đang xử lý
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Lỗi
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                @if($report->status === 'ready')
                                    <a href="{{ route('admin.reports.view', $report) }}" class="text-blue-600 hover:text-blue-900" title="Xem Báo Cáo">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.reports.download', $report) }}" class="text-green-600 hover:text-green-900" title="Tải Báo Cáo">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p>Chưa có báo cáo nào. Hãy tạo báo cáo mới!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Plans Management Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Quản Lý Kế Hoạch</h2>
            <div class="flex items-center space-x-4">
                <!-- User Filter -->
                <form method="GET" action="{{ route('admin.analytics') }}" class="flex items-center space-x-2">
                    <input type="hidden" name="plan_type" value="{{ $planType ?? 'health' }}">
                    <select name="user_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tất cả người dùng</option>
                        @foreach($allUsers ?? [] as $user)
                            <option value="{{ $user->id }}" {{ ($selectedUser ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <button onclick="switchTab('health')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ ($planType ?? 'health') == 'health' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Kế Hoạch Sức Khỏe ({{ $healthPlans->total() ?? 0 }})
                </button>
                <button onclick="switchTab('nutrition')" 
                        class="tab-button py-4 px-1 border-b-2 font-medium text-sm {{ ($planType ?? 'health') == 'nutrition' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Kế Hoạch Dinh Dưỡng ({{ $nutritionPlans->total() ?? 0 }})
                </button>
            </nav>
        </div>

        <!-- Health Plans Tab -->
        <div id="health-tab" class="tab-content {{ ($planType ?? 'health') == 'health' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người Dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu Đề</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiến Độ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($healthPlans ?? [] as $plan)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-medium text-gray-900">{{ $plan->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-500 text-xs">{{ $plan->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($plan->title, 40) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->duration_days }} ngày</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $plan->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $plan->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->completion_percentage ?? 0 }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                    <a href="{{ route('health-plans.show', $plan->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Xem">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteHealthPlan({{ $plan->id }}, '{{ addslashes($plan->title) }}')" class="text-red-600 hover:text-red-900" title="Xóa">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <p>Chưa có kế hoạch sức khỏe nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($healthPlans) && $healthPlans->hasPages())
                <div class="mt-4">
                    {{ $healthPlans->links() }}
                </div>
            @endif
        </div>

        <!-- Nutrition Plans Tab -->
        <div id="nutrition-tab" class="tab-content {{ ($planType ?? 'health') == 'nutrition' ? '' : 'hidden' }}">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Người Dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu Đề</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời Gian</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calo/ngày</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Tạo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($nutritionPlans ?? [] as $plan)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-medium text-gray-900">{{ $plan->user->name ?? 'N/A' }}</div>
                                    <div class="text-gray-500 text-xs">{{ $plan->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($plan->title, 40) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->duration_days }} ngày</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->daily_calories ?? 'N/A' }} kcal</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $plan->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $plan->status === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                    <a href="{{ route('nutrition.show', $plan->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Xem">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button onclick="deleteNutritionPlan({{ $plan->id }}, '{{ addslashes($plan->title) }}')" class="text-red-600 hover:text-red-900" title="Xóa">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                    <p>Chưa có kế hoạch dinh dưỡng nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($nutritionPlans) && $nutritionPlans->hasPages())
                <div class="mt-4">
                    {{ $nutritionPlans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let userGrowthChart = null;

function initUserGrowthChart() {
    const ctx = document.getElementById('userGrowthChart');
    if (!ctx) return;

    const chartData = @json($analytics['user_growth'] ?? ['labels' => [], 'data' => []]);
    
    userGrowthChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels || [],
            datasets: [{
                label: 'New Users',
                data: chartData.data || [],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

function loadUserGrowth(period) {
    // Update button states
    document.querySelectorAll('[onclick^="loadUserGrowth"]').forEach(btn => {
        btn.classList.remove('text-blue-700', 'bg-blue-100');
        btn.classList.add('text-gray-700', 'bg-gray-100');
    });
    event.target.classList.remove('text-gray-700', 'bg-gray-100');
    event.target.classList.add('text-blue-700', 'bg-blue-100');

    // Reload page with new period parameter
    window.location.href = `{{ route('admin.analytics') }}?period=${period}`;
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    initUserGrowthChart();
});

// Tab switching
function switchTab(type) {
    // Hide all tabs
    document.getElementById('health-tab').classList.add('hidden');
    document.getElementById('nutrition-tab').classList.add('hidden');
    
    // Show selected tab
    if (type === 'health') {
        document.getElementById('health-tab').classList.remove('hidden');
    } else {
        document.getElementById('nutrition-tab').classList.remove('hidden');
    }
    
    // Update tab buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-blue-500', 'text-blue-600');
    
    // Reload page with new plan_type
    const url = new URL(window.location.href);
    url.searchParams.set('plan_type', type);
    window.location.href = url.toString();
}

// Delete Health Plan
function deleteHealthPlan(planId, planTitle) {
    if (!confirm(`Bạn có chắc chắn muốn xóa kế hoạch sức khỏe "${planTitle}"?\n\nHành động này không thể hoàn tác.`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('/admin/health-plans') }}/${planId}`;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
}

// Delete Nutrition Plan
function deleteNutritionPlan(planId, planTitle) {
    if (!confirm(`Bạn có chắc chắn muốn xóa kế hoạch dinh dưỡng "${planTitle}"?\n\nHành động này không thể hoàn tác.`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `{{ url('/admin/nutrition') }}/${planId}`;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection