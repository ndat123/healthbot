@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Settings</h1>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="divide-y divide-gray-200">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Site Information</h2>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="site_name" class="block text-sm font-medium text-gray-700">Site Name</label>
                            <div class="mt-1">
                                <input type="text" name="site_name" id="site_name" value="{{ $settings['site_name'] }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="site_description" class="block text-sm font-medium text-gray-700">Site Description</label>
                            <div class="mt-1">
                                <input type="text" name="site_description" id="site_description" value="{{ $settings['site_description'] }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                            <div class="mt-1">
                                <input type="email" name="contact_email" id="contact_email" value="{{ $settings['contact_email'] }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                            <div class="mt-1">
                                <input type="text" name="contact_phone" id="contact_phone" value="{{ $settings['contact_phone'] }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="sm:col-span-6">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <div class="mt-1">
                                <textarea id="address" name="address" rows="3" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">{{ $settings['address'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">System Settings</h2>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="maintenance_mode" class="block text-sm font-medium text-gray-700">Maintenance Mode</label>
                            <div class="mt-1">
                                <select id="maintenance_mode" name="maintenance_mode" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="0" {{ $settings['maintenance_mode'] ? '' : 'selected' }}>Disabled</option>
                                    <option value="1" {{ $settings['maintenance_mode'] ? 'selected' : '' }}>Enabled</option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="registration_enabled" class="block text-sm font-medium text-gray-700">User Registration</label>
                            <div class="mt-1">
                                <select id="registration_enabled" name="registration_enabled" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="0" {{ $settings['registration_enabled'] ? '' : 'selected' }}>Disabled</option>
                                    <option value="1" {{ $settings['registration_enabled'] ? 'selected' : '' }}>Enabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg leading-6 font-medium text-gray-900 mb-4">Consultation Settings</h2>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="consultation_fee" class="block text-sm font-medium text-gray-700">Consultation Fee</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="consultation_fee" id="consultation_fee" value="{{ $settings['consultation_fee'] }}" class="focus:ring-primary focus:border-primary block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" step="0.01">
                                <div class="absolute inset-y-0 right-0 flex items-center">
                                    <label for="currency" class="sr-only">Currency</label>
                                    <select id="currency" name="currency" class="focus:ring-primary focus:border-primary h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md">
                                        <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>USD</option>
                                        <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        <option value="GBP" {{ $settings['currency'] === 'GBP' ? 'selected' : '' }}>GBP</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-4 sm:px-6 flex justify-end">
                    <button type="button" onclick="alert('Settings saved successfully!')" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">Save Settings</button>
                </div>
            </div>
        </div>
    </div>
@endsection