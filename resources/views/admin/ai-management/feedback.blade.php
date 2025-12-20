@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">User Feedback Review</h1>
        <a href="{{ route('admin.ai-management') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to AI Management
        </a>
    </div>

    @if(isset($stats))
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Total Feedback</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Positive</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['positive'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Negative</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['negative'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Avg Rating</p>
            <div class="flex items-center mt-1">
                <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['avg_rating'], 1) }}</p>
                <span class="ml-1 text-xl text-yellow-500">★</span>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
        <form method="GET" action="{{ route('admin.ai-management.feedback') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="bug_report" {{ ($filters['type'] ?? '') == 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                        <option value="feature_request" {{ ($filters['type'] ?? '') == 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                        <option value="general_feedback" {{ ($filters['type'] ?? '') == 'general_feedback' ? 'selected' : '' }}>General Feedback</option>
                        <option value="complaint" {{ ($filters['type'] ?? '') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ ($filters['status'] ?? '') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="resolved" {{ ($filters['status'] ?? '') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ ($filters['status'] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($feedbacks as $feedback)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $feedback->user->name ?? 'Anonymous' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ucfirst(str_replace('_', ' ', $feedback->type)) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ Str::limit($feedback->subject, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($feedback->status == 'resolved') bg-green-100 text-green-800
                                    @elseif($feedback->status == 'reviewed') bg-blue-100 text-blue-800
                                    @elseif($feedback->status == 'closed') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $feedback->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No feedback found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Showing <span class="font-medium">{{ $feedbacks->firstItem() }}</span> to <span class="font-medium">{{ $feedbacks->lastItem() }}</span> of <span class="font-medium">{{ $feedbacks->total() }}</span> results
            </div>
            <div class="flex space-x-2">
                @if($feedbacks->onFirstPage())
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">Previous</button>
                @else
                    <a href="{{ $feedbacks->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</a>
                @endif
                @if($feedbacks->hasMorePages())
                    <a href="{{ $feedbacks->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</a>
                @else
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">Next</button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection




