@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Bảng Điều Khiển Theo Dõi Sức Khỏe</h1>
            <p class="text-xl text-gray-600">Theo dõi các chỉ số sức khỏe, xem tiến trình và quản lý nhắc nhở</p>
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
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Ghi Nhận Chỉ Số Sức Khỏe</h2>
                    <p class="text-sm text-gray-600 mb-4">💡 Chỉ nhập những chỉ số bạn đã đo được. Không cần nhập tất cả các trường mỗi ngày.</p>
                    <form action="{{ route('health-tracking.metric.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="recorded_date" class="block text-sm font-medium text-gray-700 mb-1">Ngày <span class="text-red-500">*</span></label>
                                <input type="date" id="recorded_date" name="recorded_date" 
                                       value="{{ date('Y-m-d') }}" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Cân Nặng (kg) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="weight" name="weight" step="0.1" min="20" max="300"
                                       placeholder="Ví dụ: 65.5"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-1">Chiều Cao (cm) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="height" name="height" step="0.1" min="50" max="250"
                                       placeholder="Ví dụ: 170"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-1">Huyết Áp (Tâm Thu) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" min="50" max="250"
                                       placeholder="Ví dụ: 120"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-1">Huyết Áp (Tâm Trương) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" min="30" max="150"
                                       placeholder="Ví dụ: 80"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-1">Đường Huyết (mg/dL) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="blood_sugar" name="blood_sugar" step="0.1" min="50" max="500"
                                       placeholder="Ví dụ: 95"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-1">Nhịp Tim (bpm) <span class="text-gray-400 text-xs">(tùy chọn)</span></label>
                                <input type="number" id="heart_rate" name="heart_rate" min="40" max="200"
                                       placeholder="Ví dụ: 72"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Ghi Chú</label>
                            <textarea id="notes" name="notes" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-2 rounded-lg hover:from-purple-700 hover:to-pink-700 transition-colors">
                            Ghi Nhận
                        </button>
                    </form>
                </div>

                <!-- Charts -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Biểu Đồ Tiến Trình Sức Khỏe</h2>
                        <select id="chart-days" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                            <option value="7">7 ngày qua</option>
                            <option value="30" selected>30 ngày qua</option>
                            <option value="90">90 ngày qua</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Weight Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Cân Nặng (kg)</h3>
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
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Huyết Áp</h3>
                            <div class="h-48">
                                <canvas id="bpChart"></canvas>
                            </div>
                        </div>
                        <!-- Blood Sugar Chart -->
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wider">Đường Huyết (mg/dL)</h3>
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
                        <h2 class="text-xl font-bold text-gray-800">Nhắc Nhở</h2>
                        <button id="add-reminder-btn" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                            + Thêm
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
                                <form action="{{ route('health-tracking.reminder.delete', $reminder->id) }}" method="POST" class="inline" onsubmit="return confirm('Xóa nhắc nhở này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">Xóa</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Chưa có nhắc nhở nào</p>
                        @endforelse
                    </div>
                </div>

                <!-- AI Consultation History -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Tư Vấn AI Gần Đây</h2>
                    <div class="space-y-3">
                        @forelse($consultations as $consultation)
                            <a href="{{ route('ai-consultation.index') }}?session={{ $consultation->session_id }}" 
                               class="flex items-center justify-between border-l-4 border-purple-500 pl-3 py-2 group hover:bg-purple-50 rounded-r-lg transition-colors cursor-pointer">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $consultation->topic ?? 'Chung' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit($consultation->user_message, 40) }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $consultation->created_at->diffForHumans() }}</p>
                                </div>
                                <button type="button" 
                                        class="ml-2 p-1 text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition-opacity delete-consultation-btn"
                                        data-session-id="{{ $consultation->session_id }}"
                                        onclick="event.stopPropagation(); event.preventDefault(); deleteConsultationFromDashboard('{{ $consultation->session_id }}', this);"
                                        title="Xóa consultation">
                                    🗑️
                                </button>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Chưa có tư vấn nào</p>
                        @endforelse
                    </div>
                    <a href="{{ route('ai-consultation.index') }}" class="block text-center mt-4 text-purple-600 hover:text-purple-800 text-sm font-medium">
                        Xem Tất Cả →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reminder Modal -->
<div id="reminder-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Thêm Nhắc Nhở</h2>
        <form action="{{ route('health-tracking.reminder.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="reminder_type" class="block text-sm font-medium text-gray-700 mb-1">Loại</label>
                <select id="reminder_type" name="reminder_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="medication">Thuốc</option>
                    <option value="water">Nước</option>
                    <option value="exercise">Tập thể dục</option>
                    <option value="meal">Bữa ăn</option>
                    <option value="appointment">Cuộc hẹn</option>
                    <option value="other">Khác</option>
                </select>
            </div>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu Đề</label>
                <input type="text" id="title" name="title" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-1">Thời Gian</label>
                <input type="time" id="reminder_time" name="reminder_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_recurring" value="1" checked
                           class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700">Lặp lại</span>
                </label>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Tạo
                </button>
                <button type="button" id="close-modal" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                    Hủy
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
                    label: 'Cân Nặng (kg)',
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
                        label: 'Tâm Thu',
                        data: chartData.blood_pressure.map(bp => bp ? bp.systolic : null),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Tâm Trương',
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
                    label: 'Đường Huyết',
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

// Delete consultation session from dashboard
function deleteConsultationFromDashboard(sessionId, button) {
    if (!confirm('Bạn có chắc chắn muốn xóa consultation session này? Hành động này không thể hoàn tác.')) {
        return;
    }

    // Disable button during deletion
    button.disabled = true;
    button.innerHTML = '⏳';

    fetch(`{{ route("ai-consultation.destroy", ":sessionId") }}`.replace(':sessionId', sessionId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the consultation item from DOM
            const item = button.closest('.group');
            if (item) {
                item.style.transition = 'opacity 0.3s';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    // Reload page if no consultations left
                    if (document.querySelectorAll('.delete-consultation-btn').length === 0) {
                        location.reload();
                    }
                }, 300);
            }
        } else {
            alert('Không thể xóa consultation session: ' + (data.message || 'Lỗi không xác định'));
            button.disabled = false;
            button.innerHTML = '🗑️';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi xóa consultation session.');
        button.disabled = false;
        button.innerHTML = '🗑️';
    });
}
</script>
@endsection
