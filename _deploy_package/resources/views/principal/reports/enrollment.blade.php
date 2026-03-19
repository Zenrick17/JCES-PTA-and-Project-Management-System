@extends('layouts.pr-sidebar')

@section('title', 'Enrollment Statistics')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Enrollment Statistics</h1>
                <p class="text-gray-600 mt-1">Track enrollment totals and trends by academic year and grade.</p>
            </div>
        </div>

        <form method="GET" class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                <select name="academic_year" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Years</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year }}" {{ $academicYear === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Students</p>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalStudents) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Active Students</p>
            <p class="text-3xl font-bold text-green-600">{{ number_format($activeStudents) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Active Rate</p>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($activeRate, 2) }}%</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Enrollment by Academic Year</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Academic Year</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($enrollmentByYear as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $row->academic_year }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-sm text-gray-500 text-center">No enrollment data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Enrollment by Grade Level</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade Level</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($enrollmentByGrade as $row)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $row->grade_level }}</td>
                                <td class="px-4 py-2 text-sm text-gray-900 text-right">{{ number_format($row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-4 text-sm text-gray-500 text-center">No enrollment data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
