@extends('layouts.pr-sidebar')

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
        <form method="POST" action="{{ route('principal.store-account') }}" enctype="multipart/form-data">
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
                        Add Photo
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
    preview.innerHTML = '<img src="data:image/jpeg;base64,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" alt="Profile Preview" class="w-full h-full object-cover">';
}
</script>
@endsection