@extends('layouts.ad-sidebar')

@section('title', 'Create Account')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-br from-green-100 to-green-50 rounded-2xl shadow-sm border border-green-200 p-8">
                    <form method="POST" action="#" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- First Name -->
                        <div class="mb-6">
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="Maria"
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                   required>
                        </div>

                        <!-- Last Name -->
                        <div class="mb-6">
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="Santos"
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                   required>
                        </div>

                        <!-- Role -->
                        <div class="mb-6">
                            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select id="role" 
                                    name="role" 
                                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors appearance-none"
                                    required>
                                <option value="parent" selected>Parent</option>
                                <option value="teacher">Teacher</option>
                                <option value="principal">Principal</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <!-- Contact -->
                        <div class="mb-6">
                            <label for="contact" class="block text-sm font-semibold text-gray-700 mb-2">Contact</label>
                            <input type="text" 
                                   id="contact" 
                                   name="contact" 
                                   value="09123456789"
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                   required>
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="MariaSantos@gmail.com"
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                   required>
                        </div>

                        <!-- Password -->
                        <div class="mb-8">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   value="MariaSantos123"
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors"
                                   required>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition-colors duration-200">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Photo Upload Section -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-green-100 to-green-50 rounded-2xl shadow-sm border border-green-200 p-8 flex flex-col items-center justify-center min-h-[500px]">
                    <!-- Photo Preview Circle -->
                    <div class="relative mb-6">
                        <div class="w-56 h-56 rounded-full overflow-hidden bg-white border-4 border-green-200 shadow-lg">
                            <img id="photo-preview" 
                                 src="https://via.placeholder.com/224/f97316/ffffff?text=User" 
                                 alt="User Photo" 
                                 class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Upload Button -->
                    <div class="relative">
                        <input type="file" 
                               id="photo" 
                               name="photo" 
                               accept="image/*"
                               class="hidden"
                               onchange="previewPhoto(event)">
                        <label for="photo" 
                               class="cursor-pointer inline-block px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-semibold rounded-lg shadow-md transition-colors duration-200">
                            Add Photo
                        </label>
                    </div>

                    <p class="text-xs text-gray-600 mt-4 text-center">
                        Click "Add Photo" to upload a profile picture
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Photo Preview -->
    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection