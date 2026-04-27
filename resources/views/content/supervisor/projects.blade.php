@extends('layouts/layoutMaster')

@section('title', 'Projects')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
<style>
    /* Custom spacing for action buttons in table */
    .table-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    /* Ensure Select2 fits the theme */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #dbdade;
    }
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Project Management</h4>
    </div>

    {{-- Alerts Section --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @if(session('error')) <li>{{ session('error') }}</li> @endif
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Create Project Card --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="ti ti-plus me-2"></i>Create New Project</h5>
        </div>
        <div class="card-body">
            {{-- Removed .modal-content class as it was breaking the card layout --}}
            <form id="createProjectForm" action="{{ route('supervisor.projects.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    {{-- Project Title --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Project Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter Project Title" required>
                    </div>

                    {{-- Select Intern --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Select Intern</label>
                        <select name="eti_id" id="internSelect" class="form-select select2" required>
                            <option value="">-- Choose Intern --</option>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->eti_id }}" data-email="{{ $intern->email }}">
                                    {{ $intern->name }} ({{ $intern->eti_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Intern Email (Readonly) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Intern Email</label>
                        <input type="email" name="email" id="internEmail" class="form-control bg-light" required readonly>
                    </div>

                    {{-- Tech Stack --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Technology Stack</label>
                        <input type="text" name="tech_stack" class="form-control" placeholder="e.g. Laravel, React">
                    </div>

                    {{-- Difficulty --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Difficulty Level</label>
                        <select name="difficulty_level" class="form-select" required>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate" selected>Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>

                    {{-- Dates --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="pstatus" class="form-select" required>
                            <option value="Pending">Pending</option>
                            <option value="Ongoing" selected>Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description / Requirements</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter project requirements..." required></textarea>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="ti ti-device-floppy me-1"></i> Save Project
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Project List Card --}}
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Project List</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Project Info</th>
                        <th>Tech & Difficulty</th>
                        <th>Dates</th>
                        <th>Assigned By</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
    @forelse($projects as $index => $project)
        <tr>
            {{-- Index --}}
            <td class="align-middle">{{ $index + 1 }}</td>
            
            {{-- Project Info --}}
            <td class="align-middle">
                <div class="d-flex flex-column">
                    <span class="fw-bold text-heading" style="font-size: 0.95rem;">{{ $project->title }}</span>
                    <small class="text-muted"><i class="ti ti-mail ti-xs me-1"></i>{{ $project->email }}</small>
                </div>
            </td>
            
            {{-- Tech & Difficulty --}}
            <td class="align-middle">
                <div class="mb-1 small fw-medium">{{ $project->tech_stack }}</div>
                <span class="badge bg-label-info badge-sm">{{ $project->difficulty_level }}</span>
            </td>
            
            {{-- Dates --}}
            <td class="align-middle">
                <div class="small">
                    <span class="text-muted">Start:</span> {{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}
                </div>
                <div class="small">
                    <span class="text-muted">End:</span> <span class="text-danger">{{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}</span>
                </div>
            </td>
            
            {{-- Assigned By --}}
            <td class="align-middle">
                <span class="text-nowrap small"><i class="ti ti-user-check ti-xs me-1"></i>{{ $project->supervisor_name }}</span>
            </td>
            
            {{-- Status --}}
            <td class="align-middle">
                @php
                    $statusClass = match(strtolower($project->pstatus)) {
                        'ongoing' => 'primary',
                        'completed', 'approved' => 'success',
                        'pending', 'submitted' => 'warning',
                        'rejected', 'expired' => 'danger',
                        default => 'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($project->pstatus) }}</span>
            </td>
            
            {{-- Professional Dropdown Actions --}}
            <td class="text-center align-middle">
                <div class="d-inline-block">
                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="{{ route('supervisor.projects.tasks', $project->project_id) }}" class="dropdown-item">
                            <i class="ti ti-list-check me-2"></i> View Tasks
                        </a>
                        
                        @if(!($project->chat ?? null))
                            <button type="button" class="dropdown-item initiate-chat-btn text-success" 
                                    data-bs-toggle="modal" data-bs-target="#initiateChatModal"
                                    data-project-id="{{ $project->project_id }}"
                                    data-project-title="{{ $project->title }}"
                                    data-intern-id="{{ $project->eti_id }}">
                                <i class="ti ti-messages me-2"></i> Start Chat
                            </button>
                        @else
                            <a href="{{ route('chat.show', $project->project_id) }}" class="dropdown-item text-success">
                                <i class="ti ti-message-dots me-2"></i> Open Group Chat
                            </a>
                        @endif

                        <a href="{{ route('supervisor.projects.edit', $project->project_id) }}" class="dropdown-item">
                            <i class="ti ti-edit me-2"></i> Edit Project
                        </a>
                        
                        <div class="dropdown-divider"></div>
                        
                        <form action="{{ route('supervisor.projects.delete', $project->project_id) }}" method="POST" 
                              onsubmit="return confirm('Delete this project?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="ti ti-trash me-2"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center py-5 text-muted">No projects found.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
</div>

{{-- Initiate Chat Modal --}}
<div class="modal fade" id="initiateChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="initiateChatForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Initiate Chat: <span id="modalProjectTitle" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted small mb-0">Select participants for the chat group</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllInterns">
                        <label class="form-check-label" for="selectAllInterns">Select All</label>
                    </div>
                </div>
                
                <div class="list-group list-group-flush border rounded" style="max-height: 300px; overflow-y: auto;">
                    @foreach($interns as $intern)
                        <label class="list-group-item d-flex align-items-center cursor-pointer">
                            <input class="form-check-input me-3 intern-check" type="checkbox" name="participants[]" value="{{ $intern->eti_id }}">
                            <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ strtoupper(substr($intern->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $intern->name }}</span>
                                <small class="text-muted">{{ $intern->eti_id }}</small>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Chat Group</button>
            </div>
        </form>
    </div>
</div>

@section('page-script')
<script>
    function initProjectPage() {    
        const $internSelect = $('#internSelect');
        
        if ($.fn.select2 && !$internSelect.hasClass("select2-hidden-accessible")) {
            $internSelect.select2({
                placeholder: "-- Choose Intern --",
                allowClear: true,
                dropdownParent: $('#createProjectForm').parent() 
            });
        }

        $internSelect.on('change select2:select', function() {
            const email = $(this).find(':selected').data('email');
            $('#internEmail').val(email || '');
        });

        $(document).on('click', '.initiate-chat-btn', function() {
            const btn = $(this);
            const projectId = btn.data('project-id');
            const projectTitle = btn.data('project-title');
            const assignedInternEtiId = btn.data('intern-id'); 

            $('#modalProjectTitle').text(projectTitle);
            
            const actionUrl = "{{ route('supervisor.projects.chat.initiate', ':id') }}".replace(':id', projectId);
            $('#initiateChatForm').attr('action', actionUrl);

            $('.intern-check').prop('checked', false);
            $('#selectAllInterns').prop('checked', false);

            if (assignedInternEtiId) {
                $(`.intern-check[value="${assignedInternEtiId}"]`).prop('checked', true);
            }
        });

        $(document).on('change', '#selectAllInterns', function() {
            const isChecked = $(this).prop('checked');
            $('.intern-check').prop('checked', isChecked);
        });

        $(document).on('change', '.intern-check', function() {
            const total = $('.intern-check').length;
            const checked = $('.intern-check:checked').length;
            $('#selectAllInterns').prop('checked', total === checked);
        });
    }

    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', initProjectPage);
    } else {
        $(document).ready(initProjectPage);
    }
</script>
@endsection
@endsection