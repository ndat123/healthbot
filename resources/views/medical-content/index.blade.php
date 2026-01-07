@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Kiến Thức Y Tế</h1>
        <p class="text-gray-600">Tìm hiểu thông tin y tế, câu hỏi thường gặp và tài liệu hữu ích</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-{{ Auth::check() ? '4' : '3' }} gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Bài viết</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['knowledge_base_count'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('medical-content.knowledge-base') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    Xem tất cả
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Câu hỏi thường gặp</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['faqs_count'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('medical-content.faqs') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    Xem tất cả
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Mẫu tư vấn</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['templates_count'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        @auth
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Đã đánh dấu</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $bookmarksCount }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('medical-content.bookmarks') }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    Xem tất cả
                    <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @endauth
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <form method="GET" action="{{ route('medical-content.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:w-48">
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="knowledge_base" {{ $type === 'knowledge_base' ? 'selected' : '' }}>Bài viết</option>
                    <option value="faq" {{ $type === 'faq' ? 'selected' : '' }}>Câu hỏi thường gặp</option>
                    <option value="template" {{ $type === 'template' ? 'selected' : '' }}>Mẫu tư vấn</option>
                </select>
            </div>
            <div class="md:w-48">
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                Tìm kiếm
            </button>
        </form>
    </div>

    <!-- Knowledge Base Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Bài viết nổi bật</h2>
            <a href="{{ route('medical-content.knowledge-base') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                Xem tất cả
                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($knowledgeBase as $article)
                <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150 cursor-pointer" onclick="window.location.href='{{ route('medical-content.knowledge-base.show', $article->id) }}'">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $article->title }}</h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            @if($article->tags && is_array($article->tags) && count($article->tags) > 0)
                                @foreach(array_slice($article->tags, 0, 2) as $tag)
                                    <a href="{{ route('medical-content.index', ['tag' => $tag]) }}" onclick="event.stopPropagation();" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors cursor-pointer">{{ $tag }}</a>
                                @endforeach
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">{{ $article->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-8 text-center text-gray-500">
                    <p>Chưa có bài viết nào.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- FAQs Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Câu hỏi thường gặp</h2>
            <a href="{{ route('medical-content.faqs') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                Xem tất cả
                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="space-y-4">
            @forelse($faqs as $faq)
                <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150 cursor-pointer" onclick="window.location.href='{{ route('medical-content.faq.show', $faq->id) }}'">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $faq->title }}</h3>
                            <p class="text-sm text-gray-500">{{ $faq->category ?? 'Chung' }}</p>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p>Chưa có câu hỏi nào.</p>
                </div>
            @endforelse
        </div>
    </div>

    @auth
    <!-- Bookmarks Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center space-x-3">
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <svg class="h-6 w-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Bài viết đã đánh dấu</h2>
                    <p class="text-sm text-gray-500">{{ $bookmarksCount }} bài viết đã đánh dấu</p>
                </div>
            </div>
            <a href="{{ route('medical-content.bookmarks') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                Xem tất cả
                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @if($bookmarks->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($bookmarks as $bookmark)
                    @php
                        $content = $bookmark;
                    @endphp
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150 relative">
                        <button onclick="toggleBookmark({{ $content->id }})" class="absolute top-3 right-3 text-yellow-600 hover:text-yellow-700 z-10" title="Bỏ đánh dấu">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                            </svg>
                        </button>
                        <div class="cursor-pointer" onclick="window.location.href='{{ $content->content_type === 'knowledge_base' ? route('medical-content.knowledge-base.show', $content->id) : ($content->content_type === 'faq' ? route('medical-content.faq.show', $content->id) : '#') }}'">
                            <h3 class="text-lg font-medium text-gray-900 mb-2 pr-8">{{ $content->title }}</h3>
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit(strip_tags($content->content), 100) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex flex-wrap gap-2">
                                    @if($content->tags && is_array($content->tags) && count($content->tags) > 0)
                                        @foreach(array_slice($content->tags, 0, 2) as $tag)
                                            <a href="{{ route('medical-content.index', ['tag' => $tag]) }}" onclick="event.stopPropagation();" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors cursor-pointer">{{ $tag }}</a>
                                        @endforeach
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $content->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <p class="mb-2">Bạn chưa đánh dấu bài viết nào.</p>
                <p class="text-sm text-gray-400">Hãy đánh dấu các bài viết bạn quan tâm để xem lại sau.</p>
            </div>
        @endif
    </div>
    @endauth
</div>

@auth
<script>
function toggleBookmark(id) {
    fetch(`/medical-content/${id}/bookmark`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (!data.is_bookmarked) {
                // Reload page to remove the bookmark from list
                window.location.reload();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endauth
@endsection

