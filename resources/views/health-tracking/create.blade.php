@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Add Health Tracking Entry</h1>

            <form action="{{ route('health-tracking.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Date -->
                    <div>
                        <label for="tracking_date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" id="tracking_date" name="tracking_date" 
                               value="{{ old('tracking_date', $todayEntry->tracking_date ?? $today) }}" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Weight and Height -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                            <input type="number" id="weight" name="weight" 
                                   value="{{ old('weight', $todayEntry->weight ?? '') }}" 
                                   step="0.1" min="20" max="300"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                            <input type="number" id="height" name="height" 
                                   value="{{ old('height', $todayEntry->height ?? '') }}" 
                                   step="0.1" min="50" max="250"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Blood Pressure -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="blood_pressure_systolic" class="block text-xs text-gray-500 mb-1">Systolic</label>
                                <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" 
                                       value="{{ old('blood_pressure_systolic', $todayEntry->blood_pressure_systolic ?? '') }}" 
                                       min="50" max="250"
                                       placeholder="120"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="blood_pressure_diastolic" class="block text-xs text-gray-500 mb-1">Diastolic</label>
                                <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" 
                                       value="{{ old('blood_pressure_diastolic', $todayEntry->blood_pressure_diastolic ?? '') }}" 
                                       min="30" max="150"
                                       placeholder="80"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blood Sugar and Heart Rate -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-2">Blood Sugar (mg/dL)</label>
                            <input type="number" id="blood_sugar" name="blood_sugar" 
                                   value="{{ old('blood_sugar', $todayEntry->blood_sugar ?? '') }}" 
                                   step="0.1" min="50" max="500"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="heart_rate" class="block text-sm font-medium text-gray-700 mb-2">Heart Rate (bpm)</label>
                            <input type="number" id="heart_rate" name="heart_rate" 
                                   value="{{ old('heart_rate', $todayEntry->heart_rate ?? '') }}" 
                                   min="30" max="220"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Body Temperature -->
                    <div>
                        <label for="body_temperature" class="block text-sm font-medium text-gray-700 mb-2">Body Temperature (°C)</label>
                        <input type="number" id="body_temperature" name="body_temperature" 
                               value="{{ old('body_temperature', $todayEntry->body_temperature ?? '') }}" 
                               step="0.1" min="35" max="42"
                               placeholder="36.5"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="notes" name="notes" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Any additional notes about your health today...">{{ old('notes', $todayEntry->notes ?? '') }}</textarea>
                    </div>

                    <!-- Auto-calculate BMI info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Note:</strong> BMI will be automatically calculated when you enter both weight and height.
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <a href="{{ route('health-tracking.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

