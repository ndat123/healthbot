@extends('layouts.admin')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">System Administration</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- API Management -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">API Management</h3>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-md font-medium text-gray-700">API Status</h4>
                        <p class="text-sm text-gray-500">HealthBot AI Service</p>
                    </div>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $systemData['api_status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $systemData['api_status'] }}
                    </span>
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('admin.system.restart-api') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm">
                            Restart API
                        </button>
                    </form>
                    <a href="{{ route('admin.system.logs') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm inline-block">
                        View Logs
                    </a>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notification Settings</h3>
                <form action="{{ route('admin.system.notifications.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @foreach($systemData['notifications'] as $notification)
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="{{ $notification['key'] }}" class="form-checkbox h-5 w-5 text-blue-600 rounded" {{ $notification['status'] === 'Enabled' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">{{ $notification['type'] }}</span>
                            </label>
                            <span class="text-xs {{ $notification['status'] === 'Enabled' ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $notification['status'] }}
                            </span>
                        </div>
                    @endforeach
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm transition-colors">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Subscription Plans -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Plans</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($systemData['subscriptions'] as $plan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan['plan'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $plan['users'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.users', ['role' => strtolower($plan['plan'])]) }}" class="text-blue-600 hover:text-blue-900 mr-2">View Users</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="{{ route('admin.users.create') }}" class="mt-4 block text-center w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm">
                    Add New User/Plan
                </a>
            </div>

            <!-- Feedback & Bug Reports -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Feedback & Bug Reports</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    @foreach($systemData['feedback'] as $item)
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h4 class="font-medium text-gray-900">{{ $item['type'] }}</h4>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $item['count'] }}</p>
                            <div class="mt-4">
                                <div class="h-2 bg-gray-200 rounded-full">
                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $item['count'] > 0 ? min(($item['count'] / 10) * 100, 100) : 0 }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('admin.ai-management.feedback', ['type' => str_replace(' ', '_', strtolower($item['type']))]) }}" class="mt-3 block text-center w-full bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-md text-xs">
                                View {{ $item['type'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.ai-management.feedback') }}" class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md text-sm">
                    Manage All Feedback
                </a>
            </div>
        </div>
    </div>
@endsection