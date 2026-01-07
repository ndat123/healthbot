<?php

namespace App\Http\Controllers;

use App\Models\HealthJournal;
use App\Models\HealthProfile;
use App\Services\HealthJournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HealthJournalController extends Controller
{
    protected $journalService;

    public function __construct(HealthJournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Show health journal dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get today's journal or create empty one
        $todayJournal = HealthJournal::firstOrNew([
            'user_id' => $user->id,
            'journal_date' => Carbon::today(),
        ]);

        // Get recent journals
        $recentJournals = HealthJournal::where('user_id', $user->id)
            ->orderBy('journal_date', 'desc')
            ->limit(30)
            ->get();

        // Get statistics
        $stats = $this->getStatistics($user->id);

        return view('health-journal.index', compact('todayJournal', 'recentJournals', 'stats'));
    }

    /**
     * Store or update journal entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'journal_date' => 'required|date',
            'symptoms' => 'nullable|string|max:2000',
            'food_diary' => 'nullable|string|max:2000',
            'exercise_log' => 'nullable|string|max:2000',
            'mood' => 'nullable|in:excellent,good,okay,poor,very_poor',
            'mood_score' => 'nullable|integer|min:1|max:10',
            'mood_notes' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();

        // Get or create journal entry
        $journal = HealthJournal::updateOrCreate(
            [
                'user_id' => $user->id,
                'journal_date' => $validated['journal_date'],
            ],
            [
                'symptoms' => $validated['symptoms'] ?? null,
                'food_diary' => $validated['food_diary'] ?? null,
                'exercise_log' => $validated['exercise_log'] ?? null,
                'mood' => $validated['mood'] ?? null,
                'mood_score' => $validated['mood_score'] ?? null,
                'mood_notes' => $validated['mood_notes'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        // Analyze and generate suggestions/warnings
        $analysis = $this->journalService->analyzeJournalEntry($journal, $profile);

        // Update journal with analysis results
        $journal->update([
            'ai_suggestions' => $analysis['suggestions'] ?? [],
            'ai_warnings' => $analysis['warnings'] ?? [],
            'risk_level' => $analysis['risk_level'] ?? 'low',
            'doctor_recommended' => $analysis['doctor_recommended'] ?? false,
            'doctor_recommendation_reason' => $analysis['doctor_recommendation_reason'] ?? null,
        ]);

        return back()->with('success', 'Health journal entry saved successfully!');
    }

    /**
     * Show specific journal entry
     */
    public function show($id)
    {
        $journal = HealthJournal::where('user_id', Auth::id())->findOrFail($id);
        
        return view('health-journal.show', compact('journal'));
    }

    /**
     * Delete journal entry
     */
    public function destroy($id)
    {
        $journal = HealthJournal::where('user_id', Auth::id())->findOrFail($id);
        
        $journalDate = $journal->journal_date->format('d/m/Y');
        $journal->delete();

        return redirect()->route('health-journal.index')
            ->with('success', "Journal entry for {$journalDate} has been deleted successfully.");
    }

    /**
     * Get statistics
     */
    private function getStatistics(int $userId): array
    {
        $last30Days = HealthJournal::where('user_id', $userId)
            ->where('journal_date', '>=', Carbon::today()->subDays(30))
            ->get();

        return [
            'total_entries' => $last30Days->count(),
            'avg_mood_score' => round($last30Days->whereNotNull('mood_score')->avg('mood_score'), 1),
            'high_risk_days' => $last30Days->whereIn('risk_level', ['high', 'critical'])->count(),
            'doctor_recommended_count' => $last30Days->where('doctor_recommended', true)->count(),
            'mood_distribution' => $last30Days->whereNotNull('mood')->groupBy('mood')->map->count(),
        ];
    }
}

