@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Find Your Doctor</h1>
            <p class="text-xl text-gray-600">Book an appointment with our experienced healthcare professionals</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form method="GET" action="{{ route('doctor.index') }}" id="searchForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Doctor</label>
                        <input type="text" id="search" name="search" value="{{ old('search', $search) }}" 
                               placeholder="Search by name or specialization..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                        <select id="specialization" name="specialization" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Specializations</option>
                            @foreach($specializations as $spec)
                                <option value="{{ $spec }}" {{ old('specialization', $specialization) == $spec ? 'selected' : '' }}>
                                    {{ $spec }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">Availability</label>
                        <select id="availability" name="availability" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All</option>
                            <option value="today" {{ old('availability', $availability) == 'today' ? 'selected' : '' }}>Available Today</option>
                            <option value="tomorrow" {{ old('availability', $availability) == 'tomorrow' ? 'selected' : '' }}>Available Tomorrow</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search
                        </button>
                    </div>
                </div>
                @if($search || $specialization || $availability)
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span>Found {{ $doctors->count() }} doctor(s)</span>
                        @if($search || $specialization || $availability)
                            <span class="ml-2">with filters applied</span>
                        @endif
                    </div>
                    <a href="{{ route('doctor.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Doctors Grid -->
        @if($doctors->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($doctors as $doctor)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <!-- Doctor Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg">
                                    {{ strtoupper(substr($doctor['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $doctor['name'] }}</h3>
                                    <p class="text-sm text-blue-600 font-medium">{{ $doctor['specialization'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Info -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $doctor['experience'] }} experience
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                {{ $doctor['rating'] }} ({{ $doctor['reviews'] }} reviews)
                            </div>
                            <div class="flex items-center text-sm {{ $doctor['available_today'] ? 'text-green-600' : 'text-gray-600' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $doctor['next_available'] }}
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col space-y-2 mt-6">
                            <button onclick="openBookingModal({{ $doctor['id'] }}, '{{ $doctor['name'] }}', '{{ $doctor['specialization'] }}')" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-medium">
                                Book Appointment
                            </button>
                            <div class="flex space-x-2">
                                <a href="{{ route('doctor.chat', $doctor['id']) }}" 
                                        class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-2 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors font-medium text-center text-sm">
                                    Chat
                                </a>
                                <a href="{{ route('doctor.show', $doctor['id']) }}" 
                                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors inline-block text-center text-sm">
                                    Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No doctors found</h3>
            <p class="text-gray-600 mb-6">Try adjusting your search criteria or filters.</p>
            <a href="{{ route('doctor.index') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Clear Filters
            </a>
        </div>
        @endif

        <!-- Upcoming Appointments Section -->
        @if(count($upcomingAppointments) > 0)
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Upcoming Appointments</h2>
            <div class="space-y-4">
                @foreach($upcomingAppointments as $appointment)
                    <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $appointment['doctor_name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $appointment['date'] }} at {{ $appointment['time'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $appointment['type'] }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ $appointment['status'] }}
                            </span>
                            <a href="{{ route('doctor.appointment.show', $appointment['id']) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-900">Book Appointment</h3>
            <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div id="doctorInfo" class="mb-4 p-4 bg-blue-50 rounded-lg">
            <p class="font-semibold text-gray-900" id="doctorName"></p>
            <p class="text-sm text-blue-600" id="doctorSpecialization"></p>
        </div>

        <form id="bookingForm" method="POST" action="{{ route('doctor.booking.store') }}">
            <input type="hidden" id="doctorId" name="doctor_id">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" id="appointment_date" name="appointment_date" required
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-2">Select Time</label>
                    <select id="appointment_time" name="appointment_time" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose a time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                        <option value="17:00">5:00 PM</option>
                    </select>
                </div>
                
                <div>
                    <label for="consultation_type" class="block text-sm font-medium text-gray-700 mb-2">Consultation Type</label>
                    <select id="consultation_type" name="consultation_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="in-person">In-Person</option>
                        <option value="video">Video Call</option>
                        <option value="phone">Phone Call</option>
                    </select>
                </div>
                
                <div>
                    <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">Symptoms / Reason for Visit</label>
                    <textarea id="symptoms" name="symptoms" rows="3" placeholder="Describe your symptoms or reason for consultation..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <div class="flex space-x-3 mt-6">
                <button type="button" onclick="closeBookingModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-medium">
                    Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBookingModal(doctorId, doctorName, doctorSpecialization) {
        document.getElementById('doctorId').value = doctorId;
        document.getElementById('doctorName').textContent = doctorName;
        document.getElementById('doctorSpecialization').textContent = doctorSpecialization;
        document.getElementById('bookingModal').classList.remove('hidden');
    }

    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
        document.getElementById('bookingForm').reset();
    }

    // Form will submit normally, no need for preventDefault

    // Auto-submit form when select fields change (optional - for better UX)
    document.getElementById('specialization').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });

    document.getElementById('availability').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });

    // Optional: Submit on Enter key in search field
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });
</script>
@endsection

