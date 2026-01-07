@extends('layouts.app')

@section('content')
<style>
@media print {
    header, nav, footer, .no-print {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    article {
        box-shadow: none !important;
        border: none !important;
        padding: 20px !important;
    }
    body {
        background: white !important;
    }
}

.article-content {
    line-height: 1.8;
    font-size: 1.1rem;
    color: #374151;
}

.article-content p {
    margin-bottom: 1.5rem;
}

.article-content ul, .article-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.article-content li {
    margin-bottom: 0.75rem;
}

.article-content strong {
    color: #1f2937;
    font-weight: 600;
}

.article-content h2, .article-content h3 {
    color: #111827;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.article-content h2 {
    font-size: 1.75rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.article-content h3 {
    font-size: 1.5rem;
}
</style>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <!-- Breadcrumb -->
        <div class="mb-6 no-print">
            <nav class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('medical-content.index') }}" class="hover:text-blue-600 transition-colors">Trang chủ</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('medical-content.knowledge-base') }}" class="hover:text-blue-600 transition-colors">Kiến thức y tế</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ Str::limit($article->title, 50) }}</span>
            </nav>
        </div>

        <!-- Main Article Card -->
        <article class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100 mb-8">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-6 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="inline-block px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm font-medium mb-3">
                            {{ $article->category ?? 'Chung' }}
                        </div>
                        <h1 class="text-4xl font-bold mb-4 leading-tight">{{ $article->title }}</h1>
                        <div class="flex items-center space-x-4 text-blue-100 text-sm">
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $article->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>{{ number_format($article->views_count ?? 0) }} lượt xem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="px-8 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-3">
                        @auth
                        <button onclick="toggleBookmark({{ $article->id }})" id="bookmark-btn-{{ $article->id }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ $isBookmarked ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600 hover:bg-yellow-50 hover:text-yellow-600' }} transition-all duration-200">
                            <svg class="h-5 w-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ $isBookmarked ? 'Đã đánh dấu' : 'Đánh dấu' }}</span>
                        </button>
                        @endauth
                        <button onclick="markHelpful({{ $article->id }})" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v5m7 10h-2.586a1 1 0 01-.707-.293l-3.414-3.414A1 1 0 006.586 15H4"/>
                            </svg>
                            <span class="text-sm font-medium">Hữu ích ({{ $article->helpful_count ?? 0 }})</span>
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Share Dropdown -->
                        <div class="relative inline-block">
                            <button onclick="toggleShareMenu()" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 transition-all duration-200">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                <span class="text-sm font-medium">Chia sẻ</span>
                            </button>
                            <div id="share-menu" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 z-50 overflow-hidden">
                                <div class="py-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="h-5 w-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        <span class="font-medium">Facebook</span>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}" target="_blank" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors">
                                        <svg class="h-5 w-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        <span class="font-medium">Twitter</span>
                                    </a>
                                    <a href="mailto:?subject={{ urlencode($article->title) }}&body={{ urlencode(request()->fullUrl()) }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium">Email</span>
                                    </a>
                                    <button onclick="copyLink()" class="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                                        <svg class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium">Sao chép link</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Print Button -->
                        <button onclick="window.print()" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span class="text-sm font-medium">In</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-8">
                @if($article->tags && is_array($article->tags) && count($article->tags) > 0)
                    <div class="mb-8 flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                            <a href="{{ route('medical-content.knowledge-base', ['tag' => $tag]) }}" class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 text-sm font-semibold rounded-full hover:from-blue-200 hover:to-indigo-200 transition-all duration-200 shadow-sm hover:shadow-md">
                                #{{ $tag }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="article-content">
                    {!! nl2br(e($article->content)) !!}
                </div>
            </div>
        </article>

        <!-- Related Articles -->
        @if($related->count() > 0)
            <div class="mt-12 no-print">
                <div class="flex items-center mb-6">
                    <div class="h-1 w-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full mr-4"></div>
                    <h2 class="text-3xl font-bold text-gray-900">Bài viết liên quan</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($related as $relatedArticle)
                        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 cursor-pointer transform hover:-translate-y-1" onclick="window.location.href='{{ route('medical-content.knowledge-base.show', $relatedArticle->id) }}'">
                            <div class="p-6">
                                <div class="flex items-center mb-3">
                                    <div class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full">
                                        {{ $relatedArticle->category ?? 'Chung' }}
                                    </div>
                                    <span class="ml-auto text-xs text-gray-500">{{ $relatedArticle->created_at->format('d/m/Y') }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2">{{ $relatedArticle->title }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ Str::limit(strip_tags($relatedArticle->content), 100) }}</p>
                                <div class="flex items-center text-blue-600 font-medium text-sm group-hover:text-blue-700">
                                    <span>Đọc thêm</span>
                                    <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function markHelpful(id) {
    fetch(`/medical-content/${id}/helpful`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cảm ơn bạn đã đánh giá bài viết này hữu ích!');
        }
    })
    .catch(error => console.error('Error:', error));
}

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
            const btn = document.getElementById(`bookmark-btn-${id}`);
            const svg = btn.querySelector('svg');
            const span = btn.querySelector('span');
            
            if (data.is_bookmarked) {
                btn.classList.remove('text-gray-400');
                btn.classList.add('text-yellow-600');
                svg.setAttribute('fill', 'currentColor');
                span.textContent = 'Đã đánh dấu';
            } else {
                btn.classList.remove('text-yellow-600');
                btn.classList.add('text-gray-400');
                svg.setAttribute('fill', 'none');
                span.textContent = 'Đánh dấu';
            }
        } else if (data.message) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
    });
}

function toggleShareMenu() {
    const menu = document.getElementById('share-menu');
    menu.classList.toggle('hidden');
}

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Đã sao chép link vào clipboard!');
        document.getElementById('share-menu').classList.add('hidden');
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Không thể sao chép link. Vui lòng thử lại.');
    });
}

// Đóng menu share khi click bên ngoài
document.addEventListener('click', function(event) {
    const shareMenu = document.getElementById('share-menu');
    const shareButton = event.target.closest('button[onclick="toggleShareMenu()"]');
    if (!shareMenu.contains(event.target) && !shareButton) {
        shareMenu.classList.add('hidden');
    }
});
</script>
@endsection

