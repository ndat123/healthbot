@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Câu hỏi thường gặp</h1>
        <p class="text-gray-600">Tìm câu trả lời cho các câu hỏi phổ biến</p>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <form method="GET" action="{{ route('medical-content.faqs') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm câu hỏi..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:w-48">
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:w-48">
                <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                    <option value="views" {{ $sort === 'views' ? 'selected' : '' }}>Xem nhiều nhất</option>
                    <option value="helpful" {{ $sort === 'helpful' ? 'selected' : '' }}>Hữu ích nhất</option>
                    <option value="title" {{ $sort === 'title' ? 'selected' : '' }}>A-Z</option>
                </select>
            </div>
            @if($tag)
                <input type="hidden" name="tag" value="{{ $tag }}">
            @endif
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Tìm kiếm
            </button>
        </form>
        @if($tag)
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-600">Đang lọc theo tag:</span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">{{ $tag }}</span>
                <a href="{{ route('medical-content.faqs') }}" class="text-sm text-blue-600 hover:text-blue-800">Xóa bộ lọc</a>
            </div>
        @endif
    </div>

    <!-- FAQs List -->
    <div class="space-y-4">
        @forelse($faqs as $faq)
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition duration-150 cursor-pointer" onclick="window.location.href='{{ route('medical-content.faq.show', $faq->id) }}'">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $faq->title }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $faq->category ?? 'Chung' }}</p>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit(strip_tags($faq->content), 200) }}</p>
                    </div>
                    <svg class="h-5 w-5 text-gray-400 ml-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Không tìm thấy câu hỏi nào.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($faqs->hasPages())
        <div class="mt-8">
            {{ $faqs->links() }}
        </div>
    @endif
</div>
@endsection

