@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Chat Logs</h1>
        <a href="{{ route('admin.medical-content') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Medical Content
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.medical-content.chat-logs') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ $filters['search'] ?? '' }}" 
                           placeholder="Search messages, topics..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                </div>
                <div>
                    <select name="emergency_level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">All Emergency Levels</option>
                        <option value="low" {{ ($filters['emergency_level'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ ($filters['emergency_level'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ ($filters['emergency_level'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="critical" {{ ($filters['emergency_level'] ?? '') == 'critical' ? 'selected' : '' }}>Critical</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Topic</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Message</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emergency Level</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Unknown' }}</div>
                                <div class="text-sm text-gray-500">{{ $log->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $log->topic ?? 'General' }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($log->consultation_type ?? 'general') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ Str::limit($log->user_message, 100) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($log->emergency_level == 'critical') bg-red-100 text-red-800
                                    @elseif($log->emergency_level == 'high') bg-orange-100 text-orange-800
                                    @elseif($log->emergency_level == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($log->emergency_level ?? 'low') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showLogDetails({{ $log->id }})" class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                No chat logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ $logs->firstItem() }}</span> to <span class="font-medium">{{ $logs->lastItem() }}</span> of <span class="font-medium">{{ $logs->total() }}</span> results
            </div>
            <div class="flex space-x-2">
                @if($logs->onFirstPage())
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                        Previous
                    </button>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                @endif

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                @else
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                        Next
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal for Log Details -->
<div id="logModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Chat Log Details</h3>
                <button onclick="closeLogModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="logDetails" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showLogDetails(logId) {
    // Fetch log details via AJAX
    fetch(`{{ url('/admin/medical-content/chat-logs') }}/${logId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('logDetails').innerHTML = `
                <div>
                    <h4 class="font-semibold text-gray-700">User:</h4>
                    <p class="text-gray-900">${data.user_name || 'Unknown'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Topic:</h4>
                    <p class="text-gray-900">${data.topic || 'General'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">User Message:</h4>
                    <p class="text-gray-900 whitespace-pre-wrap">${data.user_message || 'N/A'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">AI Response:</h4>
                    <p class="text-gray-900 whitespace-pre-wrap">${data.ai_response || 'N/A'}</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Emergency Level:</h4>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${getEmergencyColor(data.emergency_level)}">
                        ${data.emergency_level ? data.emergency_level.charAt(0).toUpperCase() + data.emergency_level.slice(1) : 'Low'}
                    </span>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700">Date:</h4>
                    <p class="text-gray-900">${data.created_at || 'N/A'}</p>
                </div>
            `;
            document.getElementById('logModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load log details');
        });
}

function closeLogModal() {
    document.getElementById('logModal').classList.add('hidden');
}

function getEmergencyColor(level) {
    switch(level) {
        case 'critical': return 'bg-red-100 text-red-800';
        case 'high': return 'bg-orange-100 text-orange-800';
        case 'medium': return 'bg-yellow-100 text-yellow-800';
        default: return 'bg-green-100 text-green-800';
    }
}
</script>
@endsection

