<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    /**
     * Store new reminder
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reminder_type' => 'required|in:medication,water,exercise,meal,appointment,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reminder_time' => 'required|date_format:H:i',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'integer|min:1|max:7',
            'is_recurring' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'reminder_type' => $validated['reminder_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'reminder_time' => $validated['reminder_time'],
            'reminder_days' => $validated['reminder_days'] ?? [1,2,3,4,5,6,7], // All days by default
            'is_recurring' => $validated['is_recurring'] ?? true,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('health-tracking.index')
            ->with('success', 'Reminder created successfully!');
    }

    /**
     * Update reminder
     */
    public function update(Request $request, $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'reminder_type' => 'required|in:medication,water,exercise,meal,appointment,other',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reminder_time' => 'required|date_format:H:i',
            'reminder_days' => 'nullable|array',
            'reminder_days.*' => 'integer|min:1|max:7',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $reminder->update($validated);

        return redirect()->route('health-tracking.index')
            ->with('success', 'Reminder updated successfully!');
    }

    /**
     * Delete reminder
     */
    public function destroy($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);
        $reminder->delete();

        return redirect()->route('health-tracking.index')
            ->with('success', 'Reminder deleted successfully!');
    }

    /**
     * Toggle reminder active status
     */
    public function toggle($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);
        $reminder->is_active = !$reminder->is_active;
        $reminder->save();

        return response()->json([
            'success' => true,
            'is_active' => $reminder->is_active,
        ]);
    }
}

