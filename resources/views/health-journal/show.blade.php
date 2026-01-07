@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <a href="{{ route('health-journal.index') }}" class="text-orange-600 hover:text-orange-800 inline-block">← Back to Journal</a>
                <form action="{{ route('health-journal.destroy', $journal->id) }}" method="POST" class="inline" id="delete-journal-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        🗑️ Xóa Journal
                    </button>
                </form>
            </div>
            <h1 class="text-4xl font-bold text-gray-900">Journal Entry - {{ $journal->journal_date->format('M d, Y') }}</h1>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8 space-y-6">
            <!-- Risk Level Badge -->
            @if($journal->risk_level !== 'low')
            <div class="p-4 rounded-lg border-l-4 
                {{ $journal->risk_level === 'critical' ? 'bg-red-50 border-red-500' : '' }}
                {{ $journal->risk_level === 'high' ? 'bg-orange-50 border-orange-500' : '' }}
                {{ $journal->risk_level === 'medium' ? 'bg-yellow-50 border-yellow-500' : '' }}">
                <p class="font-semibold {{ $journal->risk_level === 'critical' ? 'text-red-800' : ($journal->risk_level === 'high' ? 'text-orange-800' : 'text-yellow-800') }}">
                    Risk Level: {{ strtoupper($journal->risk_level) }}
                </p>
            </div>
            @endif

            <!-- Symptoms -->
            @if($journal->symptoms)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">📝 Symptoms</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $journal->symptoms }}</p>
            </div>
            @endif

            <!-- Food Diary -->
            @if($journal->food_diary)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">🍽️ Food Diary</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $journal->food_diary }}</p>
            </div>
            @endif

            <!-- Exercise Log -->
            @if($journal->exercise_log)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">💪 Exercise & Activity</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $journal->exercise_log }}</p>
            </div>
            @endif

            <!-- Mood -->
            @if($journal->mood)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">😊 Mood</h2>
                <div class="flex items-center gap-4">
                    <span class="text-3xl">
                        @if($journal->mood === 'excellent') 😄
                        @elseif($journal->mood === 'good') 🙂
                        @elseif($journal->mood === 'okay') 😐
                        @elseif($journal->mood === 'poor') 😔
                        @elseif($journal->mood === 'very_poor') 😢
                        @endif
                    </span>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $journal->mood_label }}</p>
                        @if($journal->mood_score)
                            <p class="text-sm text-gray-600">Score: {{ $journal->mood_score }}/10</p>
                        @endif
                    </div>
                </div>
                @if($journal->mood_notes)
                    <p class="text-gray-700 mt-2">{{ $journal->mood_notes }}</p>
                @endif
            </div>
            @endif

            <!-- Additional Notes -->
            @if($journal->notes)
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-3">📋 Additional Notes</h2>
                <p class="text-gray-700 whitespace-pre-line">{{ $journal->notes }}</p>
            </div>
            @endif

            <!-- AI Warnings -->
            @if($journal->ai_warnings && count($journal->ai_warnings) > 0)
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">⚠️ Health Warnings</h2>
                <div class="space-y-3">
                    @foreach($journal->ai_warnings as $warning)
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
            @if($journal->doctor_recommended)
            <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <h3 class="font-semibold text-red-800 mb-2">🏥 Khuyến nghị Tư vấn Bác sĩ</h3>
                <p class="text-sm text-red-700">{{ $journal->doctor_recommendation_reason ?? 'Dựa trên mục nhật ký sức khỏe của bạn, chúng tôi khuyên bạn nên tư vấn với chuyên gia y tế.' }}</p>
            </div>
            @endif

            <!-- AI Suggestions -->
            @if($journal->ai_suggestions && count($journal->ai_suggestions) > 0)
            <div class="border-t pt-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">💡 Gợi ý Sức khỏe</h2>
                <div class="space-y-2">
                    @foreach($journal->ai_suggestions as $suggestion)
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-sm text-gray-700">{{ str_replace('🤖 AI: ', '', $suggestion['message'] ?? '') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa journal entry này? Hành động này không thể hoàn tác.')) {
        document.getElementById('delete-journal-form').submit();
    }
}
</script>
@endsection

