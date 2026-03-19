<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Teacher Dashboard - <?php echo e(config('app.name', 'JCES-PTA')); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar { width: 13rem; transition: width 0.3s ease; }
        .sidebar-collapsed .sidebar { width: 5rem; }
        .sidebar-collapsed .sidebar-label { display: none; }
        .sidebar-collapsed .sidebar-logo { justify-content: center; }
        .sidebar-collapsed .sidebar-link { justify-content: center; }
        .sidebar-collapsed .sidebar-profile { justify-content: center; }
        .sidebar-collapsed .sidebar-padding { padding-left: 0.75rem; padding-right: 0.75rem; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="flex h-screen" x-data="{ sidebarOpen: true }" :class="sidebarOpen ? '' : 'sidebar-collapsed'">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar bg-gradient-to-b from-green-500 to-green-600 flex flex-col overflow-hidden">
            <!-- Logo and School Name -->
            <div class="p-6 bg-white border-r border-gray-200 sidebar-padding">
                <div class="flex items-center gap-3 sidebar-logo">
                    <img src="http://127.0.0.1:8000/images/logos/jces-logo.png"
                         alt="JCES Logo"
                         class="w-12 h-12 object-contain">
                    <div class="sidebar-label" x-cloak>
                        <div class="text-sm font-semibold text-gray-900">J. Cruz Sr.</div>
                        <div class="text-xs text-gray-600">Elementary School</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-3 py-6 space-y-2">
                <a href="<?php echo e(route('teacher.dashboard')); ?>"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-white <?php echo e(request()->routeIs('teacher.dashboard') ? 'bg-green-700' : 'hover:bg-green-700'); ?> rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Dashboard</span>
                </a>

                <a href="<?php echo e(route('teacher.announcements')); ?>"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-white <?php echo e(request()->routeIs('teacher.announcements') ? 'bg-green-700' : 'hover:bg-green-700'); ?> rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Announcement</span>
                </a>

                <a href="<?php echo e(route('teacher.projects.index')); ?>"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-white <?php echo e(request()->routeIs('teacher.projects.*') ? 'bg-green-700' : 'hover:bg-green-700'); ?> rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                        <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Projects</span>
                </a>

                <a href="<?php echo e(route('teacher.payments.index')); ?>"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-white <?php echo e(request()->routeIs('teacher.payments.*') ? 'bg-green-700' : 'hover:bg-green-700'); ?> rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Payments</span>
                </a>

                <a href="<?php echo e(route('teacher.users')); ?>"
                   class="sidebar-link flex items-center gap-3 px-4 py-3 text-white <?php echo e(request()->routeIs('teacher.users') ? 'bg-green-700' : 'hover:bg-green-700'); ?> rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Parents</span>
                </a>

            </nav>

            <!-- User Profile and Sign Out -->
            <div class="p-4 border-t border-green-700">
                <a href="<?php echo e(route('sign-out')); ?>" class="sidebar-link flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors w-full mb-3 block">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="sidebar-label" x-cloak>Sign Out</span>
                </a>

                <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-profile flex items-center gap-3 px-4 py-3 text-white hover:bg-green-700 rounded-lg font-medium transition-colors w-full">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <span class="text-green-600 font-semibold text-sm">
                            <?php echo e(strtoupper(substr(Auth::user()->first_name ?? Auth::user()->username ?? 'T', 0, 1) . substr(Auth::user()->last_name ?? '', 0, 1))); ?>

                        </span>
                    </div>
                    <div class="text-white sidebar-label" x-cloak>
                        <div class="text-sm font-semibold"><?php echo e(Auth::user()->name ?? Auth::user()->username ?? 'Teacher'); ?></div>
                        <div class="text-xs opacity-90">Teacher</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top Header -->
            <header class="bg-gradient-to-r from-green-50 to-green-100 border-b border-gray-200 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button class="text-gray-700" @click="sidebarOpen = !sidebarOpen" :aria-expanded="sidebarOpen.toString()" aria-controls="sidebar" aria-label="Toggle sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-2xl font-bold text-gray-900"><?php echo $__env->yieldContent('title', 'Home'); ?></h1>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                <?php echo $__env->make('components.flash-messages', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->yieldContent('content'); ?>
            </main>
        </div>
    </div>

    <!-- Floating Message Popup -->
    <?php echo $__env->make('components.message-floating-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Web Developement\JCES-PTA and Project Management System\resources\views/layouts/te-sidebar.blade.php ENDPATH**/ ?>