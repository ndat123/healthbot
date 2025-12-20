<?php

namespace App\Http\Controllers;

use App\Models\NutritionPlan;
use App\Models\HealthProfile;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NutritionController extends Controller
{
    protected $nutritionService;

    public function __construct(NutritionService $nutritionService)
    {
        $this->nutritionService = $nutritionService;
    }

    /**
     * Show nutrition consultations page
     */
    public function index()
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();
        $plans = NutritionPlan::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('nutrition.index', compact('profile', 'plans'));
    }

    /**
     * Generate nutrition plan
     */
    public function generatePlan(Request $request)
    {
        $user = Auth::user();
        $profile = HealthProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return redirect()->route('health-plans.profile')
                ->with('error', 'Vui lòng tạo hồ sơ sức khỏe trước.');
        }

        $validated = $request->validate([
            'duration_days' => 'required|integer|min:7|max:30',
            'dietary_preferences' => 'nullable|string|max:500',
            'allergies_restrictions' => 'nullable|string|max:500',
        ]);

        $preferences = [];
        if ($validated['dietary_preferences'] ?? null) {
            $preferences['dietary_preferences'] = $validated['dietary_preferences'];
        }
        if ($validated['allergies_restrictions'] ?? null) {
            $preferences['allergies_restrictions'] = $validated['allergies_restrictions'];
        }

        try {
            // Lấy health plans cũ để tham khảo (nếu có)
            $existingHealthPlans = \App\Models\HealthPlan::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->get();

            // Generate plan
            $planData = $this->nutritionService->generateNutritionPlan(
                $profile,
                $preferences,
                $validated['duration_days'],
                $existingHealthPlans
            );
        } catch (\Throwable $e) {
            \Log::error('Failed to generate AI nutrition plan', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Không thể tạo kế hoạch dinh dưỡng từ AI. Vui lòng thử lại sau hoặc kiểm tra API Key.');
        }

        // Create nutrition plan
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($validated['duration_days'] - 1);

        $plan = NutritionPlan::create([
            'user_id' => $user->id,
            'health_profile_id' => $profile->id,
            'title' => "Kế hoạch dinh dưỡng {$validated['duration_days']} ngày - " . Carbon::now()->format('M Y'),
            'plan_data' => $planData['plan_data'],
            'duration_days' => $validated['duration_days'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'dietary_preferences' => $validated['dietary_preferences'] ?? null,
            'allergies_restrictions' => $validated['allergies_restrictions'] ?? null,
            'daily_calories' => $planData['daily_calories'],
            'ai_prompt_used' => $planData['ai_prompt'],
            'ai_response' => $planData['ai_response'],
        ]);

        return redirect()->route('nutrition.show', $plan->id)
            ->with('success', 'Kế hoạch dinh dưỡng cá nhân hóa của bạn đã được tạo!');
    }

    /**
     * Show nutrition plan details
     */
    public function show($id)
    {
        $plan = NutritionPlan::where('user_id', Auth::id())->findOrFail($id);
        $profile = $plan->healthProfile;

        return view('nutrition.show', compact('plan', 'profile'));
    }
}

