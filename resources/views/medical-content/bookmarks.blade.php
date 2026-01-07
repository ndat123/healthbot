@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Bài viết đã đánh dấu</h1>
        <p class="text-gray-600">Xem lại các bài viết bạn đã đánh dấu</p>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <form method="GET" action="{{ route('medical-content.bookmarks') }}" class="flex flex-col md:flex-row gap-4">
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
                <a href="{{ route('medical-content.bookmarks') }}" class="text-sm text-blue-600 hover:text-blue-800">Xóa bộ lọc</a>
            </div>
        @endif
    </div>

    <!-- Bookmarks List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($bookmarks as $bookmark)
            @php
                $content = $bookmark;
            @endphp
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition duration-150">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-lg font-medium text-gray-900 flex-1 cursor-pointer" onclick="window.location.href='{{ $content->content_type === 'knowledge_base' ? route('medical-content.knowledge-base.show', $content->id) : ($content->content_type === 'faq' ? route('medical-content.faq.show', $content->id) : '#') }}'">{{ $content->title }}</h3>
                    <button onclick="toggleBookmark({{ $content->id }})" class="text-yellow-600 hover:text-yellow-700 ml-2">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ Str::limit(strip_tags($content->content), 150) }}</p>
                <div class="flex items-center justify-between">
                    <div class="flex flex-wrap gap-2">
                        @if($content->tags && is_array($content->tags) && count($content->tags) > 0)
                            @foreach(array_slice($content->tags, 0, 2) as $tagItem)
                                <a href="{{ route('medical-content.bookmarks', ['tag' => $tagItem]) }}" class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200 transition-colors cursor-pointer">{{ $tagItem }}</a>
                            @endforeach
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $content->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-8 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                </svg>
                <p>Bạn chưa đánh dấu bài viết nào.</p>
                <a href="{{ route('medical-content.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Khám phá bài viết</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($bookmarks->hasPages())
        <div class="mt-8">
            {{ $bookmarks->links() }}
        </div>
    @endif
</div>

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
@endsection

