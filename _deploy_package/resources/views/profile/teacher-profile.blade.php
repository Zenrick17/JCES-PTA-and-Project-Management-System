@extends('layouts.te-sidebar')

@section('title', 'Teacher Profile')

@section('content')
    <div class="space-y-6">
        <!-- Profile Information Section -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.teacher-profile-form')
            </div>
        </div>

        <!-- Update Password Section -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
@endsection