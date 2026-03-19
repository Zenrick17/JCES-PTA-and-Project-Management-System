@extends('layouts.te-sidebar')

@section('title', 'Messages')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Messages</h3>
        <p class="text-gray-500 mb-4">Create announcements and schedules using the floating button below.</p>
        <p class="text-sm text-gray-400">Click the green button in the bottom-right corner to get started.</p>
    </div>
</div>
@endsection
