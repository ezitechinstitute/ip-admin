@extends('layouts.blankLayout')
@section('title', 'Ezitech Internship Application')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
     style="background: linear-gradient(135deg, #e8f0fe 0%, #f1f5ff 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="row g-0">

            <!-- ═══════════════════════════════
                 LEFT PANEL
            ════════════════════════════════ -->
            <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white"
                 style="background: linear-gradient(160deg, #1a3c6e 0%, #2563eb 60%, #3b82f6 100%);">

              <!-- Logo -->
              <div>
                <img src="{{ asset('assets/img/branding/logo.png') }}"
                     alt="Ezitech Logo" width="160" class="mb-4"
                     style="filter: brightness(0) invert(1);">

                <h2 class="fw-bold fs-4 lh-sm mb-3">
                  Start Your Internship<br>Journey With Us
                </h2>
                <p class="small lh-lg mb-0" style="opacity:.75;">
                  Complete the 3-step form to find your best internship path at Ezitech Institute.
                </p>
              </div>

              <!-- Step Indicators -->
              <div class="d-flex flex-column gap-3 my-4">

                <!-- Step 1 — Active -->
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;background:#fff;color:#2563eb;font-size:.82rem;">
                    1
                  </div>
                  <div>
                    <div class="fw-semibold small">Personal Information</div>
                    <div class="small" style="opacity:.6;">Name, contact & basic details</div>
                  </div>
                </div>

                <!-- Step 2 -->
                <div class="d-flex align-items-center gap-3" style="opacity:.45;">
                  <div class="rounded-circle border border-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;font-size:.82rem;border-opacity:.4;">
                    2
                  </div>
                  <div>
                    <div class="fw-semibold small">Academic &amp; Interest</div>
                    <div class="small" style="opacity:.6;">University, skills &amp; preferences</div>
                  </div>
                </div>

                <!-- Step 3 -->
                <div class="d-flex align-items-center gap-3" style="opacity:.45;">
                  <div class="rounded-circle border border-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;font-size:.82rem;border-opacity:.4;">
                    3
                  </div>
                  <div>
                    <div class="fw-semibold small">Assignment</div>
                    <div class="small" style="opacity:.6;">Complete your task to apply</div>
                  </div>
                </div>
              </div>

              <!-- Banner Illustration -->
              <img src="{{ asset('assets/img/illustrations/auth-register-multisteps-illustration.png') }}"
                   class="img-fluid rounded-3 mt-auto" alt="Internship Banner" style="opacity:.8;">
            </div>


            <!-- ═══════════════════════════════
                 RIGHT FORM
            ════════════════════════════════ -->
            <div class="col-lg-7 bg-white">
              <div class="p-4 p-md-5">

                <!-- Mobile logo -->
                <div class="d-lg-none text-center mb-4">
                  <img src="{{ asset('assets/img/branding/logo.png') }}" alt="Ezitech Logo" width="150">
                </div>

                <!-- Heading -->
                <h4 class="fw-bold mb-1 text-dark">Personal Information</h4>
                <p class="text-muted small mb-4">Step 1 of 3 — Fill in your basic details to get started.</p>

                <!-- ── Progress Bar ── -->
                <div class="mb-4">

                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-medium">Registration Progress</span>
                    <span class="badge rounded-pill px-3 py-1 small fw-semibold"
                          style="background:#eff6ff;color:#2563eb;">Step 1 / 3</span>
                  </div>

                  <!-- Segmented track (colorless pending steps) -->
                  <div class="d-flex align-items-center gap-2">
                    <!-- Segment 1 — completed/active -->
                    <div class="flex-fill rounded-pill" style="height:7px;background:#2563eb;"></div>
                    <!-- Segment 2 — pending -->
                    <div class="flex-fill rounded-pill" style="height:7px;background:#e2e8f0;"></div>
                    <!-- Segment 3 — pending -->
                    <div class="flex-fill rounded-pill" style="height:7px;background:#e2e8f0;"></div>
                  </div>

                  <!-- Step dot labels -->
                  <div class="d-flex justify-content-between mt-2">

                    <div class="d-flex flex-column align-items-center gap-1">
                      <div class="rounded-circle d-flex align-items-center justify-content-center"
                           style="width:22px;height:22px;background:#2563eb;">
                        <i class="bx bx-check text-white" style="font-size:.75rem;"></i>
                      </div>
                      <span class="text-primary fw-semibold" style="font-size:.65rem;">Info</span>
                    </div>

                    <div class="d-flex flex-column align-items-center gap-1">
                      <div class="rounded-circle d-flex align-items-center justify-content-center"
                           style="width:22px;height:22px;border:2px solid #cbd5e1;background:#fff;">
                        <span style="font-size:.65rem;color:#94a3b8;font-weight:600;">2</span>
                      </div>
                      <span class="text-muted" style="font-size:.65rem;">Academic</span>
                    </div>

                    <div class="d-flex flex-column align-items-center gap-1">
                      <div class="rounded-circle d-flex align-items-center justify-content-center"
                           style="width:22px;height:22px;border:2px solid #cbd5e1;background:#fff;">
                        <span style="font-size:.65rem;color:#94a3b8;font-weight:600;">3</span>
                      </div>
                      <span class="text-muted" style="font-size:.65rem;">Assignment</span>
                    </div>

                  </div>
                </div>
                <!-- /Progress Bar -->


                <!-- ── Form ── -->
                <form id="internshipForm" method="POST"
                      action="{{ route('internship.step2') }}"
                      enctype="multipart/form-data"
                      novalidate>
                  @csrf

                  <div class="row g-3">

                    <!-- Full Name -->
                    <div class="col-12">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Full Name <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-user"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 ps-1 @error('internUsername') is-invalid @enderror"
                               name="internUsername" id="internUsername"
                               placeholder="John Doe" required
                               value="{{ old('internUsername') }}">
                        <div class="invalid-feedback">Please enter your full name.</div>
                      </div>
                    </div>

                    <!-- Email -->
                    <div class="col-12">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Email Address <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-envelope"></i>
                        </span>
                        <input type="email"
                               class="form-control border-start-0 ps-1 @error('internEmail') is-invalid @enderror"
                               name="internEmail" id="internEmail"
                               placeholder="info@ezitech.org" required
                               value="{{ old('internEmail') }}">
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                      </div>
                      <div id="emailHint" class="form-text text-muted small mt-1" style="display:none;">
                        <i class="bx bx-info-circle me-1"></i>All updates will be sent to this email.
                      </div>
                    </div>

                    <!-- WhatsApp -->
                    <div class="col-12">
                      <label class="form-label fw-medium small text-dark mb-1">
                        WhatsApp Number <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <select name="internPhoneCountry" class="form-select bg-light flex-grow-0" style="max-width:95px;">
                          <option value="PK" selected>🇵🇰 PK</option>
                          <option value="US">🇺🇸 US</option>
                          <option value="UAE">🇦🇪 AE</option>
                          <option value="GB">🇬🇧 UK</option>
                        </select>
                        <input type="tel"
                               class="form-control @error('internPhone') is-invalid @enderror"
                               name="internPhone" id="internPhone"
                               placeholder="+92 3XX XXXXXXX" required
                               value="{{ old('internPhone', '+92') }}">
                        <div class="invalid-feedback">Please enter your WhatsApp number.</div>
                      </div>
                      <div id="whatsappHint" class="form-text text-muted small mt-1" style="display:none;">
                        <i class="bx bxl-whatsapp me-1 text-success"></i>Use your active WhatsApp number.
                      </div>
                    </div>

                    <!-- Country + City -->
                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Country <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-globe"></i>
                        </span>
                        <select class="form-select border-start-0" name="country" id="country" required>
                          <option value="PK" {{ old('country','PK')=='PK' ? 'selected':'' }}>Pakistan</option>
                          <option value="US" {{ old('country')=='US' ? 'selected':'' }}>United States</option>
                          <option value="UAE" {{ old('country')=='UAE' ? 'selected':'' }}>UAE</option>
                          <option value="GB" {{ old('country')=='GB' ? 'selected':'' }}>United Kingdom</option>
                        </select>
                        <div class="invalid-feedback">Please select your country.</div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        City <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-map"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 ps-1 @error('internCity') is-invalid @enderror"
                               name="internCity" id="internCity"
                               placeholder="Your city" required
                               value="{{ old('internCity') }}">
                        <div class="invalid-feedback">Please enter your city.</div>
                      </div>
                    </div>

                    <!-- Gender + DOB -->
                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Gender <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-male-female"></i>
                        </span>
                        <select class="form-select border-start-0" name="gender" id="gender" required>
                          <option value="" disabled {{ old('gender') ? '':'selected' }}>Select Gender</option>
                          <option {{ old('gender')=='Male' ? 'selected':'' }}>Male</option>
                          <option {{ old('gender')=='Female' ? 'selected':'' }}>Female</option>
                          <option {{ old('gender')=='Other' ? 'selected':'' }}>Other</option>
                        </select>
                        <div class="invalid-feedback">Please select your gender.</div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Date of Birth <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-calendar"></i>
                        </span>
                        <input type="date"
                               class="form-control border-start-0 ps-1 @error('dob') is-invalid @enderror"
                               name="dob" id="dob" required
                               value="{{ old('dob') }}">
                        <div class="invalid-feedback">Please enter your date of birth.</div>
                      </div>
                    </div>

                    <!-- University -->
                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        University / Institute <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-buildings"></i>
                        </span>
                        <input type="text"
                               class="form-control border-start-0 ps-1 @error('university') is-invalid @enderror"
                               name="university" id="university"
                               placeholder="Your university" required
                               value="{{ old('university') }}">
                        <div class="invalid-feedback">Please enter your university.</div>
                      </div>
                    </div>

                    <!-- Technology Interest -->
                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Technology Interest <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-code-alt"></i>
                        </span>
                        <select class="form-select border-start-0" name="techInterest" id="techInterest" required>
                          <option value="" disabled {{ old('techInterest') ? '':'selected' }}>Select Interest</option>
                          <option {{ old('techInterest')=='MERN Stack' ? 'selected':'' }}>MERN Stack</option>
                          <option {{ old('techInterest')=='Frontend Development' ? 'selected':'' }}>Frontend Development</option>
                          <option {{ old('techInterest')=='Backend Development' ? 'selected':'' }}>Backend Development</option>
                          <option {{ old('techInterest')=='Python Development' ? 'selected':'' }}>Python Development</option>
                          <option {{ old('techInterest')=='UI/UX Design' ? 'selected':'' }}>UI/UX Design</option>
                        </select>
                        <div class="invalid-feedback">Please select your technology interest.</div>
                      </div>
                    </div>

                    <!-- Duration + Type -->
                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Duration <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-time-five"></i>
                        </span>
                        <select class="form-select border-start-0" name="internDuration" id="internDuration" required>
                          <option value="" disabled {{ old('internDuration') ? '':'selected' }}>-- Select --</option>
                          <option value="1 Month" {{ old('internDuration')=='1 Month' ? 'selected':'' }}>1 Month</option>
                          <option value="2 Month" {{ old('internDuration')=='2 Month' ? 'selected':'' }}>2 Month</option>
                          <option value="3 Month" {{ old('internDuration')=='3 Month' ? 'selected':'' }}>3 Month</option>
                          <option value="6 Month" {{ old('internDuration')=='6 Month' ? 'selected':'' }}>6 Month</option>
                        </select>
                        <div class="invalid-feedback">Please select a duration.</div>
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Internship Type <span class="text-danger">*</span>
                      </label>
                      <div class="input-group has-validation">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                          <i class="bx bx-laptop"></i>
                        </span>
                        <select class="form-select border-start-0" name="internType" id="internType" required>
                          <option value="" disabled {{ old('internType') ? '':'selected' }}>-- Select --</option>
                          <option value="Onsite" {{ old('internType')=='Onsite' ? 'selected':'' }}>Onsite</option>
                          <option value="Remote" {{ old('internType')=='Remote' ? 'selected':'' }}>Remote</option>
                        </select>
                        <div class="invalid-feedback">Please select internship type.</div>
                      </div>
                    </div>

                    <!-- Profile Image -->
                    <div class="col-12">
                      <label class="form-label fw-medium small text-dark mb-1">
                        Profile Image <span class="text-muted fw-normal">(optional)</span>
                      </label>
                      <input type="file" class="form-control bg-light"
                             name="profileImage" accept="image/*">
                      <div class="form-text text-muted small mt-1">
                        <i class="bx bx-image me-1"></i> JPG, PNG or GIF — max 2MB
                      </div>
                    </div>

                  </div><!-- /row g-3 -->

                  <!-- Submit Button -->
                  <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold rounded-3 py-3"
                            style="background:linear-gradient(90deg,#1d4ed8,#2563eb);border:none;letter-spacing:.3px;">
                      Continue to Step 2
                      <i class="bx bx-right-arrow-alt ms-2 fs-5 align-middle"></i>
                    </button>
                  </div>

                  <p class="text-center text-muted small mt-3 mb-0">
                    <i class="bx bx-lock-alt me-1"></i>Your information is secure and will not be shared.
                  </p>

                </form>
                <!-- /Form -->

              </div>
            </div>
            <!-- /Right Form -->

          </div><!-- /row g-0 -->
        </div><!-- /card -->

      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

  /* ── Hints on focus ── */
  const emailInput = document.getElementById('internEmail');
  const emailHint  = document.getElementById('emailHint');
  emailInput.addEventListener('focus', () => emailHint.style.display = 'block');
  emailInput.addEventListener('blur',  () => emailHint.style.display = 'none');

  const phoneInput = document.getElementById('internPhone');
  const phoneHint  = document.getElementById('whatsappHint');
  phoneInput.addEventListener('focus', () => phoneHint.style.display = 'block');
  phoneInput.addEventListener('blur',  () => phoneHint.style.display = 'none');


  /* ── Required field IDs in DOM order ── */
  const requiredFields = [
    'internUsername',
    'internEmail',
    'internPhone',
    'internCity',
    'gender',
    'dob',
    'university',
    'techInterest',
    'internDuration',
    'internType'
  ];

  const form = document.getElementById('internshipForm');

  /* ── Live validation — clear error as user types/selects ── */
  requiredFields.forEach(function (id) {
    const field = document.getElementById(id);
    if (!field) return;
    const evt = field.tagName === 'SELECT' ? 'change' : 'input';
    field.addEventListener(evt, function () {
      if (field.value.trim() !== '') {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      }
    });
  });


  /* ── Submit handler ── */
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    let firstInvalid = null;

    requiredFields.forEach(function (id) {
      const field = document.getElementById(id);
      if (!field) return;

      const val = field.value.trim();

      if (val === '') {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        if (!firstInvalid) firstInvalid = field;
      } else {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      }
    });

    /* Extra: email format check */
    if (emailInput.classList.contains('is-valid')) {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(emailInput.value.trim())) {
        emailInput.classList.add('is-invalid');
        emailInput.classList.remove('is-valid');
        if (!firstInvalid) firstInvalid = emailInput;
      }
    }

    if (firstInvalid) {
      /* Scroll + focus first empty/invalid field */
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => firstInvalid.focus(), 400);
      return;
    }

    /* ✅ All credentials filled — submit → controller redirects to assignment page */
    form.submit();
  });

});
</script>

@endsection