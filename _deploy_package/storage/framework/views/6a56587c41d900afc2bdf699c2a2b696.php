<?php $__env->startSection('title', 'Audit Logs'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
                <p class="text-gray-600 mt-1">Authentication and audit events monitoring</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="<?php echo e(route('principal.reports')); ?>" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                    </svg>
                    Back to Reports
                </a>
                <div class="flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Audit Trail
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Events</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($logs->total()); ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed Logins</p>
                    <p class="text-2xl font-bold text-red-600"><?php echo e($logs->where('action', 'failed_login')->count()); ?></p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Successful Logins</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo e($logs->where('action', 'login')->count()); ?></p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Unique IPs</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo e($logs->pluck('ip_address')->unique()->count()); ?></p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M3 8a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M3 12a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="<?php echo e(route('principal.reports.security-logs')); ?>" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Users</option>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($user->userID); ?>" <?php echo e(request('user_id') == $user->userID ? 'selected' : ''); ?>>
                        <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select name="action" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">All Actions</option>
                    <optgroup label="Authentication">
                        <option value="login" <?php echo e(request('action') == 'login' ? 'selected' : ''); ?>>Login</option>
                        <option value="logout" <?php echo e(request('action') == 'logout' ? 'selected' : ''); ?>>Logout</option>
                        <option value="failed_login" <?php echo e(request('action') == 'failed_login' ? 'selected' : ''); ?>>Failed Login</option>
                        <option value="password_change" <?php echo e(request('action') == 'password_change' ? 'selected' : ''); ?>>Password Change</option>
                        <option value="account_locked" <?php echo e(request('action') == 'account_locked' ? 'selected' : ''); ?>>Account Locked</option>
                        <option value="permission_denied" <?php echo e(request('action') == 'permission_denied' ? 'selected' : ''); ?>>Permission Denied</option>
                    </optgroup>
                    <optgroup label="Project Management">
                        <option value="project_created" <?php echo e(request('action') == 'project_created' ? 'selected' : ''); ?>>Project Created</option>
                        <option value="project_updated" <?php echo e(request('action') == 'project_updated' ? 'selected' : ''); ?>>Project Updated</option>
                        <option value="project_deleted" <?php echo e(request('action') == 'project_deleted' ? 'selected' : ''); ?>>Project Deleted</option>
                        <option value="project_status_changed" <?php echo e(request('action') == 'project_status_changed' ? 'selected' : ''); ?>>Project Status Changed</option>
                    </optgroup>
                    <optgroup label="Milestones">
                        <option value="milestone_created" <?php echo e(request('action') == 'milestone_created' ? 'selected' : ''); ?>>Milestone Created</option>
                        <option value="milestone_completed" <?php echo e(request('action') == 'milestone_completed' ? 'selected' : ''); ?>>Milestone Completed</option>
                        <option value="milestone_deleted" <?php echo e(request('action') == 'milestone_deleted' ? 'selected' : ''); ?>>Milestone Deleted</option>
                    </optgroup>
                    <optgroup label="Announcements">
                        <option value="announcement_created" <?php echo e(request('action') == 'announcement_created' ? 'selected' : ''); ?>>Announcement Created</option>
                        <option value="announcement_updated" <?php echo e(request('action') == 'announcement_updated' ? 'selected' : ''); ?>>Announcement Updated</option>
                        <option value="announcement_deleted" <?php echo e(request('action') == 'announcement_deleted' ? 'selected' : ''); ?>>Announcement Deleted</option>
                    </optgroup>
                    <optgroup label="User Management">
                        <option value="user_created" <?php echo e(request('action') == 'user_created' ? 'selected' : ''); ?>>User Created</option>
                        <option value="user_updated" <?php echo e(request('action') == 'user_updated' ? 'selected' : ''); ?>>User Updated</option>
                        <option value="user_deleted" <?php echo e(request('action') == 'user_deleted' ? 'selected' : ''); ?>>User Deleted</option>
                        <option value="user_status_changed" <?php echo e(request('action') == 'user_status_changed' ? 'selected' : ''); ?>>User Status Changed</option>
                    </optgroup>
                    <optgroup label="Contributions & Payments">
                        <option value="contribution_submitted" <?php echo e(request('action') == 'contribution_submitted' ? 'selected' : ''); ?>>Contribution Submitted</option>
                        <option value="contribution_approved" <?php echo e(request('action') == 'contribution_approved' ? 'selected' : ''); ?>>Contribution Approved</option>
                        <option value="contribution_rejected" <?php echo e(request('action') == 'contribution_rejected' ? 'selected' : ''); ?>>Contribution Rejected</option>
                    </optgroup>
                    <optgroup label="Schedules">
                        <option value="schedule_created" <?php echo e(request('action') == 'schedule_created' ? 'selected' : ''); ?>>Schedule Created</option>
                        <option value="schedule_updated" <?php echo e(request('action') == 'schedule_updated' ? 'selected' : ''); ?>>Schedule Updated</option>
                        <option value="schedule_deleted" <?php echo e(request('action') == 'schedule_deleted' ? 'selected' : ''); ?>>Schedule Deleted</option>
                    </optgroup>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                <input type="text" name="ip_address" value="<?php echo e(request('ip_address')); ?>" placeholder="IP Address..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>

            <div class="md:col-span-2 lg:col-span-5 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    Filter
                </button>
                <a href="<?php echo e(route('principal.reports.security-logs')); ?>" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Audit Events</h3>
            <p class="text-sm text-gray-600 mt-1"><?php echo e($logs->total()); ?> audit events found</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 
                        <?php if($log->action === 'failed_login' || $log->action === 'account_locked' || $log->action === 'permission_denied'): ?> bg-red-50 <?php endif; ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div><?php echo e($log->timestamp->format('M d, Y')); ?></div>
                            <div class="text-xs text-gray-500"><?php echo e($log->timestamp->format('H:i:s')); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($log->action === 'login'): ?> bg-green-100 text-green-800
                                <?php elseif($log->action === 'logout'): ?> bg-blue-100 text-blue-800
                                <?php elseif($log->action === 'failed_login'): ?> bg-red-100 text-red-800
                                <?php elseif($log->action === 'password_change'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($log->action === 'account_locked'): ?> bg-orange-100 text-orange-800
                                <?php elseif($log->action === 'permission_denied'): ?> bg-purple-100 text-purple-800
                                <?php elseif(str_contains($log->action, 'project')): ?> bg-indigo-100 text-indigo-800
                                <?php elseif(str_contains($log->action, 'milestone')): ?> bg-purple-100 text-purple-800
                                <?php elseif(str_contains($log->action, 'announcement')): ?> bg-cyan-100 text-cyan-800
                                <?php elseif(str_contains($log->action, 'user')): ?> bg-teal-100 text-teal-800
                                <?php elseif(str_contains($log->action, 'contribution')): ?> bg-emerald-100 text-emerald-800
                                <?php elseif(str_contains($log->action, 'schedule')): ?> bg-pink-100 text-pink-800
                                <?php else: ?> bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($log->action === 'login'): ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                <?php elseif($log->action === 'logout'): ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                                    </svg>
                                <?php elseif($log->action === 'failed_login'): ?>
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                <?php endif; ?>
                                <?php echo e(str_replace('_', ' ', ucfirst($log->action))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($log->user): ?>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-red-600 font-medium text-xs">
                                        <?php echo e(substr($log->user->first_name, 0, 1)); ?><?php echo e(substr($log->user->last_name, 0, 1)); ?>

                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($log->user->first_name); ?> <?php echo e($log->user->last_name); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo e($log->user->email); ?></div>
                                </div>
                            </div>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">Unknown User</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="font-mono"><?php echo e($log->ip_address); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($log->success): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Success
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                Failed
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <?php if($log->error_message): ?>
                            <div class="text-red-600 text-xs"><?php echo e($log->error_message); ?></div>
                            <?php endif; ?>
                            <?php if($log->session_id): ?>
                            <div class="text-gray-500 text-xs">Session: <?php echo e(substr($log->session_id, 0, 8)); ?>...</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-lg font-medium">No audit logs found</p>
                            <p class="text-sm">Try adjusting your filters to see more results.</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($logs->hasPages()): ?>
        <div class="px-6 py-4 border-t border-gray-200">
            <?php echo e($logs->appends(request()->query())->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pr-sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Web Developement\JCES-PTA and Project Management System\resources\views/principal/reports/security-logs.blade.php ENDPATH**/ ?>