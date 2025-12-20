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
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download All Reports
            </button>
        </div>
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
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Monthly User Activity</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">User Analytics</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-10-01</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ready
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900" title="View Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button class="text-green-600 hover:text-green-900" title="Download Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">AI Performance Report</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">AI Analytics</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-09-28</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ready
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900" title="View Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button class="text-green-600 hover:text-green-900" title="Download Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Health Trend Analysis</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Health Analytics</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2023-09-25</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ready
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-900" title="View Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button class="text-green-600 hover:text-green-900" title="Download Report">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
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
</script>
@endsection