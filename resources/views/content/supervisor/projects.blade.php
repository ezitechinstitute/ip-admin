@extends('layouts/layoutMaster')

@section('title', 'Projects')

{{-- 1. Load the Select2 CSS --}}
@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

{{-- 2. Load the Select2 JS --}}
@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Projects</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Create Project</h5>
        </div>
        <div class="card-body">
            <form id="createProjectForm" action="{{ route('supervisor.projects.store') }}" method="POST" class="modal-content">
    @csrf

    <div class="row g-3">
        {{-- Project Title --}}
        <div class="col-md-12">
            <label class="form-label">Project Title</label>
            <input type="text" name="title" class="form-control" placeholder="Enter Project Title" required>
        </div>

        {{-- Select Intern --}}
        <div class="col-md-6">
            <label class="form-label">Select Intern (Search by Name or ID)</label>
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
        <div class="col-md-6">
            <label class="form-label">Intern Email</label>
            <input type="email" name="email" id="internEmail" class="form-control" required readonly>
        </div>

        {{-- Tech Stack --}}
        <div class="col-md-3">
            <label class="form-label">Technology Stack</label>
            <input type="text" name="tech_stack" class="form-control" placeholder="e.g. Laravel, React">
        </div>

        {{-- Difficulty --}}
        <div class="col-md-3">
            <label class="form-label">Difficulty Level</label>
            <select name="difficulty_level" class="form-select" required>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate" selected>Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
        </div>

        {{-- Dates --}}
        <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        {{-- Status --}}
        <div class="col-md-12">
            <label class="form-label">Status</label>
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
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Enter project requirements..." required></textarea>
        </div>

        {{-- Submit Button --}}
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">
                <i class="ti ti-plus me-1"></i> Create Project
            </button>
        </div>
    </div>
</form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Project List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Tech</th>
                            <th>Difficulty</th>
                            <th>Email</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Assigned By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $index => $project)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $project->title }}</td>
                                <td>{{ $project->tech_stack }}</td>
                                <td><span class="badge bg-label-info">{{ $project->difficulty_level }}</span></td>
                                <td>{{ $project->email }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}</td>
                                <td>{{ $project->supervisor_name }}</td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($project->pstatus)) {
                                            'ongoing' => 'primary',
                                            'completed', 'approved' => 'success',
                                            'pending', 'submitted' => 'warning',
                                            'rejected', 'expired' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $statusClass }}">{{ ucfirst($project->pstatus) }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('supervisor.projects.tasks', $project->project_id) }}" class="btn btn-sm btn-primary">Tasks</a>


                                        {{-- NEW: Initiate Chat Button --}}
                                        {{-- @if(!$project->chat) --}}
                                        @if(!($project->chat ?? null))
                                            {{-- Initiate Chat Button --}}
                                            <button type="button" 
                                                    class="btn btn-sm btn-label-success initiate-chat-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#initiateChatModal"
                                                    data-project-id="{{ $project->project_id }}"
                                                    data-project-title="{{ $project->title }}"
                                                    data-intern-id="{{ $project->eti_id }}"
                                                    data-intern-name="{{ $project->intern->name ?? 'Intern' }}"
                                                <i class="ti ti-messages"></i> Chat
                                            </button>
                                        @else
                                            {{-- Open Chat Button - Matches the new route name --}}
                                            <a href="{{ route('chat.show', $project->project_id) }}" class="btn btn-sm btn-success">
                                                <i class="ti ti-message-dots"></i> Open Chat
                                            </a>
                                        @endif


                                        <a href="{{ route('supervisor.projects.edit', $project->project_id) }}" class="btn btn-sm btn-label-secondary">Edit</a>
                                        <form action="{{ route('supervisor.projects.delete', $project->project_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? All associated tasks will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-label-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No projects found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="initiateChatModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="initiateChatForm" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Initiate Chat for <span id="modalProjectTitle"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted small mb-0">Select participants for the chat</p>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllInterns">
            <label class="form-check-label" for="selectAllInterns">Select All</label>
        </div>
    </div>
    
    <div class="list-group" style="max-height: 300px; overflow-y: auto;">
        @foreach($interns as $intern)
            <label class="list-group-item d-flex align-items-center">
                {{-- Added 'intern-check' class here --}}
                <input class="form-check-input me-3 intern-check" type="checkbox" name="participants[]" value="{{ $intern->eti_id }}">
                <div class="avatar avatar-sm me-2">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ strtoupper(substr($intern->name, 0, 1)) }}
                    </span>
                </div>
                <span>{{ $intern->name }} <small class="text-muted">({{ $intern->eti_id }})</small></span>
            </label>
        @endforeach
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
        // --- 1. SELECT2 INITIALIZATION ---
        const $internSelect = $('#internSelect');
        if ($.fn.select2 && !$internSelect.hasClass("select2-hidden-accessible")) {
            $internSelect.select2({
                placeholder: "-- Choose Intern --",
                allowClear: true,
                width: '100%'
            });
        }

        // --- 2. EMAIL AUTO-FILL LOGIC ---
        // Fills the readonly email input when an intern is selected in the creation form
        $internSelect.on('change select2:select', function() {
            const email = $(this).find(':selected').data('email');
            $('#internEmail').val(email || '');
        });

        // --- 3. INITIATE CHAT MODAL LOGIC (THE FIX) ---
        // This ensures the form POSTS to the correct unique ID route
        $(document).on('click', '.initiate-chat-btn', function() {
            const btn = $(this);
            const projectId = btn.data('project-id');
            const projectTitle = btn.data('project-title');
            const assignedInternEtiId = btn.data('intern-id'); // e.g., ETI-1001

            // Update modal text UI
            $('#modalProjectTitle').text(projectTitle);
            
            // 🔥 CRITICAL: Update the form action dynamically
            // This prevents the '405 Method Not Allowed' by hitting the correct POST route
            const actionUrl = "{{ route('supervisor.projects.chat.initiate', ':id') }}".replace(':id', projectId);
            $('#initiateChatForm').attr('action', actionUrl);

            // Reset all checkboxes in the modal
            $('.intern-check').prop('checked', false);
            $('#selectAllInterns').prop('checked', false);

            // Automatically check the box for the intern specifically assigned to this project
            if (assignedInternEtiId) {
                $(`.intern-check[value="${assignedInternEtiId}"]`).prop('checked', true);
            }
            
            console.log("Chat Modal Ready. Target URL: " + actionUrl);
        });

        // --- 4. SELECT ALL / BULK SELECTION LOGIC ---
        $(document).on('change', '#selectAllInterns', function() {
            const isChecked = $(this).prop('checked');
            $('.intern-check').prop('checked', isChecked);
        });

        // Optional: Uncheck "Select All" if a user manually unchecks a single intern
        $(document).on('change', '.intern-check', function() {
            if ($('.intern-check:checked').length === $('.intern-check').length) {
                $('#selectAllInterns').prop('checked', true);
            } else {
                $('#selectAllInterns').prop('checked', false);
            }
        });
    }

    // --- 5. ROBUST LOAD HANDLING ---
    // Prevents "$ is not defined" if jQuery loads late
    if (typeof jQuery === 'undefined') {
        window.addEventListener('load', initProjectPage);
    } else {
        $(document).ready(initProjectPage);
    }
</script>
@endsection
@endsection