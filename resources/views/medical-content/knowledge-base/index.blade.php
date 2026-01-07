@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Bài viết kiến thức y tế</h1>
        <p class="text-gray-600">Khám phá các bài viết về sức khỏe và y tế</p>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <form method="GET" action="{{ route('medical-content.knowledge-base') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Tìm kiếm bài viết..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                <a href="{{ route('medical-content.knowledge-base') }}" class="text-sm text-blue-600 hover:text-blue-800">Xóa bộ lọc</a>
            </div>
        @endif
    </div>

    <!-- Articles List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($articles as $article)
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition duration-150 cursor-pointer" onclick="window.location.href='{{ route('medical-content.knowledge-base.show', $article->id) }}'">
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $article->title }}</h3>
                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ Str::limit(strip_tags($article->content), 150) }}</p>
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            @if($article->tags && is_array($article->tags) && count($article->tags) > 0)
                                @foreach(array_slice($article->tags, 0, 2) as $tag)
                                    <a href="{{ route('medical-content.knowledge-base', ['tag' => $tag]) }}" onclick="event.stopPropagation();" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors cursor-pointer">{{ $tag }}</a>
                                @endforeach
                            @endif
                        </div>
                        <span class="text-xs text-gray-500">{{ $article->created_at->format('d/m/Y') }}</span>
                    </div>
            </div>
        @empty
            <div class="col-span-3 p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p>Không tìm thấy bài viết nào.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($articles->hasPages())
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection

