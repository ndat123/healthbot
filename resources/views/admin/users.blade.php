@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Quản Lý Người Dùng</h1>

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

    @if(isset($stats))
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Tổng Người Dùng</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Người Dùng Hoạt Động</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Người Dùng Premium</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $stats['by_role']['premium'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Bác Sĩ</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['by_role']['doctor'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Danh Sách Người Dùng</h2>
            <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300 flex items-center">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Thêm Người Dùng Mới
            </a>
        </div>

        <!-- Search and Filters -->
        <form method="GET" action="{{ route('admin.users') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Tìm theo tên hoặc email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất Cả Vai Trò</option>
                        <option value="user" {{ ($filters['role'] ?? '') == 'user' ? 'selected' : '' }}>Người Dùng</option>
                        <option value="premium" {{ ($filters['role'] ?? '') == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="doctor" {{ ($filters['role'] ?? '') == 'doctor' ? 'selected' : '' }}>Bác Sĩ</option>
                        <option value="admin" {{ ($filters['role'] ?? '') == 'admin' ? 'selected' : '' }}>Quản Trị</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tất Cả Trạng Thái</option>
                        <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Hoạt Động</option>
                        <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Không Hoạt Động</option>
                        <option value="locked" {{ ($filters['status'] ?? '') == 'locked' ? 'selected' : '' }}>Đã Khóa</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        Lọc
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai Trò</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng Thái</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày Tạo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['id'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user['name'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['email'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user['role'] == 'Doctor') bg-green-100 text-green-800
                                    @elseif($user['role'] == 'Premium') bg-purple-100 text-purple-800
                                    @elseif($user['role'] == 'Admin') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $user['role'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($user['status'] == 'Active') bg-green-100 text-green-800
                                    @elseif($user['status'] == 'Inactive') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user['created_at'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                <button onclick="viewUser({{ $user['id'] }})" class="text-blue-600 hover:text-blue-900" title="Xem Hồ Sơ">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('admin.users.edit', $user['id']) }}" class="text-green-600 hover:text-green-900" title="Chỉnh Sửa">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.users.delete', $user['id']) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Hành động này không thể hoàn tác.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.toggle-lock', $user['id']) }}" method="POST" class="inline">
                                    @csrf
                                    @if($user['status'] == 'Active')
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" title="Khóa Tài Khoản">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </button>
                                    @else
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Mở Khóa Tài Khoản">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                Không tìm thấy người dùng nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($pagination) && $pagination->hasPages())
        <div class="mt-6 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Hiển thị <span class="font-medium">{{ $pagination->firstItem() }}</span> đến <span class="font-medium">{{ $pagination->lastItem() }}</span> trong tổng số <span class="font-medium">{{ $pagination->total() }}</span> kết quả
            </div>
            <div class="flex space-x-2">
                @if($pagination->onFirstPage())
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                        Trước
                    </button>
                @else
                    <a href="{{ $pagination->previousPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Trước
                    </a>
                @endif

                @foreach($pagination->getUrlRange(max(1, $pagination->currentPage() - 2), min($pagination->lastPage(), $pagination->currentPage() + 2)) as $page => $url)
                    @if($page == $pagination->currentPage())
                        <button class="px-4 py-2 border border-blue-500 text-sm font-medium rounded-md text-white bg-blue-600">
                            {{ $page }}
                        </button>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach

                @if($pagination->hasMorePages())
                    <a href="{{ $pagination->nextPageUrl() }}" class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Sau
                    </a>
                @else
                    <button disabled class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                        Sau
                    </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- User Profile Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Hồ Sơ Người Dùng</h3>
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="userModalContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewUser(userId) {
    fetch(`/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('userModal');
            const content = document.getElementById('userModalContent');
            
            content.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID</label>
                        <p class="mt-1 text-sm text-gray-900">${data.id}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tên</label>
                        <p class="mt-1 text-sm text-gray-900">${data.name}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">${data.email}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vai Trò</label>
                        <p class="mt-1 text-sm text-gray-900">${data.role}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Trạng Thái</label>
                        <p class="mt-1 text-sm text-gray-900">${data.status}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Điện Thoại</label>
                        <p class="mt-1 text-sm text-gray-900">${data.phone || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ngày Sinh</label>
                        <p class="mt-1 text-sm text-gray-900">${data.date_of_birth || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Giới Tính</label>
                        <p class="mt-1 text-sm text-gray-900">${data.gender}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Địa Chỉ</label>
                        <p class="mt-1 text-sm text-gray-900">${data.address || 'N/A'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Đăng Nhập Cuối</label>
                        <p class="mt-1 text-sm text-gray-900">${data.last_login}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ngày Tạo</label>
                        <p class="mt-1 text-sm text-gray-900">${data.created_at}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="/admin/users/${data.id}/edit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-300">
                        Chỉnh Sửa
                    </a>
                    <button onclick="closeUserModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-300">
                        Đóng
                    </button>
                </div>
            `;
            
            modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Không thể tải dữ liệu người dùng');
        });
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
</script>
@endsection