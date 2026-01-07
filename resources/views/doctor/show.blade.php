@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('doctor.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Doctors
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Doctor Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-6">
                    <!-- Doctor Avatar & Basic Info -->
                    <div class="text-center mb-6">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-white shadow-lg mx-auto mb-4">
                            {{ strtoupper(substr($doctor['name'], 0, 1)) }}
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $doctor['name'] }}</h1>
                        <p class="text-lg text-blue-600 font-medium mb-4">{{ $doctor['specialization'] }}</p>
                        
                        <!-- Rating -->
                        <div class="flex items-center justify-center space-x-2 mb-4">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= floor($doctor['rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-gray-700 font-semibold">{{ $doctor['rating'] }}</span>
                            <span class="text-gray-500 text-sm">({{ $doctor['reviews'] }} reviews)</span>
                        </div>

                        <!-- Experience -->
                        <div class="flex items-center justify-center text-gray-600 mb-4">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $doctor['experience'] }} experience
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Available Hours</p>
                                <p class="text-sm text-gray-600">{{ $doctor['available_hours'] }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Consultation Fee</p>
                                <p class="text-lg font-bold text-blue-600">${{ number_format($doctor['consultation_fee'], 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Languages</p>
                                <p class="text-sm text-gray-600">{{ implode(', ', $doctor['languages']) }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Next Available</p>
                                <p class="text-sm text-green-600 font-medium">{{ $doctor['next_available'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Book Appointment Button -->
                    <button onclick="openBookingModal({{ $doctor['id'] }}, '{{ $doctor['name'] }}', '{{ $doctor['specialization'] }}')" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-semibold text-lg mb-3">
                        Book Appointment
                    </button>
                    
                    <!-- Chat Button -->
                    <a href="{{ route('doctor.chat', $doctor['id']) }}" 
                       class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-lg hover:from-green-700 hover:to-emerald-700 transition-colors font-semibold text-lg flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Chat with Doctor
                    </a>
                </div>
            </div>

            <!-- Right Column - Detailed Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About Section -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">About</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $doctor['bio'] }}</p>
                </div>

                <!-- Education & Certifications -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Education & Certifications</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Education</h3>
                            <p class="text-gray-700">{{ $doctor['education'] }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 mb-2">Certifications</h3>
                            <ul class="space-y-2">
                                @foreach($doctor['certifications'] as $cert)
                                    <li class="flex items-center text-gray-700">
                                        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $cert }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Specialties -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Specialties</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($doctor['specialties'] as $specialty)
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $specialty }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Hospital Affiliations -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Hospital Affiliations</h2>
                    <ul class="space-y-3">
                        @foreach($doctor['hospital_affiliations'] as $hospital)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                {{ $hospital }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Patient Reviews</h2>
                    <div class="space-y-6">
                        @foreach($reviews as $review)
                            <div class="border-b border-gray-200 pb-6 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $review['patient_name'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $review['date'] }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-gray-700">{{ $review['comment'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
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
</script>
@endsection

