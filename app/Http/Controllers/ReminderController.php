<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ReminderService;
use App\Models\Reminder;
use Carbon\Carbon;

class ReminderController extends Controller
{
    protected $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    /**
     * Hiển thị danh sách reminders của user
     */
    public function index()
    {
        $user = Auth::user();
        $reminders = $this->reminderService->getUserReminders($user, true);

        return view('reminders.index', compact('reminders'));
    }

    /**
     * Tạo reminder mới
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reminder_type' => 'required|in:health_checkup,appointment',
            'reminder_time' => 'required|date',
            'is_recurring' => 'boolean',
            'reminder_days' => 'nullable|array',
        ]);

        $reminderTime = Carbon::parse($validated['reminder_time']);

        if ($validated['reminder_type'] === 'health_checkup') {
            $reminder = $this->reminderService->createHealthReminder(
                $user,
                $validated['title'],
                $validated['description'] ?? '',
                $reminderTime,
                $validated['reminder_days'] ?? [],
                $validated['is_recurring'] ?? false
            );
        } else {
            $reminder = $this->reminderService->createAppointmentReminder(
                $user,
                $validated['title'],
                $validated['description'] ?? '',
                $reminderTime
            );
        }

        if ($reminder) {
            return back()->with('success', 'Tạo nhắc nhở thành công!');
        }

        return back()->with('error', 'Không thể tạo nhắc nhở. Vui lòng kiểm tra cài đặt của bạn.');
    }

    /**
     * Tắt reminder
     */
    public function deactivate(Reminder $reminder)
    {
        // Kiểm tra quyền
        if ($reminder->user_id !== Auth::id()) {
            abort(403);
        }

        if ($this->reminderService->deactivateReminder($reminder)) {
            return back()->with('success', 'Đã tắt nhắc nhở!');
        }

        return back()->with('error', 'Không thể tắt nhắc nhở.');
    }
}
