@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">My Profile</h1>
            <p class="text-xl text-gray-600">Manage your account information and settings</p>
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

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Basic Information</h2>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar -->
                        <div class="text-center mb-6">
                            <div class="inline-block relative">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                                @else
                                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-500">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <label for="avatar" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full cursor-pointer hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </label>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                <select id="gender" name="gender"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select...</option>
                                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea id="address" name="address" rows="2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('address', $user->address) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Change Password</h2>
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" id="password" name="password" required minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Settings Section -->
                <div id="settings" class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Settings</h2>
                    <form action="{{ route('profile.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Notifications -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Notifications</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="email_notifications" class="text-sm font-medium text-gray-700">Email Notifications</label>
                                        <p class="text-xs text-gray-500">Receive notifications via email</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                               {{ old('email_notifications', $settings->email_notifications ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="sms_notifications" class="text-sm font-medium text-gray-700">SMS Notifications</label>
                                        <p class="text-xs text-gray-500">Receive notifications via SMS</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="sms_notifications" name="sms_notifications" value="1" 
                                               {{ old('sms_notifications', $settings->sms_notifications ?? false) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="health_reminders" class="text-sm font-medium text-gray-700">Health Reminders</label>
                                        <p class="text-xs text-gray-500">Get reminders for health checkups</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="health_reminders" name="health_reminders" value="1" 
                                               {{ old('health_reminders', $settings->health_reminders ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="appointment_reminders" class="text-sm font-medium text-gray-700">Appointment Reminders</label>
                                        <p class="text-xs text-gray-500">Get reminders for appointments</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="appointment_reminders" name="appointment_reminders" value="1" 
                                               {{ old('appointment_reminders', $settings->appointment_reminders ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="newsletter_subscription" class="text-sm font-medium text-gray-700">Newsletter Subscription</label>
                                        <p class="text-xs text-gray-500">Subscribe to our newsletter</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="newsletter_subscription" name="newsletter_subscription" value="1" 
                                               {{ old('newsletter_subscription', $settings->newsletter_subscription ?? false) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Preferences -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                    <select id="language" name="language" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="en" {{ old('language', $settings->language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="vi" {{ old('language', $settings->language ?? 'en') == 'vi' ? 'selected' : '' }}>Tiếng Việt</option>
                                        <option value="es" {{ old('language', $settings->language ?? 'en') == 'es' ? 'selected' : '' }}>Español</option>
                                        <option value="fr" {{ old('language', $settings->language ?? 'en') == 'fr' ? 'selected' : '' }}>Français</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                    <select id="timezone" name="timezone" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="UTC" {{ old('timezone', $settings->timezone ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Asia/Ho_Chi_Minh" {{ old('timezone', $settings->timezone ?? 'UTC') == 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>Asia/Ho Chi Minh (GMT+7)</option>
                                        <option value="America/New_York" {{ old('timezone', $settings->timezone ?? 'UTC') == 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                        <option value="Europe/London" {{ old('timezone', $settings->timezone ?? 'UTC') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Privacy</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="privacy_level" class="block text-sm font-medium text-gray-700 mb-2">Privacy Level</label>
                                    <select id="privacy_level" name="privacy_level" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="private" {{ old('privacy_level', $settings->privacy_level ?? 'private') == 'private' ? 'selected' : '' }}>Private</option>
                                        <option value="friends" {{ old('privacy_level', $settings->privacy_level ?? 'private') == 'friends' ? 'selected' : '' }}>Friends Only</option>
                                        <option value="public" {{ old('privacy_level', $settings->privacy_level ?? 'private') == 'public' ? 'selected' : '' }}>Public</option>
                                    </select>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="share_health_data" class="text-sm font-medium text-gray-700">Share Health Data</label>
                                        <p class="text-xs text-gray-500">Allow sharing anonymized health data for research</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="share_health_data" name="share_health_data" value="1" 
                                               {{ old('share_health_data', $settings->share_health_data ?? false) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label for="allow_ai_learning" class="text-sm font-medium text-gray-700">Allow AI Learning</label>
                                        <p class="text-xs text-gray-500">Help improve AI by allowing it to learn from your interactions</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="allow_ai_learning" name="allow_ai_learning" value="1" 
                                               {{ old('allow_ai_learning', $settings->allow_ai_learning ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Member Since</p>
                            <p class="font-semibold text-gray-800">{{ $user->created_at->format('M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Last Login</p>
                            <p class="font-semibold text-gray-800">{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Never' }}</p>
                        </div>
                        @if($user->role)
                        <div>
                            <p class="text-gray-600">Role</p>
                            <p class="font-semibold text-gray-800">{{ ucfirst($user->role) }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="{{ route('health-plans.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Health Plans</a>
                        <a href="{{ route('health-journal.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Health Journal</a>
                        <a href="{{ route('health-tracking.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Health Tracking</a>
                        <a href="{{ route('nutrition.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">Nutrition Plans</a>
                        <a href="{{ route('ai-consultation.index') }}" class="block text-blue-600 hover:text-blue-800 transition-colors">AI Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

