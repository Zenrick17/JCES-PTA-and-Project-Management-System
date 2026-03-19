@extends('layouts.ad-sidebar')

@section('title', 'Administrator Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Information Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="max-w-2xl">
            @include('profile.partials.admin-profile-form')
        </div>
    </div>

    <!-- Update Password Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="max-w-2xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</div>
@endsection