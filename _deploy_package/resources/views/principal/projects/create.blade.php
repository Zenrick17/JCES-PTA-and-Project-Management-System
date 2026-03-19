@extends('layouts.pr-sidebar')

@section('title', 'Create Project')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Project</h1>
        <p class="text-gray-600 mt-1">Define objectives, budgets, and timelines.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4">
            <p class="font-semibold">Please fix the errors below.</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="project-create-form" method="POST" action="{{ route('principal.projects.store') }}" class="bg-white rounded-lg shadow p-6 space-y-6" novalidate>
        @csrf
        <div id="project-create-errors" class="hidden bg-red-50 border border-red-200 text-red-700 rounded-lg p-4" role="alert" aria-live="polite">
            <p class="font-semibold">Please fix the errors below.</p>
            <ul class="mt-2 list-disc list-inside text-sm"></ul>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Project Name</label>
            <input type="text" name="project_name" value="{{ old('project_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            @error('project_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Goals</label>
            <textarea name="goals" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('goals') }}</textarea>
            @error('goals')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Target Budget</label>
                <input type="number" step="0.01" min="0" name="target_budget" value="{{ old('target_budget') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('target_budget')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Target Completion</label>
                <input type="date" name="target_completion_date" value="{{ old('target_completion_date') }}" min="{{ old('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                @error('target_completion_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Project Status</label>
            <select name="project_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach ($statusOptions as $option)
                    <option value="{{ $option }}" {{ old('project_status', 'created') === $option ? 'selected' : '' }}>
                        {{ $option === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $option)) }}
                    </option>
                @endforeach
            </select>
            @error('project_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('principal.projects.index') }}" class="px-4 py-2 text-gray-700 border rounded-md">Cancel</a>
            <button id="project-create-submit" type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed">Create Project</button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('project-create-form');
        if (!form) return;

        const submitButton = document.getElementById('project-create-submit');
        const errorBox = document.getElementById('project-create-errors');
        const errorList = errorBox ? errorBox.querySelector('ul') : null;
        const budgetField = form.querySelector('[name="target_budget"]');
        const startDateField = form.querySelector('[name="start_date"]');
        const completionDateField = form.querySelector('[name="target_completion_date"]');
        const requiredFields = Array.from(form.querySelectorAll('[required]'));

        const buildErrors = () => {
            const messages = [];

            requiredFields.forEach((field) => {
                if (!field.value || (field.type === 'text' && !field.value.trim())) {
                    const label = field.closest('div')?.querySelector('label')?.textContent?.trim();
                    if (label) {
                        messages.push(`${label} is required.`);
                    }
                }
            });

            if (budgetField && budgetField.value !== '') {
                const budgetValue = Number(budgetField.value);
                if (Number.isNaN(budgetValue) || budgetValue < 0) {
                    messages.push('Target Budget must be 0 or more.');
                }
            }

            if (startDateField && completionDateField && startDateField.value && completionDateField.value) {
                if (completionDateField.value < startDateField.value) {
                    messages.push('Target Completion must be on or after Start Date.');
                }
            }

            return messages;
        };

        const renderErrors = (messages) => {
            if (!errorBox || !errorList) return;
            errorList.innerHTML = '';
            if (messages.length === 0) {
                errorBox.classList.add('hidden');
                return;
            }

            messages.forEach((message) => {
                const item = document.createElement('li');
                item.textContent = message;
                errorList.appendChild(item);
            });
            errorBox.classList.remove('hidden');
        };

        const syncCompletionMin = () => {
            if (startDateField && completionDateField) {
                completionDateField.min = startDateField.value || '';
            }
        };

        const validateForm = () => {
            syncCompletionMin();
            const messages = buildErrors();
            renderErrors(messages);
            if (submitButton) {
                submitButton.disabled = messages.length > 0;
            }
            return messages.length === 0;
        };

        form.addEventListener('input', validateForm);
        form.addEventListener('change', validateForm);

        form.addEventListener('submit', (event) => {
            if (!validateForm()) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        validateForm();
    });
</script>
@endsection
