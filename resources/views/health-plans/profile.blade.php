@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Health Profile</h1>

            <form action="{{ route('health-plans.profile.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Age</label>
                                <input type="number" id="age" name="age" value="{{ old('age', $profile->age ?? '') }}" 
                                       min="1" max="120"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select id="gender" name="gender" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    <option value="male" {{ old('gender', $profile->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $profile->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $profile->gender ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                <input type="number" id="height" name="height" value="{{ old('height', $profile->height ?? '') }}" 
                                       step="0.1" min="50" max="250"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" value="{{ old('weight', $profile->weight ?? '') }}" 
                                       step="0.1" min="20" max="300"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Medical Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">Medical History</label>
                                <textarea id="medical_history" name="medical_history" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Previous illnesses, surgeries, chronic conditions...">{{ old('medical_history', $profile->medical_history ?? '') }}</textarea>
                            </div>
                            <div>
                                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                                <input type="text" id="allergies" name="allergies" value="{{ old('allergies', $profile->allergies ?? '') }}" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Separate multiple allergies with commas">
                            </div>
                        </div>
                    </div>

                    <!-- Lifestyle Habits -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Lifestyle Habits</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="exercise_frequency" class="block text-sm font-medium text-gray-700 mb-2">Exercise Frequency</label>
                                <select id="exercise_frequency" name="exercise_frequency" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    @php
                                        $exerciseFreq = old('exercise_frequency', ($profile && $profile->lifestyle_habits && isset($profile->lifestyle_habits['exercise_frequency'])) ? $profile->lifestyle_habits['exercise_frequency'] : '');
                                    @endphp
                                    <option value="none" {{ $exerciseFreq === 'none' ? 'selected' : '' }}>None</option>
                                    <option value="1-2" {{ $exerciseFreq === '1-2' ? 'selected' : '' }}>1-2 times/week</option>
                                    <option value="3-4" {{ $exerciseFreq === '3-4' ? 'selected' : '' }}>3-4 times/week</option>
                                    <option value="5+" {{ $exerciseFreq === '5+' ? 'selected' : '' }}>5+ times/week</option>
                                </select>
                            </div>
                            <div>
                                <label for="sleep_hours" class="block text-sm font-medium text-gray-700 mb-2">Sleep Hours (per night)</label>
                                <input type="number" id="sleep_hours" name="sleep_hours" value="{{ old('sleep_hours', ($profile && $profile->lifestyle_habits && isset($profile->lifestyle_habits['sleep_hours'])) ? $profile->lifestyle_habits['sleep_hours'] : '') }}" 
                                       min="0" max="24"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Smoking</label>
                                <div class="flex items-center">
                                    <input type="checkbox" id="smoking" name="smoking" value="1" 
                                           {{ old('smoking', ($profile && $profile->lifestyle_habits && isset($profile->lifestyle_habits['smoking'])) ? $profile->lifestyle_habits['smoking'] : false) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="smoking" class="ml-2 text-sm text-gray-700">I smoke</label>
                                </div>
                            </div>
                            <div>
                                <label for="alcohol" class="block text-sm font-medium text-gray-700 mb-2">Alcohol Consumption</label>
                                <select id="alcohol" name="alcohol" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    @php
                                        $alcohol = old('alcohol', ($profile && $profile->lifestyle_habits && isset($profile->lifestyle_habits['alcohol'])) ? $profile->lifestyle_habits['alcohol'] : '');
                                    @endphp
                                    <option value="none" {{ $alcohol === 'none' ? 'selected' : '' }}>None</option>
                                    <option value="occasional" {{ $alcohol === 'occasional' ? 'selected' : '' }}>Occasional</option>
                                    <option value="regular" {{ $alcohol === 'regular' ? 'selected' : '' }}>Regular</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Health Goals -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Health Goals</h2>
                        <div class="space-y-2">
                            @php
                                $healthGoals = old('health_goals', ($profile && $profile->health_goals) ? (is_array($profile->health_goals) ? $profile->health_goals : []) : []);
                            @endphp
                            <label class="flex items-center">
                                <input type="checkbox" name="health_goals[]" value="weight_loss" 
                                       {{ in_array('weight_loss', $healthGoals) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Weight Loss</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="health_goals[]" value="muscle_gain" 
                                       {{ in_array('muscle_gain', $healthGoals) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Muscle Gain</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="health_goals[]" value="disease_control" 
                                       {{ in_array('disease_control', $healthGoals) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">Disease Control</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="health_goals[]" value="general_wellness" 
                                       {{ in_array('general_wellness', $healthGoals) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-gray-700">General Wellness</span>
                            </label>
                        </div>
                    </div>

                    <!-- Health Metrics -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Health Metrics (Optional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure (Systolic)</label>
                                <input type="number" id="blood_pressure_systolic" name="blood_pressure_systolic" 
                                       value="{{ old('blood_pressure_systolic', $profile->blood_pressure_systolic ?? '') }}" 
                                       step="1" min="50" max="250"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure (Diastolic)</label>
                                <input type="number" id="blood_pressure_diastolic" name="blood_pressure_diastolic" 
                                       value="{{ old('blood_pressure_diastolic', $profile->blood_pressure_diastolic ?? '') }}" 
                                       step="1" min="30" max="150"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-2">Blood Sugar (mg/dL)</label>
                                <input type="number" id="blood_sugar" name="blood_sugar" 
                                       value="{{ old('blood_sugar', $profile->blood_sugar ?? '') }}" 
                                       step="0.1" min="50" max="500"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4 pt-4">
                        <a href="{{ route('health-plans.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-colors font-semibold">
                            Save Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

