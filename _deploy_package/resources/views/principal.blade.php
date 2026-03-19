@extends('layouts.pr-sidebar')

@section('title', 'Dashboard')

@section('content')
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
                <div class="text-2xl font-bold text-gray-900 text-center mt-3">1</div>
                <div class="text-xs text-gray-600 text-center mt-1">This month</div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Active Parents</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-1a4 4 0 00-4-4h-1m-4 5v-1a4 4 0 00-4-4H5a4 4 0 00-4 4v1h12zm4-12a4 4 0 11-8 0 4 4 0 018 0zm6 2a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3">87</div>
                <div class="text-xs text-gray-600 text-center mt-1">+3 new this month</div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Upcoming Events</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3">3</div>
                <div class="text-xs text-gray-600 text-center mt-1">This month</div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                    <span>Active Projects</span>
                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 text-center mt-3">3</div>
                <div class="text-xs text-gray-600 text-center mt-1">5 completed this month</div>
            </div>
        </div>

        <!-- Content Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-300 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Recent Announcements</h2>
                <div class="space-y-3">
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3 border-l-4 border-l-red-600">
                        <div class="text-sm font-semibold text-gray-900">Parent–Teacher Conference</div>
                        <div class="text-xs text-gray-600">Scheduled for March 15–17, 2024. Please confirm your attendance.</div>
                        <div class="text-xs text-gray-500 mt-1">2 hours ago</div>
                    </div>
                    <div class="rounded-lg border border-purple-100 bg-purple-50 p-3 border-l-4 border-l-purple-600">
                        <div class="text-sm font-semibold text-gray-900">Science Fair Winners</div>
                        <div class="text-xs text-gray-600">Congratulations to all participants in the annual science fair.</div>
                        <div class="text-xs text-gray-500 mt-1">1 day ago</div>
                    </div>
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 border-l-4 border-l-blue-600">
                        <div class="text-sm font-semibold text-gray-900">School Maintenance</div>
                        <div class="text-xs text-gray-600">Building maintenance scheduled for this weekend.</div>
                        <div class="text-xs text-gray-500 mt-1">3 days ago</div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-300 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Upcoming Schedule</h2>
                <div class="space-y-3">
                    <div class="rounded-lg border border-red-100 bg-red-50 p-3 border-l-4 border-l-red-600">
                        <div class="flex items-start gap-3">
                            <div class="text-center">
                                <div class="text-[10px] font-semibold text-gray-500">DEC 2</div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">PTA Coordination Meeting</div>
                                <div class="text-xs text-gray-600">Finalize school fundraising and improvement plans.</div>
                                <div class="text-xs text-gray-500 mt-1">9:00 AM – 10:00 AM</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-purple-100 bg-purple-50 p-3 border-l-4 border-l-purple-600">
                        <div class="flex items-start gap-3">
                            <div class="text-center">
                                <div class="text-[10px] font-semibold text-gray-500">NOV 5</div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Quarterly Academic Review</div>
                                <div class="text-xs text-gray-600">Evaluate student performance and upcoming exam preparations.</div>
                                <div class="text-xs text-gray-500 mt-1">1:30 PM – 2:00 PM</div>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 border-l-4 border-l-blue-600">
                        <div class="flex items-start gap-3">
                            <div class="text-center">
                                <div class="text-[10px] font-semibold text-gray-500">OCT 20</div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">School Safety Audit</div>
                                <div class="text-xs text-gray-600">Inspect facilities to ensure a safe learning environment.</div>
                                <div class="text-xs text-gray-500 mt-1">10:00 AM – 11:00 AM</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection