@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Quản Lý Nội Dung Y Tế</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Knowledge Base Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Cơ Sở Kiến Thức</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['knowledge_base_count'] ?? count($content['knowledge_base']) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13c1.168-.777 2.754-1.253 4.5-1.253S19.832 5.477 21 6.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-content.knowledge-base.create') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Thêm Bài Viết Mới
                </a>
            </div>
        </div>

        <!-- FAQs Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Câu Hỏi Thường Gặp</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['faqs_count'] ?? count($content['faqs']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-content.faq.create') }}" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Thêm FAQ Mới
                </a>
            </div>
        </div>

        <!-- Templates Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Mẫu</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['templates_count'] ?? count($content['templates']) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-content.template.create') }}" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Thêm Mẫu Mới
                </a>
            </div>
        </div>

        <!-- Chat Logs Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wide">Nhật Ký Trò Chuyện</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['chat_logs_count'] ?? 0) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.medical-content.chat-logs') }}" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Xem Nhật Ký
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Knowledge Base Section -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Cơ Sở Kiến Thức</h2>
                <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                    Xem Tất Cả
                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                @forelse($content['knowledge_base'] as $article)
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $article['title'] }}</h3>
                                <div class="mt-1 flex flex-wrap gap-2">
                                    @if(isset($article['tags']) && is_array($article['tags']) && count($article['tags']) > 0)
                                        @foreach($article['tags'] as $tag)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">{{ $tag }}</span>
                                        @endforeach
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Không có thẻ</span>
                                    @endif
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if(strtolower($article['status']) == 'published') bg-green-100 text-green-800
                                @elseif(strtolower($article['status']) == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $article['status'] }}
                            </span>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <a href="{{ route('admin.medical-content.knowledge-base.edit', $article['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                            <button onclick="showDeleteConfirm('knowledge-base', {{ $article['id'] }}, 'Bạn có chắc chắn muốn xóa bài viết này? Hành động này không thể hoàn tác.')" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                            <form id="delete-knowledge-base-{{ $article['id'] }}" action="{{ route('admin.medical-content.knowledge-base.delete', $article['id']) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 border border-gray-100 rounded-xl text-center text-gray-500">
                        Không tìm thấy bài viết nào.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- FAQs Section -->
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Câu Hỏi Thường Gặp</h2>
                <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                    Xem Tất Cả
                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                @forelse($content['faqs'] as $faq)
                    <div class="p-4 border border-gray-100 rounded-xl hover:bg-gray-50 transition duration-150">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $faq['question'] }}</h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $faq['category'] ?? 'Chung' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if(strtolower($faq['status']) == 'published') bg-green-100 text-green-800
                                @elseif(strtolower($faq['status']) == 'draft') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $faq['status'] }}
                            </span>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <a href="{{ route('admin.medical-content.faq.edit', $faq['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Sửa</a>
                            <button onclick="showDeleteConfirm('faq', {{ $faq['id'] }}, 'Bạn có chắc chắn muốn xóa FAQ này? Hành động này không thể hoàn tác.')" class="text-red-600 hover:text-red-800 text-sm font-medium">Xóa</button>
                            <form id="delete-faq-{{ $faq['id'] }}" action="{{ route('admin.medical-content.faq.delete', $faq['id']) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-4 border border-gray-100 rounded-xl text-center text-gray-500">
                        Không tìm thấy FAQ nào.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Consultation Templates Section -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mt-8 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Mẫu Tư Vấn</h2>
            <button class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center">
                Xem Tất Cả
                <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Mẫu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chuyên Khoa</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($content['templates'] as $template)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $template['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $template['specialty'] ?? 'Y Học Tổng Quát' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if(strtolower($template['status']) == 'active' || strtolower($template['status']) == 'published') bg-green-100 text-green-800
                                    @elseif(strtolower($template['status']) == 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $template['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                <a href="{{ route('admin.medical-content.template.edit', $template['id']) }}" class="text-blue-600 hover:text-blue-900" title="Sửa Mẫu">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="showDeleteConfirm('template', {{ $template['id'] }}, 'Bạn có chắc chắn muốn xóa mẫu này? Hành động này không thể hoàn tác.')" class="text-red-600 hover:text-red-900" title="Xóa Mẫu">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                                <form id="delete-template-{{ $template['id'] }}" action="{{ route('admin.medical-content.template.delete', $template['id']) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                Không tìm thấy mẫu nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Toast -->
<div id="deleteToast" class="fixed inset-0 bg-black bg-opacity-40 overflow-y-auto h-full w-full z-50 hidden transition-opacity duration-200">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md transform transition-all duration-200 scale-100">
            <!-- Header -->
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-red-50">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Xác Nhận Xóa</h3>
                </div>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-5">
                <p class="text-sm text-gray-600 leading-relaxed" id="deleteMessage"></p>
            </div>
            
            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                <button onclick="cancelDelete()" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150">
                    Không
                </button>
                <button onclick="confirmDelete()" class="px-5 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-150">
                    Có
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFormId = null;

function showDeleteConfirm(type, id, message) {
    deleteFormId = `delete-${type}-${id}`;
    document.getElementById('deleteMessage').textContent = message;
    
    const toast = document.getElementById('deleteToast');
    toast.classList.remove('hidden');
    
    // Smooth fade in
    setTimeout(() => {
        toast.classList.add('opacity-100');
    }, 10);
}

function confirmDelete() {
    if (deleteFormId) {
        document.getElementById(deleteFormId).submit();
    }
    cancelDelete();
}

function cancelDelete() {
    const toast = document.getElementById('deleteToast');
    toast.classList.remove('opacity-100');
    
    // Smooth fade out
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 200);
    
    deleteFormId = null;
}

// Close toast when clicking outside
document.getElementById('deleteToast').addEventListener('click', function(e) {
    if (e.target === this) {
        cancelDelete();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteToast').classList.contains('hidden')) {
        cancelDelete();
    }
});
</script>
@endsection