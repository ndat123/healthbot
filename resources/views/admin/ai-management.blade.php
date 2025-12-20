@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">AI HealthBot Management</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- AI Configuration Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">AI Configuration</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">Active</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <button onclick="document.getElementById('configForm').scrollIntoView({behavior: 'smooth'})" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                    Configure AI
                </button>
            </div>
        </div>

        <!-- Training Scenarios Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Training Scenarios</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $aiConfig['training_scenarios_count'] ?? count($aiConfig['training_scenarios']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.ai-management.scenarios.create') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Scenario
                </a>
            </div>
        </div>

        <!-- Performance Metrics Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Performance</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $aiConfig['performance']['accuracy'] }}%</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.ai-management.metrics') }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Metrics
                </a>
            </div>
        </div>

        <!-- User Feedback Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">User Feedback</h3>
                    <div class="flex items-center mt-1">
                        <p class="text-3xl font-bold text-gray-900">{{ $aiConfig['performance']['user_satisfaction'] }}</p>
                        <span class="ml-1 text-xl text-yellow-500">★</span>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.ai-management.feedback') }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Review Feedback
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- AI Configuration Section -->
        <div id="configForm" class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">AI Configuration</h2>
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('admin.ai-management.config.update') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="consultation-depth" class="block text-sm font-medium text-gray-700 mb-1">Consultation Depth</label>
                    <select id="consultation-depth" name="consultation_depth" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option @if($aiConfig['consultation_depth'] == 'Basic') selected @endif>Basic</option>
                        <option @if($aiConfig['consultation_depth'] == 'Medium') selected @endif>Medium</option>
                        <option @if($aiConfig['consultation_depth'] == 'Advanced') selected @endif>Advanced</option>
                    </select>
                </div>

                <div>
                    <label for="emergency-threshold" class="block text-sm font-medium text-gray-700 mb-1">Emergency Alert Threshold</label>
                    <select id="emergency-threshold" name="emergency_threshold" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option @if($aiConfig['emergency_threshold'] == 'Low') selected @endif>Low</option>
                        <option @if($aiConfig['emergency_threshold'] == 'Medium') selected @endif>Medium</option>
                        <option @if($aiConfig['emergency_threshold'] == 'High') selected @endif>High</option>
                    </select>
                </div>

                <div>
                    <label for="response-language" class="block text-sm font-medium text-gray-700 mb-1">Response Language</label>
                    <select id="response-language" name="response_language" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="English" {{ $aiConfig['response_language'] == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Spanish" {{ $aiConfig['response_language'] == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="French" {{ $aiConfig['response_language'] == 'French' ? 'selected' : '' }}>French</option>
                        <option value="German" {{ $aiConfig['response_language'] == 'German' ? 'selected' : '' }}>German</option>
                        <option value="Chinese" {{ $aiConfig['response_language'] == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- Training Scenarios Section -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Training Scenarios</h2>
                <a href="{{ route('admin.ai-management.scenarios.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Scenario
                </a>
            </div>
            <div class="space-y-4">
                @foreach($aiConfig['training_scenarios'] as $scenario)
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $scenario['scenario'] }}</h3>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($scenario['status'] == 'Trained') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $scenario['status'] }}
                            </span>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            @if($scenario['id'])
                                <a href="{{ route('admin.ai-management.scenarios.edit', $scenario['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit</a>
                                <button onclick="showDeleteConfirm('scenario', {{ $scenario['id'] }}, 'Are you sure you want to delete this training scenario? This action cannot be undone.')" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                <form id="delete-scenario-{{ $scenario['id'] }}" action="{{ route('admin.ai-management.scenarios.delete', $scenario['id']) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @else
                                <span class="text-gray-400 text-sm font-medium">Edit</span>
                                <span class="text-gray-400 text-sm font-medium">Delete</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Performance Metrics Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Performance Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Accuracy Chart -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Accuracy</h3>
                <div class="text-4xl font-bold text-blue-600 mb-2">{{ $aiConfig['performance']['accuracy'] }}%</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $aiConfig['performance']['accuracy'] }}%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Overall accuracy of AI responses</p>
            </div>

            <!-- Response Time Chart -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <h3 class="text-lg font-medium text-gray-800 mb-2">Response Time</h3>
                <div class="text-4xl font-bold text-green-600 mb-2">{{ $aiConfig['performance']['response_time'] }}s</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ 100 - ($aiConfig['performance']['response_time'] * 10) }}%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Average response time for AI</p>
            </div>

            <!-- User Satisfaction Chart -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <h3 class="text-lg font-medium text-gray-800 mb-2">User Satisfaction</h3>
                <div class="flex items-center">
                    <div class="text-4xl font-bold text-yellow-600 mr-2">{{ $aiConfig['performance']['user_satisfaction'] }}</div>
                    <span class="text-xl text-yellow-500">★</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                    <div class="bg-yellow-600 h-2.5 rounded-full" style="width: {{ $aiConfig['performance']['user_satisfaction'] * 20 }}%"></div>
                </div>
                <p class="text-sm text-gray-500 mt-2">User satisfaction rating</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Toast -->
<div id="deleteToast" class="fixed inset-0 bg-black bg-opacity-40 overflow-y-auto h-full w-full z-50 hidden transition-opacity duration-200">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md transform transition-all duration-200 scale-100">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-50">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Confirm Delete</h3>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <p class="text-sm text-gray-600 leading-relaxed" id="deleteMessage"></p>
            </div>
            
            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button onclick="cancelDelete()" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    No
                </button>
                <button onclick="confirmDelete()" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-150">
                    Yes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFormId = null;

function showDeleteConfirm(type, id, message) {
    deleteFormId = `delete-${type}-${id}`;
    document.getElementById('deleteMessage').textContent = message;
    
    const toast = document.getElementById('deleteToast');
    toast.classList.remove('hidden');
    
    // Smooth fade in
    setTimeout(() => {
        toast.classList.add('opacity-100');
    }, 10);
}

function confirmDelete() {
    if (deleteFormId) {
        document.getElementById(deleteFormId).submit();
    }
    cancelDelete();
}

function cancelDelete() {
    const toast = document.getElementById('deleteToast');
    toast.classList.remove('opacity-100');
    
    // Smooth fade out
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 200);
    
    deleteFormId = null;
}

// Close toast when clicking outside
document.getElementById('deleteToast').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelDelete();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteToast').classList.contains('hidden')) {
        cancelDelete();
    }
});
</script>
@endsection