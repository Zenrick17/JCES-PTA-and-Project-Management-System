<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Proposed Project</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3"><?php echo e($stats['proposedProjects'] ?? 0); ?></div>
                <div class="text-xs text-gray-600 text-center mt-1">This month</div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Active Parents</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-4-4h-1m-4 5v-1a4 4 0 00-4-4H5a4 4 0 00-4 4v1h12zm4-12a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3"><?php echo e($stats['activeParents'] ?? 0); ?></div>
                <div class="text-xs text-gray-600 text-center mt-1">
                    <?php if(($stats['newParentsThisMonth'] ?? 0) > 0): ?>
                        +<?php echo e($stats['newParentsThisMonth']); ?> new this month
                    <?php else: ?>
                        No new parents this month
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Upcoming Events</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3"><?php echo e($stats['upcomingEvents'] ?? 0); ?></div>
                <div class="text-xs text-gray-600 text-center mt-1">This month</div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Active Projects</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3"><?php echo e($stats['activeProjects'] ?? 0); ?></div>
                <div class="text-xs text-gray-600 text-center mt-1">
                    <?php if(($stats['completedProjectsThisMonth'] ?? 0) > 0): ?>
                        <?php echo e($stats['completedProjectsThisMonth']); ?> completed this month
                    <?php else: ?>
                        No completions this month
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-300 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Recent Announcements</h2>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $recentAnnouncements ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4
                            <?php if($announcement->category === 'important'): ?> border-red-500
                            <?php elseif($announcement->category === 'notice'): ?> border-orange-500
                            <?php elseif($announcement->category === 'update'): ?> border-blue-500
                            <?php elseif($announcement->category === 'event'): ?> border-green-500
                            <?php else: ?> border-gray-500
                            <?php endif; ?>">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900"><?php echo e($announcement->title); ?></h3>
                                <span class="text-xs text-gray-400"><?php echo e($announcement->time_ago); ?></span>
                            </div>
                            <p class="text-xs text-gray-600 mt-2"><?php echo e(Str::limit($announcement->content, 80)); ?></p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    <?php if($announcement->category === 'important'): ?> bg-red-100 text-red-700
                                    <?php elseif($announcement->category === 'notice'): ?> bg-orange-100 text-orange-700
                                    <?php elseif($announcement->category === 'update'): ?> bg-blue-100 text-blue-700
                                    <?php elseif($announcement->category === 'event'): ?> bg-green-100 text-green-700
                                    <?php else: ?> bg-gray-100 text-gray-700
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($announcement->category)); ?>

                                </span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-500 text-center py-4">No recent announcements</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Upcoming Schedule</h2>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $upcomingSchedules ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4
                            <?php if($schedule->priority === 'high'): ?> border-red-500
                            <?php elseif($schedule->priority === 'medium'): ?> border-purple-500
                            <?php else: ?> border-blue-500
                            <?php endif; ?>">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900"><?php echo e($schedule->title); ?></h3>
                                <span class="text-xs text-gray-400"><?php echo e($schedule->formatted_date); ?></span>
                            </div>
                            <p class="text-xs text-gray-600 mt-2"><?php echo e(Str::limit($schedule->description, 80)); ?></p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    <?php if($schedule->priority === 'high'): ?> bg-red-100 text-red-700
                                    <?php elseif($schedule->priority === 'medium'): ?> bg-purple-100 text-purple-700
                                    <?php else: ?> bg-blue-100 text-blue-700
                                    <?php endif; ?>">
                                    <?php echo e(ucfirst($schedule->priority)); ?> Priority
                                </span>
                                <?php if($schedule->time_range): ?>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        <?php echo e($schedule->time_range); ?>

                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-gray-500 text-center py-4">No upcoming schedules</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pr-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Web Developement\JCES-PTA and Project Management System\resources\views/principal/dashboard.blade.php ENDPATH**/ ?>