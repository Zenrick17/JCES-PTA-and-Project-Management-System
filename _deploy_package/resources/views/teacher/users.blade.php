@extends('layouts.te-sidebar')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Hidden CSRF Token for JavaScript -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('teacher.users') }}" class="space-y-4">
            <!-- Main Search Bar -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, phone, email, or address..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search
                </button>
                @if(request('search') || request('status'))
                <a href="{{ route('teacher.users') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center gap-2 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
                @endif
                <button type="button" id="printSelectedBtn" onclick="printSelectedUsers()"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2 transition-colors duration-200 hidden">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Selected (<span id="selectedCount">0</span>)
                </button>
            </div>

            <!-- Search Hints -->
            <div class="text-xs text-gray-500 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Tip: Search parents by name, phone number, email, street address, city, or barangay</span>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-4 py-3 text-left">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll()" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                        </th>
                        <th class="w-2/5 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50" data-user-id="{{ $user->userID }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="user-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500"
                                   data-user='{{ json_encode($user) }}' onchange="updateSelectedUsers()">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-green-800">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($user->user_type == 'parent') bg-blue-100 text-blue-800
                                @elseif($user->user_type == 'teacher') bg-green-100 text-green-800
                                @elseif($user->user_type == 'administrator') bg-purple-100 text-purple-800
                                @elseif($user->user_type == 'principal') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($user->user_type ?? 'Unknown') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_date ? \Carbon\Carbon::parse($user->created_date)->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="openCredentialsModal({{ json_encode($user) }})"
                                        class="text-purple-600 hover:text-purple-900 p-1 rounded hover:bg-purple-50 transition-colors duration-200"
                                        title="Show Credentials">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </button>

                                <button onclick="openEditModal({{ json_encode($user) }})"
                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50 transition-colors duration-200"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <button onclick="openViewModal({{ json_encode($user) }})"
                                        class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50 transition-colors duration-200"
                                        title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                <button onclick="openDeleteModal({{ json_encode($user) }})"
                                        class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors duration-200"
                                        title="Archive">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-lg font-medium">No users found</p>
                                <p class="text-sm text-gray-400 mt-1">Try adjusting your search filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ !$users->previousPageUrl() ? 'opacity-50 pointer-events-none' : '' }}">
                    Previous
                </a>
                <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ !$users->nextPageUrl() ? 'opacity-50 pointer-events-none' : '' }}">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $users->total() }}</span> results
                    </p>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Per page:</label>
                        <select onchange="changeUsersPerPage(this.value)" class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-green-500">
                            <option value="10" {{ request('users_per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('users_per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('users_per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('users_per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-1">
                        @if ($users->onFirstPage())
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&lt;</span>
                        @else
                            <a href="{{ $users->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&lt;</a>
                        @endif

                        @for ($page = max(1, $users->currentPage() - 2); $page <= min($users->lastPage(), $users->currentPage() + 2); $page++)
                            @if ($page == $users->currentPage())
                                <span class="px-3 py-1 text-sm font-semibold text-white bg-green-600 rounded-md">{{ $page }}</span>
                            @else
                                <a href="{{ $users->appends(request()->query())->url($page) }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($users->hasMorePages())
                            <a href="{{ $users->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&gt;</a>
                        @else
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&gt;</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Edit User Details</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editUserForm" class="mt-6" onsubmit="submitEditForm(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="user_id" value="">
                <div class="space-y-4">
                    <!-- Common Fields -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="editFullName" name="full_name"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editEmail" name="email"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="editPhone" name="phone"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="editStatus" name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password (optional)</label>
                        <input type="password" id="editPassword" name="password"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500"
                               placeholder="Leave blank to keep current">
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View User Details Modal -->
<div id="viewUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
  <div class="flex items-center justify-center min-h-full px-4 py-6">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl p-6">
      <div class="flex items-center justify-between pb-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
        <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
          <div id="viewFullName" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <div id="viewEmail" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <div id="viewPhone" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
          <div id="viewRole" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <div id="viewStatus" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Created Date</label>
          <div id="viewCreatedDate" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>
      </div>

      <div class="flex items-center justify-center pt-6 border-t mt-6">
        <button type="button" onclick="closeViewModal()"
                class="px-6 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete User Confirmation Modal -->
<div id="deleteUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Archive User</h3>
                </div>
                <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Are you sure you want to archive this user? The user will be hidden from the list but will remain in the database.</p>
                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                        <div class="flex items-center">
                            <div id="deleteUserAvatar" class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span id="deleteUserInitials" class="text-red-600 font-semibold text-sm"></span>
                            </div>
                            <div class="ml-3">
                                <div id="deleteUserName" class="text-sm font-medium text-gray-900"></div>
                                <div id="deleteUserEmail" class="text-sm text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        To confirm, type "<span id="confirmationUserName" class="font-semibold text-red-600"></span>" in the box below:
                    </label>
                    <input type="text" id="deleteConfirmationInput"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Type the user name here">
                    <div id="confirmationError" class="text-red-600 text-xs mt-1 hidden">
                        The name you entered does not match. Please try again.
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t">
                <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteButton" onclick="confirmDeleteUser()" disabled
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-200">
                    Archive
                </button>
            </div>
        </div>
    </div>
</div>

<!-- User Credentials Modal -->
<div id="credentialsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">User Credentials</h3>
                </div>
                <button onclick="closeCredentialsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-6" id="credentialsContent">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center mb-4">
                        <div id="credentialsUserAvatar" class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span id="credentialsUserInitials" class="text-purple-600 font-semibold text-lg"></span>
                        </div>
                        <div class="ml-3">
                            <div id="credentialsUserName" class="text-base font-medium text-gray-900"></div>
                            <div id="credentialsUserRole" class="text-sm text-gray-500"></div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email Address</label>
                            <div class="flex items-center">
                                <input type="text" id="credentialsEmail" readonly
                                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-l text-sm text-gray-900">
                                <button onclick="copyToClipboard('credentialsEmail')"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r hover:bg-gray-200 transition-colors"
                                        title="Copy">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Password</label>
                            <div class="flex items-center">
                                <input type="text" id="credentialsPassword" readonly
                                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-l text-sm text-gray-900">
                                <button onclick="copyToClipboard('credentialsPassword')"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r hover:bg-gray-200 transition-colors"
                                        title="Copy">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeCredentialsModal()"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors duration-200">
                    Close
                </button>
                <button type="button" onclick="printSingleCredential()"
                        class="px-4 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print Selected Users Modal -->
<div id="printPreviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Print User Credentials</h3>
                </div>
                <button onclick="closePrintPreviewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Print Preview Content -->
            <div id="printPreviewContent" class="mt-6">
                <!-- Will be populated dynamically -->
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t mt-4">
                <button type="button" onclick="closePrintPreviewModal()"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" onclick="executePrint()"
                        class="px-4 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Selected users tracking
let selectedUsers = [];
let currentCredentialUser = null;

// ==================== PAGINATION FUNCTIONS ====================

function changeUsersPerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('users_per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page when changing per page
    window.location.href = url.toString();
}

// ==================== EDIT USER MODAL ====================

let currentEditingUserId = null;

function openEditModal(user) {
    currentEditingUserId = user.userID;

    // Set the hidden user ID field
    document.getElementById('editUserId').value = user.userID;

    // Populate the common form fields - combine first_name and last_name
    const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim();
    document.getElementById('editFullName').value = fullName;
    document.getElementById('editEmail').value = user.email || '';
    document.getElementById('editPhone').value = user.phone || '';
    document.getElementById('editStatus').value = user.is_active ? '1' : '0';

    // Clear password field
    document.getElementById('editPassword').value = '';

    // Show the modal
    document.getElementById('editUserModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.add('hidden');
    currentEditingUserId = null;
}

// Form submission function
function submitEditForm(event) {
    event.preventDefault();

    const formData = new FormData();
    const userId = document.getElementById('editUserId').value;

    // Get form values
    const fullName = document.getElementById('editFullName').value.trim();
    const nameParts = fullName.split(' ');
    const firstName = nameParts[0] || '';
    const lastName = nameParts.slice(1).join(' ') || '';

    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('_method', 'PUT');
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', document.getElementById('editEmail').value);
    formData.append('phone', document.getElementById('editPhone').value);
    formData.append('is_active', document.getElementById('editStatus').value);

    const password = document.getElementById('editPassword').value;
    if (password) {
        formData.append('password', password);
    }

    // Show loading state
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;

    // Submit the form
    fetch(`/teacher/users/${userId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('User updated successfully!');
            closeEditModal();
            window.location.reload();
        } else {
            alert('Error updating user: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = 'Error updating user. Please try again.';

        if (error.errors) {
            const errors = Object.values(error.errors).flat();
            errorMessage = 'Validation errors:\n' + errors.join('\n');
        } else if (error.message) {
            errorMessage = error.message;
        }

        alert(errorMessage);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// ==================== VIEW USER MODAL ====================

function openViewModal(user) {
    const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'N/A';
    document.getElementById('viewFullName').textContent = fullName;
    document.getElementById('viewEmail').textContent = user.email || 'N/A';
    document.getElementById('viewPhone').textContent = user.phone || 'N/A';
    document.getElementById('viewRole').textContent = (user.user_type || '').charAt(0).toUpperCase() + (user.user_type || '').slice(1);
    document.getElementById('viewStatus').textContent = user.is_active ? 'Active' : 'Inactive';
    document.getElementById('viewCreatedDate').textContent = user.created_date ? new Date(user.created_date).toLocaleDateString() : 'N/A';

    document.getElementById('viewUserModal').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('viewUserModal').classList.add('hidden');
}

// ==================== DELETE USER MODAL ====================

let userToDelete = null;

function openDeleteModal(user) {
    userToDelete = user;

    const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User';
    const initials = userName.split(' ').map(name => name.charAt(0).toUpperCase()).join('').substring(0, 2);

    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserEmail').textContent = user.email || 'No email';
    document.getElementById('deleteUserInitials').textContent = initials;
    document.getElementById('confirmationUserName').textContent = userName;

    // Reset confirmation input and button state
    document.getElementById('deleteConfirmationInput').value = '';
    document.getElementById('confirmDeleteButton').disabled = true;
    document.getElementById('confirmationError').classList.add('hidden');

    document.getElementById('deleteUserModal').classList.remove('hidden');

    setTimeout(() => {
        document.getElementById('deleteConfirmationInput').focus();
    }, 100);
}

function closeDeleteModal() {
    document.getElementById('deleteUserModal').classList.add('hidden');
    userToDelete = null;
}

function validateDeleteConfirmation() {
    const input = document.getElementById('deleteConfirmationInput');
    const confirmButton = document.getElementById('confirmDeleteButton');
    const errorDiv = document.getElementById('confirmationError');
    const expectedName = userToDelete ? (userToDelete.name || `${userToDelete.first_name || ''} ${userToDelete.last_name || ''}`.trim()) : '';

    if (input.value.trim() === expectedName) {
        confirmButton.disabled = false;
        errorDiv.classList.add('hidden');
    } else {
        confirmButton.disabled = true;
        if (input.value.trim().length > 0) {
            errorDiv.classList.remove('hidden');
        } else {
            errorDiv.classList.add('hidden');
        }
    }
}

function confirmDeleteUser() {
    if (!userToDelete) {
        alert('Error: No user selected for deletion');
        return;
    }

    const expectedName = userToDelete.name || `${userToDelete.first_name || ''} ${userToDelete.last_name || ''}`.trim();
    const inputValue = document.getElementById('deleteConfirmationInput').value.trim();

    if (inputValue !== expectedName) {
        document.getElementById('confirmationError').classList.remove('hidden');
        return;
    }

    // Show loading state on the delete button
    const deleteButton = document.getElementById('confirmDeleteButton');
    const originalButtonText = deleteButton.innerHTML;
    deleteButton.disabled = true;
    deleteButton.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Archiving...';

    deleteUserWithCallback(userToDelete.userID, () => {
        deleteButton.disabled = false;
        deleteButton.innerHTML = originalButtonText;
    });
}

function deleteUserWithCallback(userId, onError) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                 document.querySelector('input[name="_token"]')?.value;

    fetch(`/teacher/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        closeDeleteModal();

        if (data.success) {
            alert(data.message || 'User archived successfully!');
            location.reload();
        } else {
            alert(data.message || 'Error archiving user. Please try again.');
            if (onError) onError();
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        closeDeleteModal();
        alert('An error occurred while archiving the user: ' + error.message);
        if (onError) onError();
    });
}

// Add event listener for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('deleteConfirmationInput').addEventListener('input', validateDeleteConfirmation);
});

// ==================== CHECKBOX SELECTION ====================

function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.user-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateSelectedUsers();
}

function updateSelectedUsers() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const allCheckboxes = document.querySelectorAll('.user-checkbox');
    const printBtn = document.getElementById('printSelectedBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    selectedUsers = [];

    checkboxes.forEach(checkbox => {
        try {
            const userData = JSON.parse(checkbox.dataset.user);
            selectedUsers.push(userData);
        } catch (e) {
            console.error('Error parsing user data:', e);
        }
    });

    // Update select all checkbox state
    if (allCheckboxes.length > 0) {
        selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }

    // Show/hide print button
    if (selectedUsers.length > 0) {
        printBtn.classList.remove('hidden');
        selectedCountSpan.textContent = selectedUsers.length;
    } else {
        printBtn.classList.add('hidden');
    }
}

// ==================== CREDENTIALS MODAL ====================

function openCredentialsModal(user) {
    currentCredentialUser = user;

    const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User';
    const initials = userName.split(' ').map(name => name.charAt(0).toUpperCase()).join('').substring(0, 2);
    const role = (user.user_type || 'user').charAt(0).toUpperCase() + (user.user_type || 'user').slice(1);

    document.getElementById('credentialsUserName').textContent = userName;
    document.getElementById('credentialsUserRole').textContent = role;
    document.getElementById('credentialsUserInitials').textContent = initials;
    document.getElementById('credentialsEmail').value = user.email || 'N/A';
    document.getElementById('credentialsPassword').value = user.plain_password || 'N/A';

    document.getElementById('credentialsModal').classList.remove('hidden');
}

function closeCredentialsModal() {
    document.getElementById('credentialsModal').classList.add('hidden');
    currentCredentialUser = null;
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');

    // Show brief feedback
    const originalValue = element.value;
    element.value = 'Copied!';
    setTimeout(() => {
        element.value = originalValue;
    }, 1000);
}

// ==================== PRINT FUNCTIONS ====================

function printSingleCredential() {
    if (!currentCredentialUser) return;

    selectedUsers = [currentCredentialUser];
    openPrintPreview();
}

function printSelectedUsers() {
    if (selectedUsers.length === 0) {
        alert('Please select at least one user to print.');
        return;
    }

    openPrintPreview();
}

function openPrintPreview() {
    const content = document.getElementById('printPreviewContent');

    let html = `
        <div class="text-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">J. Cruz Sr. Elementary School</h2>
            <p class="text-sm text-gray-600">PTA Management System - User Credentials</p>
            <p class="text-xs text-gray-500">Generated on: ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
        </div>
        <div class="border-t border-b border-gray-200 py-4">
            <table class="w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">#</th>
                        <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Name</th>
                        <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Role</th>
                        <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Email</th>
                        <th class="text-left py-2 px-3 text-sm font-semibold text-gray-700">Password</th>
                    </tr>
                </thead>
                <tbody>
    `;

    selectedUsers.forEach((user, index) => {
        const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown';
        const role = (user.user_type || 'user').charAt(0).toUpperCase() + (user.user_type || 'user').slice(1);
        const password = user.plain_password || 'N/A';

        html += `
            <tr class="border-b border-gray-100 ${index % 2 === 0 ? 'bg-gray-50' : ''}">
                <td class="py-2 px-3 text-sm text-gray-600">${index + 1}</td>
                <td class="py-2 px-3 text-sm text-gray-900 font-medium">${userName}</td>
                <td class="py-2 px-3 text-sm text-gray-600">${role}</td>
                <td class="py-2 px-3 text-sm text-gray-600">${user.email || 'N/A'}</td>
                <td class="py-2 px-3 text-sm text-gray-600 font-mono">${password}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-xs text-gray-500 text-center">
            <p>Total Users: ${selectedUsers.length}</p>
            <p class="mt-1">⚠️ This document contains sensitive information. Handle with care.</p>
        </div>
    `;

    content.innerHTML = html;
    document.getElementById('printPreviewModal').classList.remove('hidden');
}

function closePrintPreviewModal() {
    document.getElementById('printPreviewModal').classList.add('hidden');
}

function executePrint() {
    const content = document.getElementById('printPreviewContent').innerHTML;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>User Credentials - Print</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                    color: #333;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    padding: 8px 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }
                th {
                    background-color: #f5f5f5;
                    font-weight: 600;
                }
                tr:nth-child(even) {
                    background-color: #fafafa;
                }
                .text-center { text-align: center; }
                .font-bold { font-weight: bold; }
                .text-xl { font-size: 1.25rem; }
                .text-sm { font-size: 0.875rem; }
                .text-xs { font-size: 0.75rem; }
                .mb-6 { margin-bottom: 1.5rem; }
                .mt-4 { margin-top: 1rem; }
                .mt-1 { margin-top: 0.25rem; }
                .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
                .border-t { border-top: 1px solid #e5e7eb; }
                .border-b { border-bottom: 1px solid #e5e7eb; }
                .font-mono { font-family: monospace; }
                .font-medium { font-weight: 500; }
                @media print {
                    body { padding: 0; }
                }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

// ==================== CLOSE MODALS ON OUTSIDE CLICK ====================

window.addEventListener('click', function(event) {
    const editModal = document.getElementById('editUserModal');
    const viewModal = document.getElementById('viewUserModal');
    const deleteModal = document.getElementById('deleteUserModal');
    const credentialsModal = document.getElementById('credentialsModal');
    const printPreviewModal = document.getElementById('printPreviewModal');

    if (event.target === editModal) closeEditModal();
    if (event.target === viewModal) closeViewModal();
    if (event.target === deleteModal) closeDeleteModal();
    if (event.target === credentialsModal) closeCredentialsModal();
    if (event.target === printPreviewModal) closePrintPreviewModal();
});
</script>
@endsection
