@extends('layouts/layoutMaster')

@section('title', 'Create New Task')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Create New Task</h4>
      <p class="text-muted mb-0">Assign tasks to interns</p>
    </div>
    <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">
      <i class="ti ti-arrow-left me-1"></i>Back to Tasks
    </a>
  </div>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
  <strong>Please fix the following errors:</strong>
  <ul class="mb-0 mt-2">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-list-details me-2"></i>Task Details
        </h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('supervisor.tasks.store') }}" id="taskForm">
          @csrf

          <div class="row g-4 mb-4">
            <div class="col-md-12">
              <label class="form-label fw-semibold">Task Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                     value="{{ old('title') }}" placeholder="Enter task title" required>
              @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-12">
              <label class="form-label fw-semibold">Description</label>
              <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                        rows="4" placeholder="Describe the task requirements">{{ old('description') }}</textarea>
              @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Assign to Intern <span class="text-danger">*</span></label>
              <select name="intern_id" class="form-select @error('intern_id') is-invalid @enderror" required>
                <option value="">Select Intern</option>
                @foreach($interns as $intern)
                <option value="{{ $intern->int_id }}" {{ old('intern_id') == $intern->int_id ? 'selected' : '' }}>
                  {{ $intern->name }} ({{ $intern->int_technology }})
                </option>
                @endforeach
              </select>
              @error('intern_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Deadline <span class="text-danger">*</span></label>
              <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                     value="{{ old('deadline') }}" min="{{ date('Y-m-d') }}" required>
              @error('deadline')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Points <span class="text-danger">*</span></label>
              <input type="number" name="points" class="form-control @error('points') is-invalid @enderror" 
                     value="{{ old('points', 10) }}" min="1" max="100" step="1" required>
              @error('points')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Maximum points for this task (1-100)</small>
            </div>
          </div>

          <hr class="my-4">

          <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary px-5">
              <i class="ti ti-device-floppy me-2"></i>Create Task
            </button>
            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary px-4">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  $('.form-select').select2();
  $('.flatpickr').flatpickr();
});
</script>
@endpush