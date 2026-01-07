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
</style>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-6 no-print">
        <a href="{{ route('medical-content.faqs') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Quay lại danh sách câu hỏi
        </a>
    </div>

    <article class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $faq->title }}</h1>
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center space-x-4 text-sm text-gray-500">
                    <span>{{ $faq->category ?? 'Chung' }}</span>
                    <span>•</span>
                    <span>{{ $faq->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                    <button onclick="toggleBookmark({{ $faq->id }})" id="bookmark-btn-{{ $faq->id }}" class="flex items-center space-x-1 {{ $isBookmarked ? 'text-yellow-600' : 'text-gray-400' }} hover:text-yellow-600 transition-colors">
                        <svg class="h-5 w-5" fill="{{ $isBookmarked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
                        </svg>
                        <span class="text-xs">{{ $isBookmarked ? 'Đã đánh dấu' : 'Đánh dấu' }}</span>
                    </button>
                    @endauth
                    <!-- Share Dropdown -->
                    <div class="relative inline-block">
                        <button onclick="toggleShareMenu()" class="flex items-center space-x-1 text-green-600 hover:text-green-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            <span class="text-xs">Chia sẻ</span>
                        </button>
                        <div id="share-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <div class="py-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="h-5 w-5 mr-3 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                    Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($faq->title) }}" target="_blank" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-400 transition-colors">
                                    <svg class="h-5 w-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </a>
                                <a href="mailto:?subject={{ urlencode($faq->title) }}&body={{ urlencode(request()->fullUrl()) }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email
                                </a>
                                <button onclick="copyLink()" class="w-full flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="h-5 w-5 mr-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Sao chép link
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Print Button -->
                    <button onclick="window.print()" class="flex items-center space-x-1 text-purple-600 hover:text-purple-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span class="text-xs">In</span>
                    </button>
                </div>
            </div>
        </header>

        @if($faq->tags && is_array($faq->tags) && count($faq->tags) > 0)
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach($faq->tags as $tag)
                    <a href="{{ route('medical-content.faqs', ['tag' => $tag]) }}" class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full hover:bg-blue-200 transition-colors cursor-pointer">{{ $tag }}</a>
                @endforeach
            </div>
        @endif

        <div class="prose max-w-none">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
                <p class="text-lg font-semibold text-gray-800 mb-2">Câu hỏi:</p>
                <p class="text-gray-700">{{ $faq->title }}</p>
            </div>
            <div>
                <p class="text-lg font-semibold text-gray-800 mb-2">Trả lời:</p>
                <div class="text-gray-700">
                    {!! nl2br(e($faq->content)) !!}
                </div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-between">
            <button onclick="markHelpful({{ $faq->id }})" id="helpfulBtn{{ $faq->id }}" class="flex items-center space-x-2 text-blue-600 hover:text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v5m7 10h-2.586a1 1 0 01-.707-.293l-3.414-3.414A1 1 0 006.586 15H4"></path>
                </svg>
                <span id="helpfulCount{{ $faq->id }}">Câu trả lời này hữu ích ({{ $faq->helpful_count ?? 0 }})</span>
            </button>
        </div>
    </article>

    @if($related->count() > 0)
        <div class="mt-8 no-print">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Câu hỏi liên quan</h2>
            <div class="space-y-4">
                @foreach($related as $relatedFaq)
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition duration-150 cursor-pointer" onclick="window.location.href='{{ route('medical-content.faq.show', $relatedFaq->id) }}'">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $relatedFaq->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $relatedFaq->category ?? 'Chung' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 flex items-center space-x-2">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span id="toastMessage"></span>
</div>

<script>
function markHelpful(id) {
    const btn = document.getElementById('helpfulBtn' + id);
    const countSpan = document.getElementById('helpfulCount' + id);
    
    // Disable button ngay lập tức để tránh double click
    btn.disabled = true;
    btn.classList.add('opacity-50', 'cursor-not-allowed');
    
    fetch(`/medical-content/${id}/helpful`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cập nhật số lượng
            countSpan.textContent = `Câu trả lời này hữu ích (${data.helpful_count})`;
            
            // Hiển thị toast notification
            showToast(data.message || 'Cảm ơn bạn đã đánh giá câu trả lời này hữu ích!');
            
            // Đổi màu button để hiển thị đã đánh giá
            btn.classList.remove('text-blue-600', 'hover:text-blue-800');
            btn.classList.add('text-green-600');
        } else {
            // Hiển thị thông báo lỗi
            showToast(data.message || 'Đã xảy ra lỗi. Vui lòng thử lại.', 'error');
            
            // Re-enable button nếu lỗi
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Đã xảy ra lỗi. Vui lòng thử lại.', 'error');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    // Thay đổi màu sắc dựa trên type
    if (type === 'error') {
        toast.classList.remove('bg-green-500');
        toast.classList.add('bg-red-500');
    } else {
        toast.classList.remove('bg-red-500');
        toast.classList.add('bg-green-500');
    }
    
    toastMessage.textContent = message;
    
    // Hiển thị toast
    toast.classList.remove('translate-x-full');
    
    // Ẩn toast sau 3 giây
    setTimeout(() => {
        toast.classList.add('translate-x-full');
    }, 3000);
}

function toggleShareMenu() {
    const menu = document.getElementById('share-menu');
    menu.classList.toggle('hidden');
}

function copyLink() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        showToast('Đã sao chép link vào clipboard!');
        document.getElementById('share-menu').classList.add('hidden');
    }).catch(err => {
        console.error('Failed to copy:', err);
        showToast('Không thể sao chép link. Vui lòng thử lại.', 'error');
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
                showToast('Đã đánh dấu bài viết.');
            } else {
                btn.classList.remove('text-yellow-600');
                btn.classList.add('text-gray-400');
                svg.setAttribute('fill', 'none');
                span.textContent = 'Đánh dấu';
                showToast('Đã bỏ đánh dấu bài viết.');
            }
        } else if (data.message) {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
    });
}
</script>
@endsection

