@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <h1 class="text-9xl font-bold text-secondary mb-4">500</h1>
        <h2 class="text-3xl font-semibold mb-4">Server Error</h2>
        <p class="text-xl text-gray-600 mb-8 max-w-md">We're sorry, but something went wrong on our end. Please try again later.</p>
        <a href="/" class="bg-secondary text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">Return to Home</a>
    </div>
@endsection