@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Health Tracking Dashboard</h1>
            <p class="text-xl text-gray-600">Track your health metrics, view progress, and manage reminders</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Add Metric Form -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Record Health Metric</h2>
                    <form action="{{ route('health-tracking.metric.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="recorded_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="recorded_date" name="recorded_date" 
                                       value="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" step="0.1" min="20" max="300"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-1">Height (cm)</label>
                                <input type="number" id="height" name="height" step="0.1" min="50" max="250"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-1">Blood Pressure (Systolic)</label>
                                <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" min="50" max="250"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-1">Blood Pressure (Diastolic)</label>
                                <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" min="30" max="150"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-1">Blood Sugar (mg/dL)</label>
                                <input type="number" id="blood_sugar" name="blood_sugar" step="0.1" min="50" max="500"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-1">Heart Rate (bpm)</label>
                                <input type="number" id="heart_rate" name="heart_rate" min="40" max="200"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2 rounded-lg hover:from-purple-700 hover:to-pink-700 transition-colors">
                            Record Metric
                        </button>
                    </form>
                </div>

                <!-- Charts -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Health Progress Charts</h2>
                        <select id="chart-days" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                            <option value="7">Last 7 days</option>
                            <option value="30" selected>Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Weight Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Weight (kg)</h3>
                            <div class="h-48">
                                <canvas id="weightChart"></canvas>
                            </div>
                        </div>
                        <!-- BMI Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">BMI</h3>
                            <div class="h-48">
                                <canvas id="bmiChart"></canvas>
                            </div>
                        </div>
                        <!-- Blood Pressure Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Blood Pressure</h3>
                            <div class="h-48">
                                <canvas id="bpChart"></canvas>
                            </div>
                        </div>
                        <!-- Blood Sugar Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Blood Sugar (mg/dL)</h3>
                            <div class="h-48">
                                <canvas id="bsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Reminders -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Reminders</h2>
                        <button id="add-reminder-btn" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            + Add
                        </button>
                    </div>
                    <div class="space-y-3">
                        @forelse($reminders as $reminder)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-sm">{{ $reminder->title }}</h3>
                                        <p class="text-xs text-gray-600">{{ $reminder->reminder_time }}</p>
                                    </div>
                                    <form action="{{ route('health-tracking.reminder.update', $reminder->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="is_active" value="{{ $reminder->is_active ? '0' : '1' }}">
                                        <button type="submit" class="text-xs {{ $reminder->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $reminder->is_active ? '✓' : '○' }}
                                        </button>
                                    </form>
                                </div>
                                <p class="text-xs text-gray-500 mb-2">{{ ucfirst($reminder->reminder_type) }}</p>
                                <form action="{{ route('health-tracking.reminder.delete', $reminder->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this reminder?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No reminders yet</p>
                        @endforelse
                    </div>
                </div>

                <!-- AI Consultation History -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Recent AI Consultations</h2>
                    <div class="space-y-3">
                        @forelse($consultations as $consultation)
                            <div class="border-l-4 border-purple-500 pl-3 py-2">
                                <p class="text-sm font-medium text-gray-800">{{ $consultation->topic ?? 'General' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($consultation->user_message, 40) }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $consultation->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">No consultations yet</p>
                        @endforelse
                    </div>
                    <a href="{{ route('ai-consultation.index') }}" class="block text-center mt-4 text-purple-600 hover:text-purple-800 text-sm font-medium">
                        View All →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reminder Modal -->
<div id="reminder-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Add Reminder</h2>
        <form action="{{ route('health-tracking.reminder.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reminder_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="reminder_type" name="reminder_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="medication">Medication</option>
                    <option value="water">Water</option>
                    <option value="exercise">Exercise</option>
                    <option value="meal">Meal</option>
                    <option value="appointment">Appointment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" id="title" name="title" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                <input type="time" id="reminder_time" name="reminder_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_recurring" value="1" checked
                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Recurring</span>
                </label>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Create
                </button>
                <button type="button" id="close-modal" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartData = @json($chartData);
let weightChart, bmiChart, bpChart, bsChart;

function initCharts() {
    const ctx1 = document.getElementById('weightChart');
    const ctx2 = document.getElementById('bmiChart');
    const ctx3 = document.getElementById('bpChart');
    const ctx4 = document.getElementById('bsChart');

    // Weight Chart
    if (ctx1) {
        weightChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: chartData.labels,
                    datasets: [{
                    label: 'Weight (kg)',
                    data: chartData.weight,
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // BMI Chart
    if (ctx2) {
        bmiChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'BMI',
                    data: chartData.bmi,
                    borderColor: 'rgb(236, 72, 153)',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Blood Pressure Chart
    if (ctx3) {
        bpChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Sys',
                        data: chartData.blood_pressure.map(bp => bp ? bp.systolic : null),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Dia',
                        data: chartData.blood_pressure.map(bp => bp ? bp.diastolic : null),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Blood Sugar Chart
    if (ctx4) {
        bsChart = new Chart(ctx4, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Blood Sugar',
                    data: chartData.blood_sugar,
                    borderColor: 'rgb(251, 146, 60)',
                    backgroundColor: 'rgba(251, 146, 60, 0.1)',
                    borderWidth: 2,
                    pointRadius: 2,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: false,
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }
}

// Modal handlers
document.getElementById('add-reminder-btn')?.addEventListener('click', () => {
    document.getElementById('reminder-modal').classList.remove('hidden');
});

document.getElementById('close-modal')?.addEventListener('click', () => {
    document.getElementById('reminder-modal').classList.add('hidden');
});

// Chart days selector
document.getElementById('chart-days')?.addEventListener('change', function() {
    const days = this.value;
    fetch(`{{ route('health-tracking.metrics.data') }}?days=${days}`)
        .then(res => res.json())
        .then(data => {
            // Update charts with new data
            if (weightChart) {
                weightChart.data.labels = data.labels;
                weightChart.data.datasets[0].data = data.weight;
                weightChart.update();
            }
            if (bmiChart) {
                bmiChart.data.labels = data.labels;
                bmiChart.data.datasets[0].data = data.bmi;
                bmiChart.update();
            }
            if (bpChart) {
                bpChart.data.labels = data.labels;
                bpChart.data.datasets[0].data = data.blood_pressure.map(bp => bp ? bp.systolic : null);
                bpChart.data.datasets[1].data = data.blood_pressure.map(bp => bp ? bp.diastolic : null);
                bpChart.update();
            }
            if (bsChart) {
                bsChart.data.labels = data.labels;
                bsChart.data.datasets[0].data = data.blood_sugar;
                bsChart.update();
            }
        });
});

// Initialize charts on load
document.addEventListener('DOMContentLoaded', initCharts);
</script>
@endsection
