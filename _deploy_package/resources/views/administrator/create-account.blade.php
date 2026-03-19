@extends('layouts.ad-sidebar')

@section('title', 'Create Account')

@section('content')
<div class="p-6">

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Create Account Form -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <form method="POST" action="{{ route('administrator.store-account') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="flex gap-8">
                <!-- Left Side - Form Fields -->
                <div class="flex-1 space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('first_name') border-red-500 @enderror"
                               placeholder="Enter first name" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('last_name') border-red-500 @enderror"
                               placeholder="Enter last name" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select id="role" name="role" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('role') border-red-500 @enderror" required>
                            <option value="">Select Role</option>
                            <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                            <option value="administrator" {{ old('role') == 'administrator' ? 'selected' : '' }}>Administrator</option>
                            <option value="principal" {{ old('role') == 'principal' ? 'selected' : '' }}>Principal</option>
                            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Contact</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('phone') border-red-500 @enderror"
                               placeholder="Enter contact number" required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('email') border-red-500 @enderror"
                               placeholder="Enter email address" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-gray-50 @error('password') border-red-500 @enderror"
                               placeholder="Enter password" required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Side - Profile Photo -->
                <div class="w-80 flex flex-col items-center border border-gray-300 p-4 rounded-lg bg-gray-50">
                    <div class="relative mb-4">
                        <div id="photo-preview" class="w-48 h-48 bg-gray-100 rounded-full border-4 border-gray-200 overflow-hidden flex items-center justify-center">
                            <img src="{{ asset('images/icons/profile-default.jpg') }}" alt="Profile Preview" class="w-full h-full object-cover">
                        </div>
                        <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*">
                    </div>
                    <button type="button" onclick="document.getElementById('profile_photo').click()" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-md border-2 border-blue-600">
                        📷 Add Photo
                    </button>
                </div>
            </div>

            <!-- Create Account Button -->
            <div class="flex justify-end mt-8">
                <button type="submit" 
                        class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview uploaded image
document.getElementById('profile_photo').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Profile Preview" class="w-full h-full object-cover">`;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});

// Clear form function
function clearForm() {
    document.querySelector('form').reset();
    const preview = document.getElementById('photo-preview');
    preview.innerHTML = '<img src="{{ asset('images/icons/profile-default.jpg') }}" alt="Profile Preview" class="w-full h-full object-cover">';
}
</script>
@endsection