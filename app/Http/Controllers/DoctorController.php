<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Consultation;
use App\Models\DoctorMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    /**
     * Show doctor consultation page
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get search and filter parameters
        $search = trim($request->input('search', ''));
        $specialization = $request->input('specialization', '');
        $availability = $request->input('availability', '');
        
        // Build query
        $query = Doctor::where('status', 'active')
            ->withCount('reviews');
        
        // Search by name only
        if (!empty($search)) {
            // Escape special LIKE characters
            $escapedSearch = str_replace(['%', '_'], ['\%', '\_'], $search);
            $searchTerm = '%' . $escapedSearch . '%';
            
            $query->where('name', 'LIKE', $searchTerm);
        }
        
        // Filter by specialization
        if (!empty($specialization)) {
            $query->where('specialization', $specialization);
        }
        
        // Get doctors
        $doctors = $query->get()
            ->map(function ($doctor) use ($availability) {
                $availableToday = $doctor->available_today;
                $nextAvailable = $doctor->next_available;
                
                // Filter by availability if specified
                if (!empty($availability)) {
                    if ($availability === 'today' && !$availableToday) {
                        return null;
                    }
                    if ($availability === 'tomorrow' && $availableToday) {
                        return null;
                    }
                }
                
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                    'experience' => $doctor->years_of_experience . ' years',
                    'rating' => $doctor->average_rating,
                    'reviews' => $doctor->reviews_count,
                    'image' => $doctor->avatar,
                    'available_today' => $availableToday,
                    'next_available' => $nextAvailable,
                ];
            })
            ->filter(); // Remove null values

        // Get unique specializations for filter dropdown
        $specializations = Doctor::where('status', 'active')
            ->distinct()
            ->pluck('specialization')
            ->sort()
            ->values();

        // Get user's upcoming appointments
        $upcomingAppointments = Consultation::where('user_id', $user->id)
            ->where('status', 'scheduled')
            ->where(function($query) {
                $query->where('consultation_date', '>', now()->toDateString())
                    ->orWhere(function($q) {
                        $q->where('consultation_date', '=', now()->toDateString())
                          ->where('consultation_time', '>', now()->toTimeString());
                    });
            })
            ->with('doctor')
            ->orderBy('consultation_date', 'asc')
            ->orderBy('consultation_time', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($appointment) {
                $time = is_string($appointment->consultation_time) 
                    ? $appointment->consultation_time 
                    : $appointment->consultation_time->format('H:i:s');
                $formattedTime = date('g:i A', strtotime($time));
                
                return [
                    'id' => $appointment->id,
                    'doctor_name' => $appointment->doctor ? $appointment->doctor->name : 'N/A',
                    'date' => $appointment->consultation_date->format('M d, Y'),
                    'time' => $formattedTime,
                    'status' => ucfirst($appointment->status),
                    'type' => ucfirst(str_replace('-', ' ', $appointment->type)),
                ];
            });
        
        return view('doctor.index', compact('doctors', 'upcomingAppointments', 'specializations', 'search', 'specialization', 'availability'));
    }

    /**
     * Show doctor profile details
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Get doctor from database
        $doctor = Doctor::with('reviews.user')
            ->withCount('reviews')
            ->findOrFail($id);

        // Format doctor data for view
        $doctorData = [
            'id' => $doctor->id,
            'name' => $doctor->name,
            'specialization' => $doctor->specialization,
            'experience' => $doctor->years_of_experience . ' years',
            'rating' => $doctor->average_rating,
            'reviews' => $doctor->reviews_count,
            'image' => $doctor->avatar,
            'available_today' => $doctor->available_today,
            'next_available' => $doctor->next_available,
            'education' => $doctor->education,
            'certifications' => $doctor->certifications ?? [],
            'languages' => $doctor->languages ?? [],
            'bio' => $doctor->bio,
            'specialties' => $doctor->specialties ?? [],
            'hospital_affiliations' => $doctor->hospital_affiliations ?? [],
            'consultation_fee' => $doctor->consultation_fee,
            'available_hours' => $doctor->available_hours,
        ];

        // Get reviews from database
        $reviews = $doctor->reviews()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($review) {
                return [
                    'patient_name' => $review->user->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'date' => $review->created_at->diffForHumans(),
                ];
            });

        return view('doctor.show', ['doctor' => $doctorData, 'reviews' => $reviews]);
    }

    /**
     * Store a new appointment booking
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'consultation_type' => 'required|in:in-person,video,phone',
            'symptoms' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your input and try again.');
        }

        // Get doctor to get consultation fee
        $doctor = Doctor::findOrFail($request->doctor_id);

        // Check if there's already an appointment at the same time
        $existingAppointment = Consultation::where('doctor_id', $request->doctor_id)
            ->where('consultation_date', $request->appointment_date)
            ->where('consultation_time', $request->appointment_time)
            ->where('status', 'scheduled')
            ->first();

        if ($existingAppointment) {
            return back()
                ->withInput()
                ->with('error', 'This time slot is already booked. Please choose another time.');
        }

        // Create consultation
        $consultation = Consultation::create([
            'user_id' => $user->id,
            'doctor_id' => $request->doctor_id,
            'consultation_date' => $request->appointment_date,
            'consultation_time' => $request->appointment_time,
            'status' => 'scheduled',
            'type' => $request->consultation_type,
            'symptoms' => $request->symptoms,
            'fee' => $doctor->consultation_fee,
        ]);

        $formattedDate = date('F d, Y', strtotime($request->appointment_date));
        $formattedTime = date('g:i A', strtotime($request->appointment_time));
        
        return redirect()
            ->route('doctor.index')
            ->with('success', "Appointment booked successfully! Your appointment is scheduled for {$formattedDate} at {$formattedTime}.");
    }

    /**
     * Show appointment details
     */
    public function showAppointment($id)
    {
        $user = Auth::user();
        
        // Get appointment with doctor information
        $appointment = Consultation::where('id', $id)
            ->where('user_id', $user->id)
            ->with('doctor')
            ->firstOrFail();

        // Format appointment data
        $time = is_string($appointment->consultation_time) 
            ? $appointment->consultation_time 
            : $appointment->consultation_time->format('H:i:s');
        $formattedTime = date('g:i A', strtotime($time));

        $appointmentData = [
            'id' => $appointment->id,
            'doctor' => $appointment->doctor ? [
                'id' => $appointment->doctor->id,
                'name' => $appointment->doctor->name,
                'specialization' => $appointment->doctor->specialization,
                'email' => $appointment->doctor->email,
                'phone' => $appointment->doctor->phone,
            ] : null,
            'date' => $appointment->consultation_date->format('F d, Y'),
            'date_raw' => $appointment->consultation_date->format('Y-m-d'),
            'time' => $formattedTime,
            'time_raw' => $time,
            'status' => $appointment->status,
            'type' => $appointment->type,
            'symptoms' => $appointment->symptoms,
            'diagnosis' => $appointment->diagnosis,
            'prescription' => $appointment->prescription,
            'notes' => $appointment->notes,
            'fee' => $appointment->fee,
            'created_at' => $appointment->created_at->format('F d, Y g:i A'),
            'updated_at' => $appointment->updated_at->format('F d, Y g:i A'),
        ];

        return view('doctor.appointment', compact('appointmentData'));
    }

    /**
     * Show chat interface with doctor
     */
    public function chat($id)
    {
        $user = Auth::user();
        
        // Get doctor
        $doctor = Doctor::findOrFail($id);
        
        // Get conversation messages
        $messages = DoctorMessage::where(function($query) use ($user, $id) {
                $query->where('user_id', $user->id)
                      ->where('doctor_id', $id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        DoctorMessage::where('user_id', $user->id)
            ->where('doctor_id', $id)
            ->where('sender_type', 'doctor')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('doctor.chat', compact('doctor', 'messages'));
    }

    /**
     * Send message to doctor
     */
    public function sendMessage(Request $request, $id)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Message is required and must be less than 2000 characters.',
            ], 422);
        }

        // Verify doctor exists
        $doctor = Doctor::findOrFail($id);

        // Create message
        $message = DoctorMessage::create([
            'user_id' => $user->id,
            'doctor_id' => $id,
            'message' => $request->message,
            'sender_type' => 'user',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'formatted_time' => $message->created_at->format('g:i A'),
            ],
        ]);
    }

    /**
     * Get new messages (for polling)
     */
    public function getMessages(Request $request, $id)
    {
        $user = Auth::user();
        $lastMessageId = $request->input('last_message_id', 0);

        $messages = DoctorMessage::where('user_id', $user->id)
            ->where('doctor_id', $id)
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->created_at->format('g:i A'),
                ];
            });

        // Mark new doctor messages as read
        DoctorMessage::where('user_id', $user->id)
            ->where('doctor_id', $id)
            ->where('sender_type', 'doctor')
            ->where('id', '>', $lastMessageId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Show conversations list for doctor
     */
    public function conversations()
    {
        $doctor = Auth::user();
        
        // Check if user is a doctor
        if ($doctor->role !== 'doctor') {
            abort(403, 'Chỉ có bác sĩ mới có thể truy cập trang này.');
        }
        
        // Get doctor record by email
        $doctorRecord = Doctor::where('email', $doctor->email)->first();
        
        if (!$doctorRecord) {
            abort(404, 'Không tìm thấy hồ sơ bác sĩ.');
        }

        // Get all unique users who have conversations with this doctor
        $conversations = DoctorMessage::where('doctor_id', $doctorRecord->id)
            ->with('user')
            ->select('user_id')
            ->distinct()
            ->get()
            ->map(function ($message) use ($doctorRecord) {
                $user = $message->user;
                if (!$user) return null;
                
                // Get last message
                $lastMessage = DoctorMessage::where('doctor_id', $doctorRecord->id)
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();
                
                // Count unread messages from user
                $unreadCount = DoctorMessage::where('doctor_id', $doctorRecord->id)
                    ->where('user_id', $user->id)
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                    'unread_count' => $unreadCount,
                ];
            })
            ->filter()
            ->sortByDesc('last_message_time')
            ->values();

        return view('doctor.conversations', [
            'doctor' => $doctorRecord,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Show chat interface with specific user (for doctor)
     */
    public function conversationWithUser($userId)
    {
        $doctor = Auth::user();
        
        // Check if user is a doctor
        if ($doctor->role !== 'doctor') {
            abort(403, 'Chỉ có bác sĩ mới có thể truy cập trang này.');
        }
        
        // Get doctor record by email
        $doctorRecord = Doctor::where('email', $doctor->email)->first();
        
        if (!$doctorRecord) {
            abort(404, 'Không tìm thấy hồ sơ bác sĩ.');
        }

        // Get user
        $user = \App\Models\User::findOrFail($userId);
        
        // Get conversation messages
        $messages = DoctorMessage::where('doctor_id', $doctorRecord->id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark user messages as read
        DoctorMessage::where('doctor_id', $doctorRecord->id)
            ->where('user_id', $userId)
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('doctor.conversation', compact('doctorRecord', 'user', 'messages'));
    }

    /**
     * Send message to user (from doctor)
     */
    public function sendMessageToUser(Request $request, $userId)
    {
        $doctor = Auth::user();
        
        // Check if user is a doctor
        if ($doctor->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'error' => 'Chỉ có bác sĩ mới có thể gửi tin nhắn.',
            ], 403);
        }
        
        // Get doctor record by email
        $doctorRecord = Doctor::where('email', $doctor->email)->first();
        
        if (!$doctorRecord) {
            return response()->json([
                'success' => false,
                'error' => 'Không tìm thấy hồ sơ bác sĩ.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Message is required and must be less than 2000 characters.',
            ], 422);
        }

        // Verify user exists
        $user = \App\Models\User::findOrFail($userId);

        // Create message
        $message = DoctorMessage::create([
            'user_id' => $userId,
            'doctor_id' => $doctorRecord->id,
            'message' => $request->message,
            'sender_type' => 'doctor',
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'formatted_time' => $message->created_at->format('g:i A'),
            ],
        ]);
    }

    /**
     * Get new messages from user (for polling)
     */
    public function getMessagesFromUser(Request $request, $userId)
    {
        $doctor = Auth::user();
        
        // Check if user is a doctor
        if ($doctor->role !== 'doctor') {
            return response()->json([
                'success' => false,
                'error' => 'Chỉ có bác sĩ mới có thể truy cập.',
            ], 403);
        }
        
        // Get doctor record by email
        $doctorRecord = Doctor::where('email', $doctor->email)->first();
        
        if (!$doctorRecord) {
            return response()->json([
                'success' => false,
                'error' => 'Không tìm thấy hồ sơ bác sĩ.',
            ], 404);
        }

        $lastMessageId = $request->input('last_message_id', 0);

        $messages = DoctorMessage::where('doctor_id', $doctorRecord->id)
            ->where('user_id', $userId)
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                    'formatted_time' => $message->created_at->format('g:i A'),
                ];
            });

        // Mark new user messages as read
        DoctorMessage::where('doctor_id', $doctorRecord->id)
            ->where('user_id', $userId)
            ->where('sender_type', 'user')
            ->where('id', '>', $lastMessageId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}

