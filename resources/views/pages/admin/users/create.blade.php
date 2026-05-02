@extends('layouts/layoutMaster')

@section('title', 'User Management - Create')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Create New User</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
          @csrf
          
          <!-- Basic Info Row -->
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" placeholder="Umair Yaqoob" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="umair@example.com" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-12">
              <label class="form-label">Select Primary Role</label>
              <select name="role" id="role-select" class="form-select" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="manager">Manager</option>
                <option value="supervisor">Supervisor</option>
                <option value="intern">Intern</option>
              </select>
            </div>
          </div>

          <hr class="my-4">

          <!-- Module Selection Section -->
          <div id="module-wrapper" style="display: none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Module Permissions</h5>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check-all">
                <label class="form-check-label" for="check-all">Select All for this Role</label>
              </div>
            </div>

            <div class="row" id="module-container">
              @foreach($modules as $module)
                <div class="col-md-4 mb-2 module-item" data-role="{{ $module->role_access }}">
                  <div class="card border shadow-none mb-0">
                    <div class="card-body py-2 px-3">
                      <div class="form-check mb-0">
                        <input class="form-check-input module-checkbox" type="checkbox" name="modules[]" 
                               value="{{ $module->slug }}" id="mod_{{ $module->id }}">
                        <label class="form-check-label fw-medium" for="mod_{{ $module->id }}">
                          {{ $module->name }}
                        </label>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $module->slug }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Create User</button>
            <a href="{{ route('laravel-example-user-management') }}" class="btn btn-label-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role-select');
    const moduleWrapper = document.getElementById('module-wrapper');
    const checkAll = document.getElementById('check-all');

    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        const items = document.querySelectorAll('.module-item');
        
        if (selectedRole) {
            moduleWrapper.style.display = 'block';
            let visibleCount = 0;

            items.forEach(item => {
                const itemRole = item.getAttribute('data-role');
                if (itemRole === selectedRole || selectedRole === 'admin') {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                    // Uncheck hidden items to prevent accidental permission assignment
                    item.querySelector('input').checked = false;
                }
            });
        } else {
            moduleWrapper.style.display = 'none';
        }
    });

    // Select All Toggle
    checkAll.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.module-item[style*="display: block"] .module-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
});
</script>
@endsection