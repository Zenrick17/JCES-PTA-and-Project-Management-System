@extends($layout ?? 'layouts.ad-sidebar')

@php($routePrefix = $routePrefix ?? 'administrator')

@section('title', 'Users')

@section('content')
<div class="space-y-6">
    <!-- Hidden CSRF Token for JavaScript -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route($routePrefix . '.users') }}" class="space-y-4">
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
                <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    <option value="">All Roles</option>
                    <option value="parent" {{ request('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                    <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                    <option value="administrator" {{ request('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                    <option value="principal" {{ request('role') == 'principal' ? 'selected' : '' }}>Principal</option>
                </select>
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
                @if(request('search') || request('role') || request('status'))
                <a href="{{ route($routePrefix . '.users') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 flex items-center gap-2 transition-colors duration-200">
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
                                @if($user->user_type == 'principal') bg-purple-100 text-purple-800
                                @elseif($user->user_type == 'administrator') bg-blue-100 text-blue-800
                                @elseif($user->user_type == 'teacher') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($user->user_type) }}
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No users found.
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

    <!-- Students Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1"/>
                        </svg>
                        Students Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage students and link them to parent accounts</p>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route($routePrefix . '.students.import') }}" enctype="multipart/form-data" class="inline-flex">
                        @csrf
                        <input type="file" name="students_file" id="studentsImportFile" accept=".xls,.xlsx" class="hidden"
                               onchange="if(this.files.length){ this.form.submit(); }">
                        <button type="button" onclick="document.getElementById('studentsImportFile').click()"
                                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Import Students XLS
                        </button>
                    </form>
                    <button type="button" onclick="openBulkTransferModal()"
                            class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center gap-2 transition-colors duration-200"
                            id="bulkTransferBtn" style="display: none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Transfer Selected (<span id="selectedStudentCount">0</span>)
                    </button>
                    <button type="button" onclick="openAddStudentModal()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Student
                    </button>
                </div>
            </div>
        </div>

        @if(session('student_import_success'))
        <div class="mx-6 mt-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            {{ session('student_import_success') }}
        </div>
        @endif

        @if(session('student_import_error'))
        <div class="mx-6 mt-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            {{ session('student_import_error') }}
        </div>
        @endif

        <!-- Student Filters -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route($routePrefix . '.users') }}" class="flex flex-wrap gap-3 items-end">
                <!-- Preserve user filters -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="role" value="{{ request('role') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Search Student</label>
                    <input type="text" name="student_search" value="{{ request('student_search') }}"
                           placeholder="Search by name, grade, section..."
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Grade Level</label>
                    <select name="grade_level" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        <option value="Kinder" {{ request('grade_level') == 'Kinder' ? 'selected' : '' }}>Kinder</option>
                        <option value="Grade 1" {{ request('grade_level') == 'Grade 1' ? 'selected' : '' }}>Grade 1</option>
                        <option value="Grade 2" {{ request('grade_level') == 'Grade 2' ? 'selected' : '' }}>Grade 2</option>
                        <option value="Grade 3" {{ request('grade_level') == 'Grade 3' ? 'selected' : '' }}>Grade 3</option>
                        <option value="Grade 4" {{ request('grade_level') == 'Grade 4' ? 'selected' : '' }}>Grade 4</option>
                        <option value="Grade 5" {{ request('grade_level') == 'Grade 5' ? 'selected' : '' }}>Grade 5</option>
                        <option value="Grade 6" {{ request('grade_level') == 'Grade 6' ? 'selected' : '' }}>Grade 6</option>
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Academic Year</label>
                    <select name="academic_year" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Years</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                    <select name="enrollment_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('enrollment_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="transferred" {{ request('enrollment_status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="graduated" {{ request('enrollment_status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="dropped" {{ request('enrollment_status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>

                @if(request('student_search') || request('grade_level') || request('academic_year') || request('enrollment_status'))
                <a href="{{ route($routePrefix . '.users', ['search' => request('search'), 'role' => request('role'), 'status' => request('status')]) }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear
                </a>
                @endif
            </form>
        </div>

        <!-- Students Table -->
        <div class="overflow-x-auto">
            <table class="w-full table-fixed divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-12 px-4 py-3 text-left">
                            <input type="checkbox" id="selectAllStudents" onchange="toggleSelectAllStudents()" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="w-1/4 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade & Section</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent/Guardian</th>
                        <th class="w-1/8 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="w-1/6 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50" data-student-id="{{ $student->studentID }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   data-student-id="{{ $student->studentID }}" onchange="updateSelectedStudents()">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full {{ $student->gender == 'male' ? 'bg-blue-100' : 'bg-pink-100' }} flex items-center justify-center">
                                        <span class="text-sm font-medium {{ $student->gender == 'male' ? 'text-blue-800' : 'text-pink-800' }}">
                                            {{ strtoupper(substr($student->student_name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->student_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ ucfirst($student->gender) }}
                                        @if($student->birth_date)
                                            • {{ \Carbon\Carbon::parse($student->birth_date)->age }} yrs old
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->grade_level }}</div>
                            <div class="text-xs text-gray-500">{{ $student->section ?? 'No Section' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                {{ $student->academic_year }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->parents->count() > 0)
                                @foreach($student->parents->take(2) as $parent)
                                <div class="text-sm text-gray-900">
                                    {{ $parent->user ? ($parent->user->first_name . ' ' . $parent->user->last_name) : ($parent->first_name . ' ' . $parent->last_name) }}
                                    <span class="text-xs text-gray-500">({{ ucfirst($parent->pivot->relationship_type) }})</span>
                                </div>
                                @endforeach
                                @if($student->parents->count() > 2)
                                <div class="text-xs text-gray-400">+{{ $student->parents->count() - 2 }} more</div>
                                @endif
                            @else
                                <span class="text-xs text-gray-400 italic">No parent linked</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($student->enrollment_status == 'active') bg-green-100 text-green-800
                                @elseif($student->enrollment_status == 'transferred') bg-yellow-100 text-yellow-800
                                @elseif($student->enrollment_status == 'graduated') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($student->enrollment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-1">
                                <button onclick="openTransferStudentModal({{ json_encode($student) }})"
                                        class="text-amber-600 hover:text-amber-900 p-1 rounded hover:bg-amber-50 transition-colors duration-200"
                                        title="Transfer Academic Year">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </button>

                                <button onclick="openEditStudentModal({{ json_encode($student) }})"
                                        class="text-indigo-600 hover:text-indigo-900 p-1 rounded hover:bg-indigo-50 transition-colors duration-200"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                <button onclick="openViewStudentModal({{ json_encode($student) }})"
                                        class="text-green-600 hover:text-green-900 p-1 rounded hover:bg-green-50 transition-colors duration-200"
                                        title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>

                                <button onclick="openLinkParentModal({{ json_encode($student) }})"
                                        class="text-purple-600 hover:text-purple-900 p-1 rounded hover:bg-purple-50 transition-colors duration-200"
                                        title="Link Parent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </button>

                                <button onclick="openDeleteStudentModal({{ json_encode($student) }})"
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
                        <td colspan="7" class="px-6 py-8 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m9 5.197v1"/>
                                </svg>
                                <p class="text-gray-500 text-sm">No students found.</p>
                                <button onclick="openAddStudentModal()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Add your first student
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Student Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <a href="{{ $students->appends(request()->query())->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ !$students->previousPageUrl() ? 'opacity-50 pointer-events-none' : '' }}">
                    Previous
                </a>
                <a href="{{ $students->appends(request()->query())->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 {{ !$students->nextPageUrl() ? 'opacity-50 pointer-events-none' : '' }}">
                    Next
                </a>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium">{{ $students->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $students->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $students->total() }}</span> students
                    </p>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-600">Per page:</label>
                        <select onchange="changeStudentsPerPage(this.value)" class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500">
                            <option value="10" {{ request('students_per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('students_per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('students_per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('students_per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-1">
                        @if ($students->onFirstPage())
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&lt;</span>
                        @else
                            <a href="{{ $students->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&lt;</a>
                        @endif

                        @for ($page = max(1, $students->currentPage() - 2); $page <= min($students->lastPage(), $students->currentPage() + 2); $page++)
                            @if ($page == $students->currentPage())
                                <span class="px-3 py-1 text-sm font-semibold text-white bg-green-600 rounded-md">{{ $page }}</span>
                            @else
                                <a href="{{ $students->appends(request()->query())->url($page) }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($students->hasMorePages())
                            <a href="{{ $students->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">&gt;</a>
                        @else
                            <span class="px-3 py-1 text-sm text-gray-400 bg-gray-100 rounded-md cursor-not-allowed">&gt;</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Add New Student</h3>
                <button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="addStudentForm" class="mt-6" onsubmit="submitAddStudentForm(event)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
                        <input type="text" name="student_name" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Full name of the student">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level *</label>
                            <select name="grade_level" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Grade</option>
                                <option value="Kinder">Kinder</option>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                            <input type="text" name="section"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., Section A">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" name="academic_year" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                   placeholder="e.g., 2025-2026" value="{{ date('Y') }}-{{ date('Y') + 1 }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Date *</label>
                            <input type="date" name="enrollment_date" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                            <input type="date" name="birth_date"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select name="gender" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Status *</label>
                        <select name="enrollment_status" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="active" selected>Active</option>
                            <option value="transferred">Transferred</option>
                            <option value="graduated">Graduated</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>

                    <hr class="my-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Link to Parent Account</label>
                        <div class="relative">
                            <input type="hidden" name="parent_id" id="addStudentParentId">
                            <input type="text" id="addStudentParentSearch"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                   placeholder="Type to search parent by name or email..."
                                   autocomplete="off">
                            <div id="addStudentParentDropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                            <div id="addStudentSelectedParent" class="hidden mt-2 p-2 bg-blue-50 rounded-lg flex items-center justify-between">
                                <span id="addStudentSelectedParentText" class="text-sm text-blue-800"></span>
                                <button type="button" onclick="clearAddStudentParent()" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship Type</label>
                            <select name="relationship_type"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="guardian">Guardian</option>
                                <option value="mother">Mother</option>
                                <option value="father">Father</option>
                                <option value="grandparent">Grandparent</option>
                                <option value="sibling">Sibling</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="flex items-center pt-6">
                            <input type="checkbox" name="is_primary_contact" id="addIsPrimaryContact" value="1" checked
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="addIsPrimaryContact" class="ml-2 text-sm text-gray-700">Primary Contact</label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeAddStudentModal()"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Edit Student</h3>
                <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editStudentForm" class="mt-6" onsubmit="submitEditStudentForm(event)">
                @csrf
                @method('PUT')
                <input type="hidden" id="editStudentId" name="student_id" value="">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Name *</label>
                        <input type="text" id="editStudentName" name="student_name" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level *</label>
                            <select id="editGradeLevel" name="grade_level" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="Kinder">Kinder</option>
                                <option value="Grade 1">Grade 1</option>
                                <option value="Grade 2">Grade 2</option>
                                <option value="Grade 3">Grade 3</option>
                                <option value="Grade 4">Grade 4</option>
                                <option value="Grade 5">Grade 5</option>
                                <option value="Grade 6">Grade 6</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                            <input type="text" id="editSection" name="section"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year *</label>
                            <input type="text" id="editAcademicYear" name="academic_year" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Date *</label>
                            <input type="date" id="editEnrollmentDate" name="enrollment_date" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Birth Date</label>
                            <input type="date" id="editBirthDate" name="birth_date"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select id="editGender" name="gender" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Status *</label>
                        <select id="editEnrollmentStatus" name="enrollment_status" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="active">Active</option>
                            <option value="transferred">Transferred</option>
                            <option value="graduated">Graduated</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeEditStudentModal()"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Student Modal -->
<div id="viewStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Student Details</h3>
                <button onclick="closeViewStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-center space-x-4">
                    <div id="viewStudentAvatar" class="w-16 h-16 rounded-full flex items-center justify-center text-xl font-semibold"></div>
                    <div>
                        <div id="viewStudentName" class="text-xl font-medium text-gray-900"></div>
                        <div id="viewStudentGender" class="text-sm text-gray-500"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Grade Level</label>
                        <div id="viewStudentGrade" class="text-sm text-gray-900 mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Section</label>
                        <div id="viewStudentSection" class="text-sm text-gray-900 mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Academic Year</label>
                        <div id="viewStudentAcademicYear" class="text-sm text-gray-900 mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                        <div id="viewStudentStatus" class="mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Birth Date</label>
                        <div id="viewStudentBirthDate" class="text-sm text-gray-900 mt-1"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Enrollment Date</label>
                        <div id="viewStudentEnrollmentDate" class="text-sm text-gray-900 mt-1"></div>
                    </div>
                </div>

                <div class="pt-4 border-t">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Linked Parents/Guardians</label>
                    <div id="viewStudentParents" class="space-y-2"></div>
                </div>
            </div>

            <div class="flex items-center justify-center pt-6 border-t mt-6">
                <button type="button" onclick="closeViewStudentModal()"
                        class="px-6 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Transfer Student Modal -->
<div id="transferStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Transfer Student</h3>
                </div>
                <button onclick="closeTransferStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="transferStudentForm" class="mt-6" onsubmit="submitTransferStudentForm(event)">
                @csrf
                <input type="hidden" id="transferStudentId" name="student_id" value="">

                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-600">Transferring:</div>
                    <div id="transferStudentInfo" class="font-medium text-gray-900"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Academic Year *</label>
                        <input type="text" id="transferAcademicYear" name="new_academic_year" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-amber-500"
                               placeholder="e.g., 2026-2027">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Grade Level *</label>
                        <select id="transferGradeLevel" name="new_grade_level" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-amber-500">
                            <option value="Kinder">Kinder</option>
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option>
                            <option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Section</label>
                        <input type="text" id="transferSection" name="new_section"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-amber-500"
                               placeholder="Leave blank if same section">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enrollment Status</label>
                        <select id="transferEnrollmentStatus" name="new_enrollment_status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-amber-500">
                            <option value="active">Active</option>
                            <option value="transferred">Transferred</option>
                            <option value="graduated">Graduated</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeTransferStudentModal()"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors duration-200">
                        Transfer Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Transfer Modal -->
<div id="bulkTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Bulk Transfer Students</h3>
                </div>
                <button onclick="closeBulkTransferModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="bulkTransferForm" class="mt-6" onsubmit="submitBulkTransferForm(event)">
                @csrf

                <div class="bg-blue-50 rounded-lg p-3 mb-4">
                    <div class="text-sm text-blue-600">
                        <span id="bulkTransferCount" class="font-bold">0</span> student(s) selected for transfer
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Academic Year *</label>
                        <input type="text" id="bulkTransferAcademicYear" name="new_academic_year" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-amber-500"
                               placeholder="e.g., 2026-2027" value="{{ date('Y') + 1 }}-{{ date('Y') + 2 }}">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="promoteGrade" name="promote_grade" value="1" checked
                               class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                        <label for="promoteGrade" class="ml-2 text-sm text-gray-700">
                            Promote to next grade level
                        </label>
                    </div>

                    <div class="bg-gray-50 rounded p-3 text-xs text-gray-600">
                        <strong>Note:</strong> Students in Grade 6 will be marked as "Graduated" when promoted.
                        Family relationships with parents will be preserved.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeBulkTransferModal()"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors duration-200">
                        Transfer Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Parent Modal -->
<div id="linkParentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Link Parent to Student</h3>
                </div>
                <button onclick="closeLinkParentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="linkParentForm" class="mt-6" onsubmit="submitLinkParentForm(event)">
                @csrf
                <input type="hidden" id="linkParentStudentId" name="student_id" value="">

                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                    <div class="text-sm text-gray-600">Linking parent to:</div>
                    <div id="linkParentStudentInfo" class="font-medium text-gray-900"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Parent *</label>
                        <div class="relative">
                            <input type="hidden" name="parent_id" id="linkParentId" required>
                            <input type="text" id="linkParentSearch"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500"
                                   placeholder="Type to search parent by name or email..."
                                   autocomplete="off">
                            <div id="linkParentDropdown" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden">
                                <!-- Options will be populated by JavaScript -->
                            </div>
                            <div id="linkSelectedParent" class="hidden mt-2 p-2 bg-purple-50 rounded-lg flex items-center justify-between">
                                <span id="linkSelectedParentText" class="text-sm text-purple-800"></span>
                                <button type="button" onclick="clearLinkParent()" class="text-purple-600 hover:text-purple-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship Type *</label>
                        <select name="relationship_type" id="linkRelationshipType" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-purple-500">
                            <option value="guardian">Guardian</option>
                            <option value="mother">Mother</option>
                            <option value="father">Father</option>
                            <option value="grandparent">Grandparent</option>
                            <option value="sibling">Sibling</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_primary_contact" id="linkIsPrimaryContact" value="1"
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <label for="linkIsPrimaryContact" class="ml-2 text-sm text-gray-700">Set as primary contact</label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeLinkParentModal()"
                            class="px-4 py-2 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors duration-200">
                        Link Parent
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Student Confirmation Modal -->
<div id="deleteStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-full px-4 py-6">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Archive Student</h3>
                </div>
                <button onclick="closeDeleteStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mt-6">
                <p class="text-sm text-gray-600 mb-4">Are you sure you want to archive this student? The student will be hidden from the list but will remain in the database.</p>
                <div class="bg-gray-50 rounded-lg p-3">
                    <div id="deleteStudentInfo" class="font-medium text-gray-900"></div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t mt-6">
                <button type="button" onclick="closeDeleteStudentModal()"
                        class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" id="confirmDeleteStudentBtn" onclick="confirmDeleteStudent()"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    Archive Student
                </button>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="editRole" name="role"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="parent">Parent</option>
                            <option value="teacher">Teacher</option>
                            <option value="administrator">Administrator</option>
                            <option value="principal">Principal</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="editStatus" name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <!-- Role-specific fields -->
                    <div id="parentFields" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="editAddress" name="address" rows="3"
                                      class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                    </div>

                    <div id="teacherFields" class="hidden space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" id="editSubject" name="subject"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" id="editDepartment" name="department"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
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

        <!-- Role-specific display fields (span full width when present) -->
        <div id="viewParentInfo" class="hidden md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
          <div id="viewAddress" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
        </div>

        <div id="viewTeacherInfo" class="hidden md:col-span-2 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
            <div id="viewSubject" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
            <div id="viewDepartment" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-900"></div>
          </div>
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
const routePrefix = "{{ $routePrefix }}";

// Selected users tracking
let selectedUsers = [];
let currentCredentialUser = null;

function openAddUserModal() {
    // For now, this is a placeholder function
    // You can implement a modal or redirect to a create user page
    alert('Add User functionality will be implemented here');
    // Example: window.location.href = `/${routePrefix}/users/create`;
    // Or open a modal dialog
}

// Edit User Modal Functions
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
    document.getElementById('editRole').value = user.user_type || '';
    document.getElementById('editStatus').value = user.is_active ? '1' : '0';

    // Clear password field
    document.getElementById('editPassword').value = '';

    // Show/hide role-specific fields
    showRoleSpecificFields(user.user_type || '', 'edit');

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
    formData.append('user_type', document.getElementById('editRole').value);
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
    fetch(`/${routePrefix}/users/${userId}`, {
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
            // Show success message
            alert('User updated successfully!');
            // Close modal
            closeEditModal();
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert('Error updating user: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = 'Error updating user. Please try again.';

        if (error.errors) {
            // Show validation errors
            const errors = Object.values(error.errors).flat();
            errorMessage = 'Validation errors:\n' + errors.join('\n');
        } else if (error.message) {
            errorMessage = error.message;
        }

        alert(errorMessage);
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// View User Modal Functions
function openViewModal(user) {
    // Populate the view with user data - combine first_name and last_name
    const fullName = `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'N/A';
    document.getElementById('viewFullName').textContent = fullName;
    document.getElementById('viewEmail').textContent = user.email || 'N/A';
    document.getElementById('viewPhone').textContent = user.phone || 'N/A';
    document.getElementById('viewRole').textContent = (user.user_type || '').charAt(0).toUpperCase() + (user.user_type || '').slice(1);
    document.getElementById('viewStatus').textContent = user.is_active ? 'Active' : 'Inactive';
    document.getElementById('viewCreatedDate').textContent = user.created_date ? new Date(user.created_date).toLocaleDateString() : 'N/A';

    // Show/hide role-specific fields
    showRoleSpecificFields(user.user_type || '', 'view');

    // Show the modal
    document.getElementById('viewUserModal').classList.remove('hidden');
}

function closeViewModal() {
    document.getElementById('viewUserModal').classList.add('hidden');
}

// Role-specific fields management
function showRoleSpecificFields(role, mode) {
    const prefix = mode === 'edit' ? 'edit' : 'view';

    // Hide all role-specific sections
    const roleFields = ['parentFields', 'teacherFields'];
    const viewRoleFields = ['viewParentInfo', 'viewTeacherInfo'];

    if (mode === 'edit') {
        roleFields.forEach(field => {
            document.getElementById(field).classList.add('hidden');
        });

        // Show relevant fields based on role
        if (role === 'parent') {
            document.getElementById('parentFields').classList.remove('hidden');
        } else if (role === 'teacher') {
            document.getElementById('teacherFields').classList.remove('hidden');
        }
        // No special fields for administrator or principal roles
    } else {
        viewRoleFields.forEach(field => {
            document.getElementById(field).classList.add('hidden');
        });

        // Show relevant fields based on role
        if (role === 'parent') {
            document.getElementById('viewParentInfo').classList.remove('hidden');
        } else if (role === 'teacher') {
            document.getElementById('viewTeacherInfo').classList.remove('hidden');
        }
        // No special fields for administrator or principal roles
    }
}

// Delete User Modal Functions
let userToDelete = null;

function openDeleteModal(user) {
    console.log('openDeleteModal called with user:', user);
    userToDelete = user;

    // Get the user's display name (combining first_name and last_name or using name field)
    const userName = user.name || `${user.first_name || ''} ${user.last_name || ''}`.trim() || 'Unknown User';
    console.log('Determined user name:', userName);

    // Generate user initials
    const initials = userName.split(' ').map(name => name.charAt(0).toUpperCase()).join('').substring(0, 2);

    // Populate modal content
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserEmail').textContent = user.email || 'No email';
    document.getElementById('deleteUserInitials').textContent = initials;
    document.getElementById('confirmationUserName').textContent = userName;

    // Reset confirmation input and button state
    document.getElementById('deleteConfirmationInput').value = '';
    document.getElementById('confirmDeleteButton').disabled = true;
    document.getElementById('confirmationError').classList.add('hidden');

    // Show the modal
    document.getElementById('deleteUserModal').classList.remove('hidden');

    // Focus on the input field
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
    console.log('confirmDeleteUser called');
    console.log('userToDelete:', userToDelete);

    if (!userToDelete) {
        alert('Error: No user selected for deletion');
        return;
    }

    const expectedName = userToDelete.name || `${userToDelete.first_name || ''} ${userToDelete.last_name || ''}`.trim();
    const inputValue = document.getElementById('deleteConfirmationInput').value.trim();

    console.log('Expected name:', expectedName);
    console.log('Input value:', inputValue);
    console.log('Names match:', inputValue === expectedName);

    if (inputValue !== expectedName) {
        console.log('Names do not match, showing error');
        document.getElementById('confirmationError').classList.remove('hidden');
        return;
    }

    console.log('Names match, proceeding with deletion');

    // Show loading state on the delete button
    const deleteButton = document.getElementById('confirmDeleteButton');
    const originalButtonText = deleteButton.innerHTML;
    deleteButton.disabled = true;
    deleteButton.innerHTML = '<svg class="w-4 h-4 animate-spin inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Archiving...';

    // Call the actual delete function
    deleteUserWithCallback(userToDelete.userID, () => {
        // Reset button state if delete fails
        deleteButton.disabled = false;
        deleteButton.innerHTML = originalButtonText;
    });
}

function deleteUserWithCallback(userId, onError) {
    console.log('Attempting to archive user with ID:', userId);

    // Get CSRF token
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                 document.querySelector('input[name="_token"]')?.value;

    console.log('CSRF token found:', token ? 'Yes' : 'No');

    // Send DELETE request to backend (using dynamic route prefix)
    fetch(`/${routePrefix}/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);

        // Close the modal
        closeDeleteModal();

        if (data.success) {
            // Show success message
            alert(data.message || 'User archived successfully!');
            // Reload the page to show updated list
            location.reload();
        } else {
            // Show error message
            alert(data.message || 'Error archiving user. Please try again.');
            if (onError) onError();
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);

        // Close the modal
        closeDeleteModal();

        alert('An error occurred while archiving the user: ' + error.message);
        if (onError) onError();
    });
}

// Add event listener for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('deleteConfirmationInput').addEventListener('input', validateDeleteConfirmation);
});

// Delete User Function (kept for backward compatibility)

function deleteUser(userId) {
    deleteUserWithCallback(userId, null);
}

// Checkbox Selection Functions
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

// Credentials Modal Functions
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

// Print Functions
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

    // Wait for content to load then print
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const editModal = document.getElementById('editUserModal');
    const viewModal = document.getElementById('viewUserModal');
    const deleteModal = document.getElementById('deleteUserModal');
    const credentialsModal = document.getElementById('credentialsModal');
    const printPreviewModal = document.getElementById('printPreviewModal');

    if (event.target === editModal) {
        closeEditModal();
    }
    if (event.target === viewModal) {
        closeViewModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
    if (event.target === credentialsModal) {
        closeCredentialsModal();
    }
    if (event.target === printPreviewModal) {
        closePrintPreviewModal();
    }

    // Student modals
    const addStudentModal = document.getElementById('addStudentModal');
    const editStudentModal = document.getElementById('editStudentModal');
    const viewStudentModal = document.getElementById('viewStudentModal');
    const transferStudentModal = document.getElementById('transferStudentModal');
    const bulkTransferModal = document.getElementById('bulkTransferModal');
    const linkParentModal = document.getElementById('linkParentModal');
    const deleteStudentModal = document.getElementById('deleteStudentModal');

    if (event.target === addStudentModal) closeAddStudentModal();
    if (event.target === editStudentModal) closeEditStudentModal();
    if (event.target === viewStudentModal) closeViewStudentModal();
    if (event.target === transferStudentModal) closeTransferStudentModal();
    if (event.target === bulkTransferModal) closeBulkTransferModal();
    if (event.target === linkParentModal) closeLinkParentModal();
    if (event.target === deleteStudentModal) closeDeleteStudentModal();
});

// ==================== PAGINATION FUNCTIONS ====================

function changeUsersPerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('users_per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page when changing per page
    window.location.href = url.toString();
}

function changeStudentsPerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('students_per_page', perPage);
    url.searchParams.delete('student_page'); // Reset to first page when changing per page
    window.location.href = url.toString();
}

// ==================== STUDENT MANAGEMENT FUNCTIONS ====================

let selectedStudents = [];
let studentToDelete = null;
let currentEditingStudent = null;
let currentTransferStudent = null;
let currentLinkParentStudent = null;

// Student Selection Functions
function toggleSelectAllStudents() {
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const checkboxes = document.querySelectorAll('.student-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateSelectedStudents();
}

function updateSelectedStudents() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const allCheckboxes = document.querySelectorAll('.student-checkbox');
    const bulkTransferBtn = document.getElementById('bulkTransferBtn');
    const selectedCountSpan = document.getElementById('selectedStudentCount');

    selectedStudents = [];

    checkboxes.forEach(checkbox => {
        selectedStudents.push(checkbox.dataset.studentId);
    });

    // Update select all checkbox state
    if (allCheckboxes.length > 0) {
        selectAllCheckbox.checked = checkboxes.length === allCheckboxes.length;
        selectAllCheckbox.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
    }

    // Show/hide bulk transfer button
    if (selectedStudents.length > 0) {
        bulkTransferBtn.style.display = 'flex';
        selectedCountSpan.textContent = selectedStudents.length;
    } else {
        bulkTransferBtn.style.display = 'none';
    }
}

// Add Student Modal Functions
function openAddStudentModal() {
    document.getElementById('addStudentForm').reset();
    clearAddStudentParent(); // Clear the searchable parent selection
    document.getElementById('addStudentModal').classList.remove('hidden');
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
}

function submitAddStudentForm(event) {
    event.preventDefault();

    const form = document.getElementById('addStudentForm');
    const formData = new FormData(form);
    const token = document.querySelector('input[name="_token"]').value;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Adding...';
    submitBtn.disabled = true;

    fetch(`/${routePrefix}/students`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Student added successfully!');
            closeAddStudentModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Error adding student';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the student.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Edit Student Modal Functions
function openEditStudentModal(student) {
    currentEditingStudent = student;

    document.getElementById('editStudentId').value = student.studentID;
    document.getElementById('editStudentName').value = student.student_name || '';
    document.getElementById('editGradeLevel').value = student.grade_level || '';
    document.getElementById('editSection').value = student.section || '';
    document.getElementById('editAcademicYear').value = student.academic_year || '';
    document.getElementById('editEnrollmentDate').value = student.enrollment_date ? student.enrollment_date.split('T')[0] : '';
    document.getElementById('editBirthDate').value = student.birth_date ? student.birth_date.split('T')[0] : '';
    document.getElementById('editGender').value = student.gender || '';
    document.getElementById('editEnrollmentStatus').value = student.enrollment_status || 'active';

    document.getElementById('editStudentModal').classList.remove('hidden');
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
    currentEditingStudent = null;
}

function submitEditStudentForm(event) {
    event.preventDefault();

    if (!currentEditingStudent) return;

    const form = document.getElementById('editStudentForm');
    const formData = new FormData(form);
    const token = document.querySelector('input[name="_token"]').value;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;

    fetch(`/${routePrefix}/students/${currentEditingStudent.studentID}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Student updated successfully!');
            closeEditStudentModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Error updating student';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the student.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// View Student Modal Functions
function openViewStudentModal(student) {
    const avatar = document.getElementById('viewStudentAvatar');
    const initials = student.student_name.substring(0, 2).toUpperCase();

    avatar.textContent = initials;
    avatar.className = `w-16 h-16 rounded-full flex items-center justify-center text-xl font-semibold ${student.gender === 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800'}`;

    document.getElementById('viewStudentName').textContent = student.student_name;
    document.getElementById('viewStudentGender').textContent = student.gender ? (student.gender.charAt(0).toUpperCase() + student.gender.slice(1)) : 'N/A';
    document.getElementById('viewStudentGrade').textContent = student.grade_level || 'N/A';
    document.getElementById('viewStudentSection').textContent = student.section || 'Not Assigned';
    document.getElementById('viewStudentAcademicYear').textContent = student.academic_year || 'N/A';

    const statusEl = document.getElementById('viewStudentStatus');
    const statusColors = {
        'active': 'bg-green-100 text-green-800',
        'transferred': 'bg-yellow-100 text-yellow-800',
        'graduated': 'bg-blue-100 text-blue-800',
        'dropped': 'bg-red-100 text-red-800'
    };
    statusEl.innerHTML = `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusColors[student.enrollment_status] || 'bg-gray-100 text-gray-800'}">${student.enrollment_status ? student.enrollment_status.charAt(0).toUpperCase() + student.enrollment_status.slice(1) : 'N/A'}</span>`;

    document.getElementById('viewStudentBirthDate').textContent = student.birth_date ? new Date(student.birth_date).toLocaleDateString() : 'N/A';
    document.getElementById('viewStudentEnrollmentDate').textContent = student.enrollment_date ? new Date(student.enrollment_date).toLocaleDateString() : 'N/A';

    // Display parents
    const parentsContainer = document.getElementById('viewStudentParents');
    if (student.parents && student.parents.length > 0) {
        parentsContainer.innerHTML = student.parents.map(parent => {
            const parentName = parent.user ? `${parent.user.first_name} ${parent.user.last_name}` : `${parent.first_name} ${parent.last_name}`;
            return `
                <div class="flex items-center justify-between bg-gray-50 rounded p-2">
                    <div>
                        <span class="text-sm font-medium text-gray-900">${parentName}</span>
                        <span class="text-xs text-gray-500 ml-2">(${parent.pivot.relationship_type})</span>
                    </div>
                    ${parent.pivot.is_primary_contact ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Primary</span>' : ''}
                </div>
            `;
        }).join('');
    } else {
        parentsContainer.innerHTML = '<p class="text-sm text-gray-500 italic">No parents linked to this student.</p>';
    }

    document.getElementById('viewStudentModal').classList.remove('hidden');
}

function closeViewStudentModal() {
    document.getElementById('viewStudentModal').classList.add('hidden');
}

// Transfer Student Modal Functions
function openTransferStudentModal(student) {
    currentTransferStudent = student;

    document.getElementById('transferStudentId').value = student.studentID;
    document.getElementById('transferStudentInfo').textContent = `${student.student_name} (${student.grade_level} - ${student.academic_year})`;

    // Suggest next academic year
    const currentYear = student.academic_year;
    if (currentYear) {
        const years = currentYear.split('-');
        if (years.length === 2) {
            const nextYear = `${parseInt(years[0]) + 1}-${parseInt(years[1]) + 1}`;
            document.getElementById('transferAcademicYear').value = nextYear;
        }
    }

    // Suggest next grade level
    const gradeMapping = {
        'Kinder': 'Grade 1',
        'Grade 1': 'Grade 2',
        'Grade 2': 'Grade 3',
        'Grade 3': 'Grade 4',
        'Grade 4': 'Grade 5',
        'Grade 5': 'Grade 6',
        'Grade 6': 'Grade 6'
    };
    document.getElementById('transferGradeLevel').value = gradeMapping[student.grade_level] || student.grade_level;
    document.getElementById('transferSection').value = student.section || '';

    document.getElementById('transferStudentModal').classList.remove('hidden');
}

function closeTransferStudentModal() {
    document.getElementById('transferStudentModal').classList.add('hidden');
    currentTransferStudent = null;
}

function submitTransferStudentForm(event) {
    event.preventDefault();

    if (!currentTransferStudent) return;

    const form = document.getElementById('transferStudentForm');
    const formData = new FormData(form);
    const token = document.querySelector('input[name="_token"]').value;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Transferring...';
    submitBtn.disabled = true;

    fetch(`/${routePrefix}/students/${currentTransferStudent.studentID}/transfer`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Student transferred successfully!');
            closeTransferStudentModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Error transferring student';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while transferring the student.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Bulk Transfer Modal Functions
function openBulkTransferModal() {
    if (selectedStudents.length === 0) {
        alert('Please select at least one student to transfer.');
        return;
    }

    document.getElementById('bulkTransferCount').textContent = selectedStudents.length;
    document.getElementById('bulkTransferModal').classList.remove('hidden');
}

function closeBulkTransferModal() {
    document.getElementById('bulkTransferModal').classList.add('hidden');
}

function submitBulkTransferForm(event) {
    event.preventDefault();

    if (selectedStudents.length === 0) {
        alert('No students selected.');
        return;
    }

    const token = document.querySelector('input[name="_token"]').value;
    const academicYear = document.getElementById('bulkTransferAcademicYear').value;
    const promoteGrade = document.getElementById('promoteGrade').checked;

    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Transferring...';
    submitBtn.disabled = true;

    fetch(`/${routePrefix}/students/bulk-transfer`, {
        method: 'POST',
        body: JSON.stringify({
            student_ids: selectedStudents,
            new_academic_year: academicYear,
            promote_grade: promoteGrade
        }),
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Students transferred successfully!');
            closeBulkTransferModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Error transferring students';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while transferring students.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Link Parent Modal Functions
function openLinkParentModal(student) {
    currentLinkParentStudent = student;

    document.getElementById('linkParentStudentId').value = student.studentID;
    document.getElementById('linkParentStudentInfo').textContent = student.student_name;
    clearLinkParent();
    document.getElementById('linkRelationshipType').value = 'guardian';
    document.getElementById('linkIsPrimaryContact').checked = false;

    document.getElementById('linkParentModal').classList.remove('hidden');
}

function closeLinkParentModal() {
    document.getElementById('linkParentModal').classList.add('hidden');
    currentLinkParentStudent = null;
}

function submitLinkParentForm(event) {
    event.preventDefault();

    if (!currentLinkParentStudent) return;

    const form = document.getElementById('linkParentForm');
    const formData = new FormData(form);
    const token = document.querySelector('input[name="_token"]').value;

    // Add the required fields for the update endpoint
    formData.append('student_name', currentLinkParentStudent.student_name);
    formData.append('grade_level', currentLinkParentStudent.grade_level);
    formData.append('academic_year', currentLinkParentStudent.academic_year);
    formData.append('enrollment_date', currentLinkParentStudent.enrollment_date ? currentLinkParentStudent.enrollment_date.split('T')[0] : '');
    formData.append('gender', currentLinkParentStudent.gender);
    formData.append('enrollment_status', currentLinkParentStudent.enrollment_status);
    formData.append('_method', 'PUT');

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Linking...';
    submitBtn.disabled = true;

    fetch(`/${routePrefix}/students/${currentLinkParentStudent.studentID}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Parent linked successfully!');
            closeLinkParentModal();
            location.reload();
        } else {
            let errorMsg = data.message || 'Error linking parent';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('\n');
            }
            alert(errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while linking the parent.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Delete Student Modal Functions
function openDeleteStudentModal(student) {
    studentToDelete = student;

    document.getElementById('deleteStudentInfo').textContent = `${student.student_name} (${student.grade_level} - ${student.academic_year})`;
    document.getElementById('deleteStudentModal').classList.remove('hidden');
}

function closeDeleteStudentModal() {
    document.getElementById('deleteStudentModal').classList.add('hidden');
    studentToDelete = null;
}

function confirmDeleteStudent() {
    if (!studentToDelete) return;

    const token = document.querySelector('input[name="_token"]').value;
    const deleteBtn = document.getElementById('confirmDeleteStudentBtn');
    const originalText = deleteBtn.textContent;
    deleteBtn.textContent = 'Archiving...';
    deleteBtn.disabled = true;

    fetch(`/${routePrefix}/students/${studentToDelete.studentID}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Student archived successfully!');
            closeDeleteStudentModal();
            location.reload();
        } else {
            alert(data.message || 'Error archiving student.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while archiving the student.');
    })
    .finally(() => {
        deleteBtn.textContent = originalText;
        deleteBtn.disabled = false;
    });
}

// ==================== SEARCHABLE PARENT DROPDOWN FUNCTIONS ====================

// Parent data from the server (sorted alphabetically)
const parentsList = @json($parentsList);

// Initialize searchable dropdowns when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeParentSearch('addStudentParentSearch', 'addStudentParentDropdown', 'addStudentParentId', 'addStudentSelectedParent', 'addStudentSelectedParentText', false);
    initializeParentSearch('linkParentSearch', 'linkParentDropdown', 'linkParentId', 'linkSelectedParent', 'linkSelectedParentText', true);
});

function initializeParentSearch(inputId, dropdownId, hiddenId, selectedContainerId, selectedTextId, required) {
    const searchInput = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);

    if (!searchInput || !dropdown) return;

    let currentIndex = -1;
    let filteredParents = [];

    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        showParentDropdown(inputId, dropdownId, searchInput.value);
    });

    // Filter on input
    searchInput.addEventListener('input', function() {
        showParentDropdown(inputId, dropdownId, searchInput.value);
        currentIndex = -1;
    });

    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.parent-option');

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex = Math.min(currentIndex + 1, items.length - 1);
            updateHighlight(items, currentIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex = Math.max(currentIndex - 1, 0);
            updateHighlight(items, currentIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (currentIndex >= 0 && items[currentIndex]) {
                items[currentIndex].click();
            } else if (items.length > 0) {
                // Select the first item if none highlighted
                items[0].click();
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.add('hidden');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

function updateHighlight(items, index) {
    items.forEach((item, i) => {
        if (i === index) {
            item.classList.add('bg-blue-100');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('bg-blue-100');
        }
    });
}

function showParentDropdown(inputId, dropdownId, searchTerm) {
    const dropdown = document.getElementById(dropdownId);
    const search = searchTerm.toLowerCase().trim();

    // Filter parents based on search term
    let filtered = parentsList;
    if (search) {
        filtered = parentsList.filter(parent =>
            parent.name.toLowerCase().includes(search) ||
            parent.email.toLowerCase().includes(search)
        );
    }

    // Build dropdown HTML
    if (filtered.length === 0) {
        dropdown.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 italic">No parents found</div>';
    } else {
        dropdown.innerHTML = filtered.map(parent => `
            <div class="parent-option px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0"
                 onclick="selectParent('${inputId}', '${dropdownId}', ${parent.id}, '${escapeHtml(parent.name)}', '${escapeHtml(parent.email)}')">
                <div class="font-medium text-gray-900">${escapeHtml(parent.name)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(parent.email)}</div>
            </div>
        `).join('');
    }

    dropdown.classList.remove('hidden');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function selectParent(inputId, dropdownId, parentId, name, email) {
    const dropdown = document.getElementById(dropdownId);
    const searchInput = document.getElementById(inputId);

    // Determine which set of elements to update based on input ID
    if (inputId === 'addStudentParentSearch') {
        document.getElementById('addStudentParentId').value = parentId;
        document.getElementById('addStudentSelectedParentText').textContent = `${name} (${email})`;
        document.getElementById('addStudentSelectedParent').classList.remove('hidden');
        searchInput.value = '';
        searchInput.classList.add('hidden');
    } else if (inputId === 'linkParentSearch') {
        document.getElementById('linkParentId').value = parentId;
        document.getElementById('linkSelectedParentText').textContent = `${name} (${email})`;
        document.getElementById('linkSelectedParent').classList.remove('hidden');
        searchInput.value = '';
        searchInput.classList.add('hidden');
    }

    dropdown.classList.add('hidden');
}

function clearAddStudentParent() {
    document.getElementById('addStudentParentId').value = '';
    document.getElementById('addStudentSelectedParent').classList.add('hidden');
    document.getElementById('addStudentParentSearch').classList.remove('hidden');
    document.getElementById('addStudentParentSearch').value = '';
}

function clearLinkParent() {
    document.getElementById('linkParentId').value = '';
    document.getElementById('linkSelectedParent').classList.add('hidden');
    document.getElementById('linkParentSearch').classList.remove('hidden');
    document.getElementById('linkParentSearch').value = '';
}
</script>
@endsection
