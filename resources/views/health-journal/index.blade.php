@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Nhật Ký Sức Khỏe</h1>
            <p class="text-xl text-gray-600">Theo dõi sức khỏe hàng ngày, triệu chứng, thức ăn, tập luyện và tâm trạng của bạn</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Journal Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Today's Entry Form -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        Bản Ghi Hôm Nay - {{ Carbon\Carbon::parse($todayJournal->journal_date ?? now())->format('d/m/Y') }}
                    </h2>

                    <form action="{{ route('health-journal.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="journal_date" value="{{ $todayJournal->journal_date ?? date('Y-m-d') }}">

                        <!-- Symptoms -->
                        <div>
                            <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">
                                📝 Triệu Chứng Hàng Ngày
                            </label>
                            <textarea id="symptoms" name="symptoms" rows="3"
                                      placeholder="Mô tả các triệu chứng bạn đang gặp phải hôm nay..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('symptoms', $todayJournal->symptoms) }}</textarea>
                        </div>

                        <!-- Food Diary -->
                        <div>
                            <label for="food_diary" class="block text-sm font-medium text-gray-700 mb-2">
                                🍽️ Nhật Ký Ăn Uống
                            </label>
                            <textarea id="food_diary" name="food_diary" rows="4"
                                      placeholder="Bạn đã ăn gì hôm nay? Bữa sáng, trưa, tối, đồ ăn vặt..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('food_diary', $todayJournal->food_diary) }}</textarea>
                        </div>

                        <!-- Exercise Log -->
                        <div>
                            <label for="exercise_log" class="block text-sm font-medium text-gray-700 mb-2">
                                💪 Tập Luyện & Hoạt Động
                            </label>
                            <textarea id="exercise_log" name="exercise_log" rows="3"
                                      placeholder="Bạn đã thực hiện những hoạt động thể chất nào hôm nay? (đi bộ, gym, thể thao, v.v.)"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('exercise_log', $todayJournal->exercise_log) }}</textarea>
                        </div>

                        <!-- Mood -->
                        <div>
                            <label for="mood" class="block text-sm font-medium text-gray-700 mb-2">
                                😊 Đánh Giá Tâm Trạng
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                                <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-orange-50 transition-colors {{ old('mood', $todayJournal->mood) === 'excellent' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <input type="radio" name="mood" value="excellent" class="hidden" {{ old('mood', $todayJournal->mood) === 'excellent' ? 'checked' : '' }}>
                                    <span class="text-2xl mb-1">😄</span>
                                    <span class="text-xs font-medium">Tuyệt vời</span>
                                </label>
                                <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-orange-50 transition-colors {{ old('mood', $todayJournal->mood) === 'good' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <input type="radio" name="mood" value="good" class="hidden" {{ old('mood', $todayJournal->mood) === 'good' ? 'checked' : '' }}>
                                    <span class="text-2xl mb-1">🙂</span>
                                    <span class="text-xs font-medium">Tốt</span>
                                </label>
                                <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-orange-50 transition-colors {{ old('mood', $todayJournal->mood) === 'okay' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <input type="radio" name="mood" value="okay" class="hidden" {{ old('mood', $todayJournal->mood) === 'okay' ? 'checked' : '' }}>
                                    <span class="text-2xl mb-1">😐</span>
                                    <span class="text-xs font-medium">Ổn</span>
                                </label>
                                <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-orange-50 transition-colors {{ old('mood', $todayJournal->mood) === 'poor' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <input type="radio" name="mood" value="poor" class="hidden" {{ old('mood', $todayJournal->mood) === 'poor' ? 'checked' : '' }}>
                                    <span class="text-2xl mb-1">😔</span>
                                    <span class="text-xs font-medium">Kém</span>
                                </label>
                                <label class="flex flex-col items-center p-3 border-2 rounded-lg cursor-pointer hover:bg-orange-50 transition-colors {{ old('mood', $todayJournal->mood) === 'very_poor' ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                    <input type="radio" name="mood" value="very_poor" class="hidden" {{ old('mood', $todayJournal->mood) === 'very_poor' ? 'checked' : '' }}>
                                    <span class="text-2xl mb-1">😢</span>
                                    <span class="text-xs font-medium">Rất kém</span>
                                </label>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="mood_score" class="block text-xs text-gray-600 mb-1">Điểm Tâm Trạng (1-10)</label>
                                    <input type="number" id="mood_score" name="mood_score" min="1" max="10"
                                           value="{{ old('mood_score', $todayJournal->mood_score) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                </div>
                                <div>
                                    <label for="mood_notes" class="block text-xs text-gray-600 mb-1">Ghi Chú Tâm Trạng</label>
                                    <input type="text" id="mood_notes" name="mood_notes"
                                           value="{{ old('mood_notes', $todayJournal->mood_notes) }}"
                                           placeholder="Bạn cảm thấy thế nào?"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                📋 Ghi Chú Bổ Sung
                            </label>
                            <textarea id="notes" name="notes" rows="2"
                                      placeholder="Bất kỳ ghi chú nào khác về ngày của bạn..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('notes', $todayJournal->notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-orange-600 to-amber-600 text-white px-6 py-3 rounded-lg hover:from-orange-700 hover:to-amber-700 transition-colors font-semibold">
                            Lưu Bản Ghi Nhật Ký
                        </button>
                    </form>
                </div>

                <!-- AI Suggestions & Warnings -->
                @if($todayJournal->exists && ($todayJournal->ai_suggestions || $todayJournal->ai_warnings))
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Phân Tích & Đề Xuất từ AI</h2>

                    <!-- Warnings -->
                    @if($todayJournal->ai_warnings && count($todayJournal->ai_warnings) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">⚠️ Cảnh Báo Sức Khỏe</h3>
                        <div class="space-y-3">
                            @foreach($todayJournal->ai_warnings as $warning)
                                <div class="p-4 rounded-lg border-l-4 
                                    {{ $warning['level'] === 'critical' ? 'bg-red-50 border-red-500' : '' }}
                                    {{ $warning['level'] === 'high' ? 'bg-orange-50 border-orange-500' : '' }}
                                    {{ $warning['level'] === 'medium' ? 'bg-yellow-50 border-yellow-500' : '' }}
                                    {{ $warning['level'] === 'low' ? 'bg-blue-50 border-blue-500' : '' }}">
                                    <p class="text-sm {{ $warning['level'] === 'critical' ? 'text-red-800' : ($warning['level'] === 'high' ? 'text-orange-800' : 'text-gray-700') }}">
                                        {{ $warning['message'] }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Doctor Recommendation -->
                    @if($todayJournal->doctor_recommended)
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                        <h3 class="font-semibold text-red-800 mb-2">🏥 Đề Xuất Tư Vấn Bác Sĩ</h3>
                        <p class="text-sm text-red-700">{{ $todayJournal->doctor_recommendation_reason ?? 'Dựa trên bản ghi nhật ký sức khỏe của bạn, chúng tôi đề xuất bạn nên tư vấn với chuyên gia y tế.' }}</p>
                    </div>
                    @endif

                    <!-- Suggestions -->
                    @if($todayJournal->ai_suggestions && count($todayJournal->ai_suggestions) > 0)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">💡 Gợi Ý Sức Khỏe</h3>
                        <div class="space-y-2">
                            @foreach($todayJournal->ai_suggestions as $suggestion)
                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                    <p class="text-sm text-gray-700">{{ $suggestion['message'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Risk Level Badge -->
                    <div class="mt-6 pt-6 border-t">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Mức Độ Rủi Ro Tổng Thể:</span>
                            <span class="px-4 py-2 rounded-full text-sm font-semibold
                                {{ $todayJournal->risk_level === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $todayJournal->risk_level === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $todayJournal->risk_level === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $todayJournal->risk_level === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ strtoupper($todayJournal->risk_level) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">30 Ngày Qua</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Tổng Số Bản Ghi</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $stats['total_entries'] }}</p>
                        </div>
                        @if($stats['avg_mood_score'])
                        <div>
                            <p class="text-sm text-gray-600">Điểm Tâm Trạng TB</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $stats['avg_mood_score'] }}/10</p>
                        </div>
                        @endif
                        @if($stats['high_risk_days'] > 0)
                        <div>
                            <p class="text-sm text-gray-600">Ngày Rủi Ro Cao</p>
                            <p class="text-2xl font-bold text-red-600">{{ $stats['high_risk_days'] }}</p>
                        </div>
                        @endif
                        @if($stats['doctor_recommended_count'] > 0)
                        <div>
                            <p class="text-sm text-gray-600">Đề Xuất Bác Sĩ</p>
                            <p class="text-2xl font-bold text-orange-600">{{ $stats['doctor_recommended_count'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Entries -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Bản Ghi Gần Đây</h3>
                    <div class="space-y-3">
                        @forelse($recentJournals->take(7) as $journal)
                            <div class="flex items-center justify-between border-l-4 border-orange-500 pl-3 py-2 hover:bg-orange-50 rounded transition-colors group">
                                <a href="{{ route('health-journal.show', $journal->id) }}" class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $journal->journal_date->format('M d, Y') }}</p>
                                    @if($journal->mood)
                                        <p class="text-xs text-gray-600 mt-1">Tâm trạng: {{ $journal->mood_label }}</p>
                                    @endif
                                    @if($journal->risk_level !== 'low')
                                        <span class="inline-block mt-1 px-2 py-1 text-xs rounded
                                            {{ $journal->risk_level === 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $journal->risk_level === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                            {{ $journal->risk_level === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ strtoupper($journal->risk_level) }}
                                        </span>
                                    @endif
                                </a>
                                <form action="{{ route('health-journal.destroy', $journal->id) }}" method="POST" class="ml-2 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Bạn có chắc chắn muốn xóa journal entry này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Xóa">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-4">Chưa có bản ghi nào</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-select mood on click
document.querySelectorAll('label[for^="mood"]').forEach(label => {
    label.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
            // Update visual state
            document.querySelectorAll('label').forEach(l => {
                if (l !== this) {
                    l.classList.remove('border-orange-500', 'bg-orange-50');
                    l.classList.add('border-gray-200');
                }
            });
            this.classList.add('border-orange-500', 'bg-orange-50');
            this.classList.remove('border-gray-200');
        }
    });
});
</script>
@endsection

