@extends('layouts/layoutMaster')

@section('title', 'Curriculum Details')

@section('content')
{{-- Define JavaScript FIRST, before any HTML that uses it --}}
<script>
// Define functions immediately, before any HTML parsing
window.openAddProject = function() {
    console.log('openAddProject called');
    
    // Reset the form first
    const form = document.getElementById('projectForm');
    if (form) {
        form.reset();
    }
    
    // Remove any _method field
    const methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }
    
    // Set form action for store
    document.getElementById('projectForm').action = '{{ route("manager.curriculum.project.store") }}';
    document.getElementById('projectForm').method = 'POST';
    document.getElementById('projectModalLabel').textContent = 'Add Project';
    document.getElementById('project_status').value = '1';
    
    // Clear hidden ID
    const projectIdField = document.getElementById('project_id');
    if (projectIdField) {
        projectIdField.value = '';
    }
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
    modal.show();
    
    console.log('Modal ready for add');
};

window.editProject = function(project) {
    console.log('editProject called with:', project);
    
    try {
        // Set modal title
        document.getElementById('projectModalLabel').textContent = 'Edit Project';
        
        // IMPORTANT: Set form action for update - use the correct URL
        document.getElementById('projectForm').action = '{{ url("manager/curriculum/project") }}/' + project.cp_id;
        document.getElementById('projectForm').method = 'POST'; // Keep as POST but add _method=PUT
        
        // Add or update _method field to PUT
        let methodInput = document.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            document.getElementById('projectForm').appendChild(methodInput);
        }
        methodInput.value = 'PUT'; // This tells Laravel to treat it as PUT
        
        // Populate all fields
        document.getElementById('project_id').value = project.cp_id || '';
        document.getElementById('project_title').value = project.project_title || '';
        document.getElementById('sequence_order').value = project.sequence_order || '';
        document.getElementById('duration_weeks').value = project.duration_weeks || '';
        document.getElementById('project_description').value = project.project_description || '';
        document.getElementById('learning_objectives').value = project.learning_objectives || '';
        document.getElementById('deliverables').value = project.deliverables || '';
        document.getElementById('project_status').value = project.status == '1' ? '1' : '0';
        
        // Set supervisor if field exists
        const supervisorField = document.getElementById('assigned_supervisor');
        if (supervisorField) {
            supervisorField.value = project.assigned_supervisor || project.supervisor_id || '';
        }
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('projectModal'));
        modal.show();
        
        console.log('Form populated and modal shown');
        
    } catch (error) {
        console.error('Error in editProject:', error);
        alert('Error loading project data: ' + error.message);
    }
};

// Verify functions are loaded
console.log('Edit functions loaded:', {
    openAddProject: typeof window.openAddProject === 'function',
    editProject: typeof window.editProject === 'function'
});
</script>

<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Curriculum: {{ $curriculum->curriculum_name }}</h4>
        <a href="{{ route('manager.curriculum.index') }}" class="btn btn-secondary">Back to list</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Technology</dt>
                <dd class="col-sm-9">{{ $curriculum->technology->technology ?? 'N/A' }}</dd>

                <dt class="col-sm-3">Total Projects</dt>
                <dd class="col-sm-9">{{ $curriculum->total_projects }}</dd>

                <dt class="col-sm-3">Duration (weeks)</dt>
                <dd class="col-sm-9">{{ $curriculum->total_duration_weeks }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if($curriculum->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $curriculum->description }}</dd>
            </dl>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h5>Projects</h5>
        <button class="btn btn-primary" onclick="openAddProject()">Add Project</button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sequence</th>
                            <th>Project Title</th>
                            <th>Duration (weeks)</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-table-body">
                        @forelse($curriculum->projects as $project)
                            <tr>
                                <td>{{ $project->sequence_order }}</td>
                                <td>{{ $project->project_title }}</td>
                                <td>{{ $project->duration_weeks }}</td>
                                <td>{{ $project->supervisor->name ?? 'Unassigned' }}</td>
                                <td>
                                    @if($project->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-text-secondary dropdown-toggle hide-arrow"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="icon-base ti tabler-dots-vertical"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>
                                                <button class="dropdown-item"
                                                        onclick='editProject(@json($project))'>
                                                    <i class="icon-base ti tabler-edit me-2"></i> Edit
                                                </button>
                                            </li>

                                            <li>
                                                <form action="{{ route('manager.curriculum.project.destroy', $project->cp_id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete project?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="icon-base ti tabler-trash me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>

                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalLabel">Add Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="projectForm" method="POST" action="{{ route('manager.curriculum.project.store') }}">
                @csrf
                <input type="hidden" id="project_id" name="project_id" value="">
                <input type="hidden" name="curriculum_id" value="{{ $curriculum->curriculum_id }}">

                <div class="modal-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label" for="project_title">Project Title</label>
                            <input type="text" id="project_title" name="project_title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="sequence_order">Sequence Order</label>
                            <input type="number" id="sequence_order" name="sequence_order" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="duration_weeks">Duration (weeks)</label>
                            <input type="number" id="duration_weeks" name="duration_weeks" class="form-control" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="assigned_supervisor">Assigned Supervisor</label>
                            <select id="assigned_supervisor" name="assigned_supervisor" class="form-select">
                                <option value="">— Select supervisor —</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->manager_id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="project_description">Project Description</label>
                            <textarea id="project_description" name="project_description" rows="3" class="form-control" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="learning_objectives">Learning Objectives</label>
                            <textarea id="learning_objectives" name="learning_objectives" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="deliverables">Deliverables</label>
                            <textarea id="deliverables" name="deliverables" rows="2" class="form-control"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="project_status">Status</label>
                            <select id="project_status" name="status" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="projectSubmitBtn">Save Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
{{-- Keep this for any additional scripts --}}
@endsection