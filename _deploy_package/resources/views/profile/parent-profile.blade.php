<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.parent-profile-form')
                </div>
            </div>

            <!-- Students Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="w-full">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ __('Students') }}
                    </h3>

                    @if(isset($students) && count($students) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500"></th>
                                        <th class="text-center py-3 px-4 text-sm font-medium text-gray-500">Grade</th>
                                        <th class="text-center py-3 px-4 text-sm font-medium text-gray-500">Section</th>
                                        <th class="text-center py-3 px-4 text-sm font-medium text-gray-500">Year</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4 text-sm text-gray-900">{{ $student->student_name }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600 text-center">{{ $student->grade_level }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600 text-center">{{ $student->section }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-600 text-center">{{ $student->academic_year }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No students linked to your account.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Update Password Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
