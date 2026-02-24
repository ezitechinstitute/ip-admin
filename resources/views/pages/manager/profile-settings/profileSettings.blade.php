@extends('layouts/layoutMaster')

@section('title', 'Manager Profile Settings')

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
  <div class="col-md-12" style="display: flex; justify-content: center; align-items: center; flex-direction: column">
    <div class="nav-align-top" style="width: 100%;">
      <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2" role="tablist">
        <li class="nav-item">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-admin-profile" role="tab">
            <i class="icon-base ti tabler-user icon-sm me-1_5"></i> Manager Profile Settings
          </button>
        </li>
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
    <div class="tab-content p-0 bg-transparent shadow-none border-0" style="min-width: 700px !important; mx: auto;">

      <!-- Change Password -->
      <div class="tab-pane fade show active" id="tab-admin-profile" role="tabpanel">
        <form action="{{ route('manager.profile.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="card mb-6">
            <h5 class="card-header d-flex" style="justify-content: space-between;">Profile Information
              <button class="btn-sm btn btn-outline-primary waves-effect m-3! waves-light" tabindex="0"
                aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                data-bs-target="#addTechnologyModal"><i class="icon-base ti icon-15px tabler-lock me-1"></i>Change
                Password</button>

            </h5>


            <div class="card-body">
              <div class="row gy-6">
                <div class="col-12 d-flex justify-center" style="flex-direction: column; align-items: center">
                  <label class="form-label d-block mb-4">Admin Avatar (type: jpeg, png, jpg, svg - less than
                    2MB)</label>
                  <div class="d-flex align-items-start align-items-sm-center gap-6">
                    <img src="{{ 
    $manager->image 
        ? (str_starts_with($manager->image, 'data:image')
            ? $manager->image 
            : asset($manager->image)) 
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

              </div>
            </div>

            <hr class="my-0">

            <div class="card-body pt-6">
              <div class="row gy-4 gx-6 mb-6">
                <div class="col-md-6">
                  <label class="form-label">Manager Name</label>
                  <input name="name" class="form-control" type="text" value="{{ $manager->name }}" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Manager Email</label>
                  <input name="email" class="form-control" type="email" value="{{ $manager->email }}" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Contact</label>
                  <input name="contact" class="form-control" type="tel" value="{{ $manager->contact }}" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Emergency Contact</label>
                  <input name="emergency_contact" class="form-control" type="tel"
                    value="{{ $manager->emergency_contact }}" required />
                </div>
              </div>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </div>
        </form>
      </div>


    </div>
  </div>
</div>

 <!-- Change password -->
      <div class="modal fade" id="addTechnologyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-simple modal-dialog-centered modal-add-new-role">
          <div class="modal-content p-2">
            <div class="modal-body">
              <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              <div class="text-start mb-6">
                <h5 class="role-title">Change Manager Password</h5>
              </div>
              <form id="changeAdminPass" class="row g-3" method="POST"
                action="{{route('manager.password.update')}}">
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
                    <input type="password" id="confirm_password" class="form-control" name="new_password_confirmation"
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