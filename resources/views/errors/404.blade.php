@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <h1 class="text-9xl font-bold text-primary mb-4">404</h1>
        <h2 class="text-3xl font-semibold mb-4">Page Not Found</h2>
        <p class="text-xl text-gray-600 mb-8 max-w-md">Sorry, the page you're looking for doesn't exist or has been moved.</p>
        <a href="/" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">Return to Home</a>
    </div>
@endsection