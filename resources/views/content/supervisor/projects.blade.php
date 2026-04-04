@extends('layouts/layoutMaster')

@section('title', 'Projects')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Projects</h4>

    <!-- Alerts Section -->
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
    <form action="{{ route('supervisor.projects.store') }}" method="POST">
      @csrf

      <div class="row g-3">
        <div class="col-md-12">
          <label class="form-label">Project Title</label>
          <input type="text" name="title" class="form-control" placeholder="Enter Project Title" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Select Intern</label>
          <select name="eti_id" id="internSelect" class="form-select" required>
            <option value="">-- Choose Intern --</option>
            @foreach($interns as $intern)
              <option value="{{ $intern->eti_id }}" data-email="{{ $intern->email }}">{{ $intern->name }} ({{ $intern->eti_id }})</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Intern Email</label>
          <input type="email" name="email" id="internEmail" class="form-control" required readonly>
        </div>

        <div class="col-md-3">
          <label class="form-label">Technology Stack</label>
          <input type="text" name="tech_stack" class="form-control" placeholder="e.g. Laravel, React">
        </div>

        <div class="col-md-3">
          <label class="form-label">Difficulty Level</label>
          <select name="difficulty_level" class="form-control" required>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="col-md-3">
          <label class="form-label">End Date</label>
          <input type="date" name="end_date" class="form-control" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Duration</label>
          <input type="number" name="duration" class="form-control" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Days</label>
          <input type="number" name="days" class="form-control" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Project Marks</label>
          <input type="text" name="project_marks" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Obtained Marks</label>
          <input type="number" step="0.01" name="obt_marks" class="form-control" value="0">
        </div>

        <div class="col-md-6">
          <label class="form-label">Status</label>
          <select name="pstatus" class="form-control" required>
            <option value="Ongoing">Ongoing</option>
            <option value="Submitted">Submitted</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
            <option value="Expired">Expired</option>
            <option value="Completed">Completed</option>
            <option value="Pending">Pending</option>
          </select>
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">Create Project</button>
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
                <table class="table table-bordered">
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
    <td>{{ $project->start_date }}</td>
    <td>{{ $project->end_date }}</td>
    <td>{{ $project->supervisor_name }}</td>
    <td>{{ $project->pstatus }}</td>
    <td>
      <div class="d-flex gap-2">
        <a href="{{ route('supervisor.projects.tasks', $project->project_id) }}" class="btn btn-sm btn-primary">Tasks</a>
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
@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const internSelect = document.getElementById('internSelect');
    const internEmail = document.getElementById('internEmail');

    if (internSelect && internEmail) {
        internSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const email = selectedOption.getAttribute('data-email');
            internEmail.value = email || '';
        });
    }
});
</script>
@endsection
@endsection