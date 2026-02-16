@extends('layouts/layoutMaster')

@section('title', 'Admin Settings')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-admin-profile" role="tab">
            <i class="icon-base ti tabler-user icon-sm me-1_5"></i> Admin Profile
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-email-config" role="tab">
            <i class="icon-base ti tabler-mail icon-sm me-1_5"></i> Email Configuration
          </button>
        </li>
        <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-advanced-settings" role="tab">
            <i class="icon-base ti tabler-settings-automation icon-sm me-1_5"></i> Advanced Settings
          </button>
        </li>
        {{-- <li class="nav-item">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-audit-logs" role="tab">
            <i class="icon-base ti tabler-file-analytics icon-sm me-1_5"></i> Audit Logs
          </button>
        </li> --}}
      </ul>
    </div>
    {{-- Error Messages --}}
    @if($errors->any())
    @foreach($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endforeach
    @endif

    {{-- Success Message --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Auto-hide script --}}
    <script>
      setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000); // 5 seconds
    </script>
    <div class="tab-content p-0 bg-transparent shadow-none border-0">
      <!-- Change password -->
      <div class="modal fade" id="addTechnologyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-simple modal-dialog-centered modal-add-new-role">
          <div class="modal-content p-2">
            <div class="modal-body">
              <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="text-start mb-6">
                <h5 class="role-title">Change Admin Password</h5>
              </div>
              <form id="changeAdminPass" class="row g-3" method="POST"
                action="{{route('admin.password.update.admin')}}">
                @csrf

                {{-- Current Password --}}
                <div class="col-12 form-password-toggle">
                  <label class="form-label" for="current_password">Current Password <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="current_password" class="form-control" name="current_password"
                      placeholder="············" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                  <span class="text-danger small error-msg" id="current_password_error"></span>
                </div>

                {{-- New Password --}}
                <div class="col-12 form-password-toggle">
                  <label class="form-label" for="new_password">New Password <span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="new_password" class="form-control" name="new_password"
                      placeholder="············" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                  <span class="text-danger small error-msg" id="new_password_error"></span>
                </div>

                {{-- Confirm Password --}}
                <div class="col-12 form-password-toggle">
                  <label class="form-label" for="confirm_password">Confirm Password <span
                      class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="confirm_password" class="form-control" name="confirm_password"
                      placeholder="············" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                  <span class="text-danger small error-msg" id="confirm_password_error"></span>
                </div>

                <div class="col-12 text-end mt-4">
                  <button type="reset" class="btn btn-sm btn-label-secondary me-2"
                    data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-sm btn-primary">Change Password</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Change Password -->
      <div class="tab-pane fade show active" id="tab-admin-profile" role="tabpanel">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card mb-6">
            <h5 class="card-header d-flex" style="justify-content: space-between;">Identity Management
              <button class="btn-sm btn btn-outline-primary waves-effect m-3! waves-light" tabindex="0"
                aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                data-bs-target="#addTechnologyModal"><i class="icon-base ti icon-15px tabler-lock me-1"></i>Change
                Password</button>

            </h5>


            <div class="card-body">
              <div class="row gy-6">
                <div class="col-md-6">
                  <label class="form-label d-block mb-4">Admin Avatar (type: jpeg, png, jpg, svg - less than
                    2MB)</label>
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    <img src="{{ 
    $admin->image 
        ? (str_starts_with($admin->image, 'data:image')
            ? $admin->image 
            : asset($admin->image)) 
        : asset('assets/img/branding/ezitech.png') 
    }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                    <div class="button-wrapper">
                      <label for="upload" class="btn btn-primary me-3 btn-sm mb-2" tabindex="0">
                        <span>Upload Avatar</span>
                        <input type="file" id="upload" name="avatar" hidden accept="image/*"
                          onchange="previewAvatar(event)" />
                      </label>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <label class="form-label d-block mb-4">System Logo (Brand) - (type: jpeg, png, jpg, svg - less than
                    2MB)</label>
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    <div
                      class="d-flex align-items-center justify-content-center bg-lighter rounded w-px-100 h-px-100 p-2">
                      <img src="{{ $settings->logo_url }}" alt="system-logo" class="mw-100 mh-100"
                        id="systemLogoPreview" />
                    </div>
                    <div class="button-wrapper">
                      <label for="uploadLogo" class="btn btn-outline-primary me-3 btn-sm mb-2" tabindex="0">
                        <span>Upload Logo</span>
                        <input type="file" id="uploadLogo" name="system_logo" hidden accept="image/*"
                          onchange="previewLogo(event)" />
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-0">

            <div class="card-body pt-6">
              <div class="row gy-4 gx-6 mb-6">
                <div class="col-md-6">
                  <label class="form-label">Admin Name</label>
                  <input name="name" class="form-control" type="text" value="{{ $admin->name }}" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Notification Email (Admin)</label>
                  <input name="email" class="form-control" type="email" value="{{ $admin->email }}" required />
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Save Profile & Logo</button>
            </div>
          </div>
        </form>
      </div>

      <div class="tab-pane fade" id="tab-email-config" role="tabpanel">
        <div class="card mb-6">
          <h5 class="card-header">SMTP Settings & Rules</h5>
          <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
              @csrf
              <div class="form-check form-switch mb-4">
                <input name="smtp_active_check" class="form-check-input" type="checkbox" id="activeRule1" {{
                  $settings->smtp_active_check ? 'checked' : '' }}>
                <label class="form-check-label" for="activeRule1">Inactive/Active</label>
              </div>
              <div class="row gy-4 gx-6 mb-6">
                <div class="col-md-3">
                  <label class="form-label">SMTP Port</label>
                  <input name="smtp_port" placeholder="Enter smtp port..." class="form-control" type="number"
                    value="{{ $settings->smtp_port ?? 587 }}" />
                </div>
                <div class="col-md-3">
                  <label class="form-label">SMTP Host</label>
                  <input name="smtp_host" class="form-control" type="text" value="{{ $settings->smtp_host }}"
                    placeholder="smtp.gmail.com" />
                </div>

                {{-- <div class="col-md-4">
                  <label class="form-label">Username</label>
                  <input name="smtp_username" class="form-control" placeholder="Enter username..." type="text"
                    value="{{ $settings->smtp_username }}" />
                </div> --}}
                <div class="col-md-3">
                  <label class="form-label">Email</label>
                  <input name="smtp_email" placeholder="Enter smtp email..." class="form-control" type="email"
                    value="{{ $settings->smtp_email }}" />
                </div>

                <div class="col-md-3 form-password-toggle form-control-validation">
                  <label class="form-label" for="password">Password</label>
                  <div class="input-group input-group-merge">
                    <input type="password" id="smtp_password" class="form-control" name="smtp_password"
                      value="{{ $settings->smtp_password }}"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" />
                    <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                  </div>
                </div>
              </div>
              <hr class="my-6">
              <h5 class="mb-4">Notification Rules</h5>
              <div class="form-check form-switch mb-4">
                <input name="notify_intern_reg" class="form-check-input" type="checkbox" id="rule1" {{
                  $settings->notify_intern_reg ? 'checked' : '' }}>
                <label class="form-check-label" for="rule1">Notify Admin on new Intern Registration</label>
              </div>
              {{-- <div class="form-check form-switch mb-4">
                <input name="notify_expense" class="form-check-input" type="checkbox" id="rule2" {{
                  $settings->notify_expense ? 'checked' : '' }}>
                <label class="form-check-label" for="rule2">Notify Manager on Expense Approval Request</label>
              </div> --}}
              <button type="submit" class="btn btn-primary">Update Configuration</button>
            </form>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="tab-advanced-settings" role="tabpanel">
        <div class="card mb-6">
          <h5 class="card-header">System Presets</h5>
          <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
              @csrf
              <div class="row gy-4 gx-6 mb-6">
                <div class="col-md-6">
                  <label class="form-label">Pagination Limits</label>
                  <select name="pagination_limit" class="select2 form-select">
                    <option value="15" {{ $settings->pagination_limit == 15 ? 'selected' : '' }}>15</option>
                    <option value="25" {{ $settings->pagination_limit == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $settings->pagination_limit == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $settings->pagination_limit == 100 ? 'selected' : '' }}>100</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Interview Timeout Duration (Days)</label>
                  <input name="interview_timeout" type="number" class="form-control"
                    value="{{ $settings->interview_timeout ?? 30 }}" />
                </div>

                <div class="col-md-6 mt-4">
                  <label class="form-label">Expense Categories (Comma Separated)</label>
                  <input name="expense_categories" type="text" class="form-control" id="expenseTags"
                    value="{{ is_array($settings->expense_categories) ? implode(', ', $settings->expense_categories) : $settings->expense_categories }}"
                    placeholder="Write category and press enter or comma" />
                  <small class="text-muted">Example: Office Supplies, Refreshment, Travel, Maintenance</small>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Internship Duration Presets (Months)</label>
                  <select name="internship_duration" class="select2 form-select">
                    <option value="1" {{ $settings->internship_duration == 1 ? 'selected' : '' }}>1 Month</option>
                    <option value="2" {{ $settings->internship_duration == 2 ? 'selected' : '' }}>2 Months</option>
                    <option value="3" {{ $settings->internship_duration == 3 ? 'selected' : '' }}>3 Months</option>
                    <option value="6" {{ $settings->internship_duration == 6 ? 'selected' : '' }}>6 Months</option>
                    <option value="12" {{ $settings->internship_duration == 12 ? 'selected' : '' }}>12 Months</option>
                  </select>
                  <small class="text-muted">Default duration that will be assigned to new internship postings.</small>
                </div>
              </div>
              <h5 class="mt-8 mb-4">Role Permission Toggles</h5>
              <div class="table-responsive border rounded mb-6">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Permission</th>
                      <th class="text-center">Admin</th>
                      <th class="text-center">Manager</th>
                      <th class="text-center">Supervisor</th>
                      <th class="text-center">Intern</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Export Data (CSV / Excel)</td>
                       <td class="text-center">
      <input type="checkbox" name="export_permissions[admin]" class="form-check-input" value="1"
        {{ (isset($settings->export_permissions['admin']) && $settings->export_permissions['admin'] == 1) ? 'checked' : '' }}>
    </td>

                      <td class="text-center">
                        <input type="checkbox" name="export_permissions[manager]" class="form-check-input" value="2" {{
                          (isset($settings->export_permissions['manager']) && $settings->export_permissions['manager']
                        == 1) ? 'checked' : '' }}>
                      </td>

                      <td class="text-center">
                        <input type="checkbox" name="export_permissions[supervisor]" class="form-check-input" value="3"
                          {{ (isset($settings->export_permissions['supervisor']) &&
                        $settings->export_permissions['supervisor'] == 1) ? 'checked' : '' }}>
                      </td>

                      <td class="text-center">
                        <input type="checkbox" name="export_permissions[intern]" class="form-check-input" value="4" {{
                          (isset($settings->export_permissions['intern']) && $settings->export_permissions['intern'] ==
                        1) ? 'checked' : '' }}>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <button type="submit" class="btn btn-primary">Save Advanced Settings</button>
            </form>
          </div>
        </div>
      </div>

      {{-- <div class="tab-pane fade" id="tab-audit-logs" role="tabpanel">
        <div class="card">...</div>
      </div> --}}

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Avatar Preview
function previewAvatar(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('uploadedAvatar').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Logo Preview
function previewLogo(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('systemLogoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('changeAdminPass');
    const currentPass = document.getElementById('current_password');
    const newPass = document.getElementById('new_password');
    const confirmPass = document.getElementById('confirm_password');

    // Live validation function
    function validateField(input, errorId, validationLogic) {
        const errorElement = document.getElementById(errorId);
        const result = validationLogic();

        if (result.isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid'); // Optional: Green border
            errorElement.innerText = '';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            errorElement.innerText = result.message;
        }
        return result.isValid;
    }

    // Individual logic for each field
    const checks = {
        current: () => ({
            isValid: currentPass.value.trim() !== '',
            message: 'Current password is required'
        }),
        new: () => ({
            isValid: newPass.value.length >= 6,
            message: newPass.value.trim() === '' ? 'New password is required' : 'Password must be at least 6 characters'
        }),
        confirm: () => ({
            isValid: confirmPass.value === newPass.value && confirmPass.value !== '',
            message: confirmPass.value === '' ? 'Please confirm your password' : 'Passwords do not match'
        })
    };

    // Attach "input" event for LIVE feedback
    currentPass.addEventListener('input', () => validateField(currentPass, 'current_password_error', checks.current));
    newPass.addEventListener('input', () => {
        validateField(newPass, 'new_password_error', checks.new);
        if (confirmPass.value !== '') validateField(confirmPass, 'confirm_password_error', checks.confirm);
    });
    confirmPass.addEventListener('input', () => validateField(confirmPass, 'confirm_password_error', checks.confirm));

    // Final check on submit
    form.addEventListener('submit', function (e) {
        const isCurrentValid = validateField(currentPass, 'current_password_error', checks.current);
        const isNewValid = validateField(newPass, 'new_password_error', checks.new);
        const isConfirmValid = validateField(confirmPass, 'confirm_password_error', checks.confirm);

        if (!isCurrentValid || !isNewValid || !isConfirmValid) {
            e.preventDefault();
        }
    });
});
</script>
@endpush