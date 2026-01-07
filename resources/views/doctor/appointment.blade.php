@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <a href="{{ route('doctor.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Doctors
        </a>

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

        <!-- Appointment Header -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Appointment Details</h1>
                    <p class="text-gray-600">View your appointment information</p>
                </div>
                <span class="px-4 py-2 text-sm rounded-full font-medium
                    {{ $appointmentData['status'] === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $appointmentData['status'] === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $appointmentData['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                    {{ $appointmentData['status'] === 'rescheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                    {{ ucfirst($appointmentData['status']) }}
                </span>
            </div>

            <!-- Doctor Information -->
            @if($appointmentData['doctor'])
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Doctor Information</h2>
                <div class="flex items-start space-x-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl border-4 border-white shadow-lg">
                        {{ strtoupper(substr($appointmentData['doctor']['name'], 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $appointmentData['doctor']['name'] }}</h3>
                        <p class="text-blue-600 font-medium mb-2">{{ $appointmentData['doctor']['specialization'] }}</p>
                        @if($appointmentData['doctor']['email'])
                            <p class="text-sm text-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                {{ $appointmentData['doctor']['email'] }}
                            </p>
                        @endif
                        @if($appointmentData['doctor']['phone'])
                            <p class="text-sm text-gray-600">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012 2h.093a2 2 0 011.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $appointmentData['doctor']['phone'] }}
                            </p>
                        @endif
                        <a href="{{ route('doctor.show', $appointmentData['doctor']['id']) }}" 
                           class="inline-block mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Doctor Profile →
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Appointment Details -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Appointment Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $appointmentData['date'] }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $appointmentData['time'] }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Type</label>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ ucfirst(str_replace('-', ' ', $appointmentData['type'])) }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consultation Fee</label>
                        <div class="flex items-center text-gray-900">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ${{ number_format($appointmentData['fee'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Symptoms / Reason for Visit -->
        @if($appointmentData['symptoms'])
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Symptoms / Reason for Visit</h2>
            <p class="text-gray-700 leading-relaxed">{{ $appointmentData['symptoms'] }}</p>
        </div>
        @endif

        <!-- Diagnosis -->
        @if($appointmentData['diagnosis'])
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Diagnosis</h2>
            <p class="text-gray-700 leading-relaxed">{{ $appointmentData['diagnosis'] }}</p>
        </div>
        @endif

        <!-- Prescription -->
        @if($appointmentData['prescription'])
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Prescription</h2>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $appointmentData['prescription'] }}</p>
        </div>
        @endif

        <!-- Notes -->
        @if($appointmentData['notes'])
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Additional Notes</h2>
            <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $appointmentData['notes'] }}</p>
        </div>
        @endif

        <!-- Appointment Metadata -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Appointment Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="block text-gray-600 mb-1">Booked On</label>
                    <p class="text-gray-900">{{ $appointmentData['created_at'] }}</p>
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Last Updated</label>
                    <p class="text-gray-900">{{ $appointmentData['updated_at'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



