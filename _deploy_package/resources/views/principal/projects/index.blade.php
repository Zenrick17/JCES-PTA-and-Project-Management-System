@extends('layouts.pr-sidebar')

@section('title', 'Projects')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="w-full md:w-56">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statusOptions as $option)
                        <option value="{{ $option }}" {{ $status === $option ? 'selected' : '' }}>
                            {{ $option === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $option)) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:flex-1">
                <label class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Project name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Filter</button>
                <button type="button" id="openCreateProjectModal" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Create Project</button>
            </div>
        </form>
    </div>

    <div class="mt-6">
        @php
            $today = \Carbon\Carbon::today();
            $upcoming = $projects->filter(function ($project) use ($today) {
                $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                return $startDate ? $startDate->isSameDay($today) || $startDate->isAfter($today) : false;
            });
            $previous = $projects->filter(function ($project) use ($today) {
                $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : null;
                return $startDate ? $startDate->isBefore($today) : false;
            });
        @endphp

        <div class="space-y-8">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming</h2>
                <div class="space-y-4">
                    @forelse ($upcoming as $project)
                        @php
                            $detailMap = [];
                            $lines = preg_split('/\r\n|\r|\n/', $project->description ?? '');
                            foreach ($lines as $line) {
                                if (strpos($line, ':') !== false) {
                                    [$key, $value] = array_map('trim', explode(':', $line, 2));
                                    if (!empty($key)) {
                                        $detailMap[$key] = $value;
                                    }
                                }
                            }
                            $photo = $detailMap['Photo'] ?? null;
                            $title = $detailMap['Title'] ?? $project->project_name;
                            $time = $detailMap['Time'] ?? null;
                            $venue = $detailMap['Venue'] ?? null;
                            $objective = $project->goals ?? null;
                        @endphp
                        <div class="block bg-slate-50 border-l-4 border-blue-500 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-32 h-32 bg-white rounded-md border flex items-center justify-center overflow-hidden">
                                    @if ($photo)
                                        <img src="{{ $photo }}" alt="{{ $project->project_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs text-gray-400">No Image</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <a href="{{ route('principal.projects.show', $project->projectID) }}" class="hover:underline">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $project->project_name }}</h3>
                                        </a>
                                        <span class="px-2 py-1 text-xs font-medium rounded 
                                            {{ $project->project_status === 'created' ? 'bg-gray-100 text-gray-700' : '' }}
                                            {{ $project->project_status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $project->project_status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $project->project_status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $project->project_status === 'archived' ? 'bg-gray-100 text-gray-500' : '' }}
                                            {{ $project->project_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                            {{ $project->project_status === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                        </span>
                                    </div>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>Project Title: {{ $title }}</li>
                                        <li>Date: {{ optional($project->start_date)->format('F j, Y') }} - {{ optional($project->target_completion_date)->format('F j, Y') }}</li>
                                        @if ($time)
                                            <li>Time: {{ $time }}</li>
                                        @endif
                                        @if ($venue)
                                            <li>Venue: {{ $venue }}</li>
                                        @endif
                                        @if ($objective)
                                            <li>Objective: {{ $objective }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No upcoming projects.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Previous</h2>
                <div class="space-y-4">
                    @forelse ($previous as $project)
                        @php
                            $detailMap = [];
                            $lines = preg_split('/\r\n|\r|\n/', $project->description ?? '');
                            foreach ($lines as $line) {
                                if (strpos($line, ':') !== false) {
                                    [$key, $value] = array_map('trim', explode(':', $line, 2));
                                    if (!empty($key)) {
                                        $detailMap[$key] = $value;
                                    }
                                }
                            }
                            $photo = $detailMap['Photo'] ?? null;
                            $title = $detailMap['Title'] ?? $project->project_name;
                            $time = $detailMap['Time'] ?? null;
                            $venue = $detailMap['Venue'] ?? null;
                            $objective = $project->goals ?? null;
                        @endphp
                        <div class="block bg-purple-50 border-l-4 border-purple-500 rounded-lg p-6 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-32 h-32 bg-white rounded-md border flex items-center justify-center overflow-hidden">
                                    @if ($photo)
                                        <img src="{{ $photo }}" alt="{{ $project->project_name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs text-gray-400">No Image</span>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <a href="{{ route('principal.projects.show', $project->projectID) }}" class="hover:underline">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $project->project_name }}</h3>
                                        </a>
                                        <span class="px-2 py-1 text-xs font-medium rounded 
                                            {{ $project->project_status === 'created' ? 'bg-gray-100 text-gray-700' : '' }}
                                            {{ $project->project_status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $project->project_status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $project->project_status === 'completed' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $project->project_status === 'archived' ? 'bg-gray-100 text-gray-500' : '' }}
                                            {{ $project->project_status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                        ">
                                            {{ $project->project_status === 'created' ? 'Not Started' : ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                        </span>
                                    </div>
                                    <ul class="text-sm text-gray-600 space-y-1">
                                        <li>Project Title: {{ $title }}</li>
                                        <li>Date: {{ optional($project->start_date)->format('F j, Y') }} - {{ optional($project->target_completion_date)->format('F j, Y') }}</li>
                                        @if ($time)
                                            <li>Time: {{ $time }}</li>
                                        @endif
                                        @if ($venue)
                                            <li>Venue: {{ $venue }}</li>
                                        @endif
                                        @if ($objective)
                                            <li>Objective: {{ $objective }}</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No previous projects.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    </div>

    @php
        $shouldOpenCreateProjectModal = old('project_name')
            || old('start_date')
            || old('target_completion_date')
            || old('target_budget')
            || old('title')
            || old('venue')
            || old('objective')
            || old('time');
    @endphp

    <!-- Create Project Modal -->
    <div id="createProjectModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Create Project</h2>
                <button type="button" id="closeCreateProjectModal" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>
            <form id="create-project-modal-form" method="POST" action="{{ route('principal.projects.store') }}" enctype="multipart/form-data" class="p-6 space-y-4" novalidate>
                @csrf
                <div id="create-project-modal-errors" class="hidden bg-red-50 border border-red-200 text-red-700 rounded-lg p-4" role="alert" aria-live="polite">
                    <p class="font-semibold">Please fix the errors below.</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @if ($errors->any() && $shouldOpenCreateProjectModal)
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Add Photo</label>
                        <input type="file" name="project_photo" accept="image/*" class="mt-1 block w-full text-sm text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Project Name</label>
                        <input type="text" name="project_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Time</label>
                        <input type="time" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="target_completion_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Budget</label>
                        <input type="number" step="0.01" min="0" name="target_budget" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Venue</label>
                        <input type="text" name="venue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Objective</label>
                    <textarea name="objective" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" id="cancelCreateProjectModal" class="px-4 py-2 text-gray-700 border rounded-md">Cancel</button>
                    <button id="create-project-modal-submit" type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const openCreateProjectModal = document.getElementById('openCreateProjectModal');
    const closeCreateProjectModal = document.getElementById('closeCreateProjectModal');
    const cancelCreateProjectModal = document.getElementById('cancelCreateProjectModal');
    const createProjectModal = document.getElementById('createProjectModal');

    const openModal = () => {
        createProjectModal.classList.remove('hidden');
        createProjectModal.classList.add('flex');
    };

    const closeModal = () => {
        createProjectModal.classList.add('hidden');
        createProjectModal.classList.remove('flex');
    };

    openCreateProjectModal.addEventListener('click', openModal);
    closeCreateProjectModal.addEventListener('click', closeModal);
    cancelCreateProjectModal.addEventListener('click', closeModal);

    createProjectModal.addEventListener('click', (event) => {
        if (event.target === createProjectModal) {
            closeModal();
        }
    });

    const modalForm = document.getElementById('create-project-modal-form');
    const modalSubmit = document.getElementById('create-project-modal-submit');
    const modalErrorBox = document.getElementById('create-project-modal-errors');
    const modalErrorList = modalErrorBox ? modalErrorBox.querySelector('ul') : null;
    const modalBudgetField = modalForm?.querySelector('[name="target_budget"]');
    const modalStartDateField = modalForm?.querySelector('[name="start_date"]');
    const modalCompletionField = modalForm?.querySelector('[name="target_completion_date"]');
    const modalRequiredFields = modalForm ? Array.from(modalForm.querySelectorAll('[required]')) : [];
    let modalFormSubmitted = false;

    const buildModalErrors = () => {
        const messages = [];

        modalRequiredFields.forEach((field) => {
            if (!field.value || (field.type === 'text' && !field.value.trim())) {
                const label = field.closest('div')?.querySelector('label')?.textContent?.trim();
                if (label) {
                    messages.push(`${label} is required.`);
                }
            }
        });

        if (modalBudgetField && modalBudgetField.value !== '') {
            const budgetValue = Number(modalBudgetField.value);
            if (Number.isNaN(budgetValue) || budgetValue < 0) {
                messages.push('Total Budget must be 0 or more.');
            }
        }

        if (modalStartDateField && modalCompletionField && modalStartDateField.value && modalCompletionField.value) {
            if (modalCompletionField.value < modalStartDateField.value) {
                messages.push('End Date must be on or after Start Date.');
            }
        }

        return messages;
    };

    const renderModalErrors = (messages) => {
        if (!modalErrorBox || !modalErrorList) return;
        modalErrorList.innerHTML = '';

        if (messages.length === 0) {
            modalErrorBox.classList.add('hidden');
            return;
        }

        messages.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            modalErrorList.appendChild(item);
        });

        modalErrorBox.classList.remove('hidden');
    };

    const syncModalCompletionMin = () => {
        if (modalStartDateField && modalCompletionField) {
            modalCompletionField.min = modalStartDateField.value || '';
        }
    };

    const validateModalForm = (showErrors = false) => {
        syncModalCompletionMin();
        const messages = buildModalErrors();
        
        if (showErrors || modalFormSubmitted) {
            renderModalErrors(messages);
            if (modalSubmit) {
                modalSubmit.disabled = messages.length > 0;
            }
        }

        return messages.length === 0;
    };

    if (modalForm) {
        modalForm.addEventListener('input', () => validateModalForm(false));
        modalForm.addEventListener('change', () => validateModalForm(false));
        modalForm.addEventListener('submit', (event) => {
            modalFormSubmitted = true;
            if (!validateModalForm(true)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        syncModalCompletionMin();
    }

    const shouldOpenCreateProjectModal = @json($shouldOpenCreateProjectModal);

    if (shouldOpenCreateProjectModal) {
        modalFormSubmitted = true;
        if (modalErrorBox) {
            modalErrorBox.classList.remove('hidden');
        }
        openModal();
    }
</script>
@endsection
