@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 to-cyan-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Admin Login
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-2xl rounded-3xl sm:px-10 border border-teal-100">
            <div class="mb-6 p-4 bg-teal-50 rounded-xl border border-teal-200">
                <h3 class="text-lg font-medium text-teal-800 mb-2">Available Admin Accounts</h3>
                <div class="space-y-2">
                    @foreach($credentials as $email => $credential)
                        <div class="flex justify-between items-center p-2 bg-white rounded-lg border border-teal-100">
                            <div>
                                <p class="text-sm font-medium text-teal-700">{{ $credential['name'] }}</p>
                                <p class="text-xs text-teal-500">{{ $email }}</p>
                            </div>
                            <p class="text-sm font-mono text-teal-600 bg-teal-50 px-2 py-1 rounded">{{ $credential['password'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 rounded-xl border border-red-200">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-teal-600 to-cyan-600 hover:from-teal-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-300 transform hover:-translate-y-0.5">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection