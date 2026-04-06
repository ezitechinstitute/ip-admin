@extends('layouts.blankLayout')
@section('title', 'Ezitech — Skill Assessment')

@section('content')

<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
     style="background: linear-gradient(135deg, #e8f0fe 0%, #f1f5ff 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-10">

        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="row g-0">

            <!-- ═══════════════════════════════
                 LEFT PANEL  (identical to Step 1)
            ════════════════════════════════ -->
            <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 text-white"
                 style="background: linear-gradient(160deg, #1a3c6e 0%, #2563eb 60%, #3b82f6 100%);">

              <!-- Logo -->
              <div>
                <img src="{{ asset('assets/img/branding/logo.png') }}"
                     alt="Ezitech Logo" width="160" class="mb-4"
                     style="filter: brightness(0) invert(1);">

                <h2 class="fw-bold fs-4 lh-sm mb-3">
                  We're Getting to<br>Know You Better
                </h2>
                <p class="small lh-lg mb-0" style="opacity:.75;">
                  Your answers help us match you with the most suitable internship track at Ezitech.
                </p>
              </div>

              <!-- Step Indicators -->
              <div class="d-flex flex-column gap-3 my-4">

                <!-- Step 1 — Done -->
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;background:rgba(255,255,255,.25);font-size:.82rem;">
                    <i class="bx bx-check" style="font-size:1rem;"></i>
                  </div>
                  <div style="opacity:.7;">
                    <div class="fw-semibold small">Personal Information</div>
                    <div class="small" style="opacity:.6;">Completed ✓</div>
                  </div>
                </div>

                <!-- Step 2 — Active -->
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;background:#fff;color:#2563eb;font-size:.82rem;">
                    2
                  </div>
                  <div>
                    <div class="fw-semibold small">Skill Assessment</div>
                    <div class="small" style="opacity:.6;">Answer 6 quick questions</div>
                  </div>
                </div>

                <!-- Step 3 -->
                <div class="d-flex align-items-center gap-3" style="opacity:.45;">
                  <div class="rounded-circle border border-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                       style="width:36px;height:36px;font-size:.82rem;">
                    3
                  </div>
                  <div>
                    <div class="fw-semibold small">Assignment</div>
                    <div class="small" style="opacity:.6;">Complete your task to apply</div>
                  </div>
                </div>
              </div>

              <!-- Banner Illustration -->
              <img src="{{ asset('assets/img/illustrations/girl-with-laptop.png') }}"
                   class="img-fluid rounded-3 mt-auto" alt="Internship Banner" style="opacity:.8;">
            </div>


            <!-- ═══════════════════════════════
                 RIGHT — ASSESSMENT FORM
            ════════════════════════════════ -->
            <div class="col-lg-7 bg-white">
              <div class="p-4 p-md-5">

                <!-- Mobile logo -->
                <div class="d-lg-none text-center mb-4">
                  <img src="{{ asset('assets/img/branding/logo.png') }}" alt="Ezitech Logo" width="150">
                </div>

                <!-- Heading -->
                <h4 class="fw-bold mb-1 text-dark">Skill Assessment</h4>
                <p class="text-muted small mb-4">
                  Answer the following questions so we can recommend the most suitable internship program.
                </p>

                <!-- ── Progress Bar ── -->
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-medium">Registration Progress</span>
                    <span class="badge rounded-pill px-3 py-1 small fw-semibold"
                          style="background:#eff6ff;color:#2563eb;">Step 2 / 3</span>
                  </div>

                  <!-- Segmented track -->
                  <div class="d-flex align-items-center gap-2">
                    <div class="flex-fill rounded-pill" style="height:7px;background:#2563eb;"></div>
                    <div class="flex-fill rounded-pill" style="height:7px;background:#2563eb;"></div>
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
                           style="width:22px;height:22px;background:#2563eb;">
                        <i class="bx bx-check text-white" style="font-size:.75rem;"></i>
                      </div>
                      <span class="text-primary fw-semibold" style="font-size:.65rem;">Assessment</span>
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


                <!-- ── Assessment Form ── -->
                <form id="assessmentForm" method="POST"
                      action="{{ route('internship.step3') }}"
                      novalidate>
                  @csrf

                  <div class="d-flex flex-column gap-4">

                    <!-- ── Q1 ── -->
                    <div class="question-block" data-q="q1">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q1</span>
                        How would you describe your current skill level?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q1">
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="skillLevel" value="complete_beginner" required>
                            <span class="small">I am a complete beginner</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="skillLevel" value="basic_concepts">
                            <span class="small">I know basic development concepts</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="skillLevel" value="small_projects">
                            <span class="small">I can build small projects myself</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="skillLevel" value="multiple_projects">
                            <span class="small">I have built multiple projects</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                    <!-- ── Q2 ── -->
                    <div class="question-block" data-q="q2">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q2</span>
                        Have you worked on any real projects before?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q2">
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="projectExperience" value="no" required>
                            <span class="small">No</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="projectExperience" value="academic">
                            <span class="small">Only academic projects</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="projectExperience" value="personal">
                            <span class="small">Personal projects</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="projectExperience" value="freelance">
                            <span class="small">Freelance or client projects</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                    <!-- ── Q3 ── -->
                    <div class="question-block" data-q="q3">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q3</span>
                        How comfortable are you with solving development problems?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q3">
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="problemSolving" value="struggle" required>
                            <span class="small">I struggle with most problems</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="problemSolving" value="simple">
                            <span class="small">I can solve simple problems</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="problemSolving" value="intermediate">
                            <span class="small">I can solve intermediate problems</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="problemSolving" value="confident">
                            <span class="small">I solve problems confidently</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                    <!-- ── Q4 ── -->
                    <div class="question-block" data-q="q4">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q4</span>
                        What type of support do you expect during the internship?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q4">
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="supportExpected" value="full_guidance" required>
                            <span class="small">I need a teacher and full guidance</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="supportExpected" value="some_guidance">
                            <span class="small">I need some guidance but mostly practice</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="supportExpected" value="self_learning">
                            <span class="small">I prefer self-learning with project experience</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="supportExpected" value="work_env">
                            <span class="small">I only need a professional work environment</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                    <!-- ── Q5 ── -->
                    <div class="question-block" data-q="q5">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q5</span>
                        How many hours per week can you dedicate to this internship?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q5">
                        <div class="col-6 col-sm-3">
                          <label class="d-flex flex-column align-items-center justify-content-center border rounded-3 py-3 px-2 w-100 text-center option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input mb-1"
                                   name="hoursPerWeek" value="5hrs" required>
                            <span class="fw-bold small">5 hrs</span>
                            <span class="text-muted" style="font-size:.7rem;">/ week</span>
                          </label>
                        </div>
                        <div class="col-6 col-sm-3">
                          <label class="d-flex flex-column align-items-center justify-content-center border rounded-3 py-3 px-2 w-100 text-center option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input mb-1"
                                   name="hoursPerWeek" value="10hrs">
                            <span class="fw-bold small">10 hrs</span>
                            <span class="text-muted" style="font-size:.7rem;">/ week</span>
                          </label>
                        </div>
                        <div class="col-6 col-sm-3">
                          <label class="d-flex flex-column align-items-center justify-content-center border rounded-3 py-3 px-2 w-100 text-center option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input mb-1"
                                   name="hoursPerWeek" value="15hrs">
                            <span class="fw-bold small">15 hrs</span>
                            <span class="text-muted" style="font-size:.7rem;">/ week</span>
                          </label>
                        </div>
                        <div class="col-6 col-sm-3">
                          <label class="d-flex flex-column align-items-center justify-content-center border rounded-3 py-3 px-2 w-100 text-center option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input mb-1"
                                   name="hoursPerWeek" value="20hrs_plus">
                            <span class="fw-bold small">20+ hrs</span>
                            <span class="text-muted" style="font-size:.7rem;">/ week</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                    <!-- ── Q6 ── -->
                    <div class="question-block" data-q="q6">
                      <p class="fw-semibold small text-dark mb-2">
                        <span class="badge me-2 rounded-2 px-2 py-1"
                              style="background:#eff6ff;color:#2563eb;font-size:.7rem;">Q6</span>
                        What is your main goal for joining this internship?
                        <span class="text-danger ms-1">*</span>
                      </p>
                      <div class="row g-2" id="q6">
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="mainGoal" value="learn_scratch" required>
                            <span class="small">Learn development from scratch</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="mainGoal" value="improve_skills">
                            <span class="small">Improve my development skills</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="mainGoal" value="portfolio">
                            <span class="small">Build projects for my portfolio</span>
                          </label>
                        </div>
                        <div class="col-12 col-sm-6">
                          <label class="d-flex align-items-center gap-2 border rounded-3 px-3 py-2 w-100 option-card"
                                 style="cursor:pointer;">
                            <input type="radio" class="form-check-input flex-shrink-0 mt-0"
                                   name="mainGoal" value="industry_exposure">
                            <span class="small">Gain real industry exposure</span>
                          </label>
                        </div>
                      </div>
                      <div class="text-danger small mt-1 q-error" style="display:none;">
                        <i class="bx bx-error-circle me-1"></i>Please select an option.
                      </div>
                    </div>

                  </div><!-- /questions -->

                  <!-- Submit Button -->
                  <div class="d-grid mt-4">
                    <button type="submit" id="submitBtn"
                            class="btn btn-primary btn-lg fw-semibold rounded-3 py-3"
                            style=";border:none;letter-spacing:.3px;">
                      Next → View Recommendation
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
            <!-- /Right -->

          </div><!-- /row g-0 -->
        </div><!-- /card -->

      </div>
    </div>
  </div>
</div>


<!-- ═══════════════════════════════════════
     ANALYZING LOADER OVERLAY
════════════════════════════════════════ -->
<div id="analyzerOverlay"
     class="position-fixed top-0 start-0 w-100 h-100 d-none align-items-center justify-content-center flex-column gap-3"
     style="z-index:9999;background:rgba(15,23,42,.82);backdrop-filter:blur(4px);">

  <!-- Spinner ring -->
  <div class="position-relative d-flex align-items-center justify-content-center mb-2"
       style="width:72px;height:72px;">
    <div class="spinner-border text-primary" style="width:72px;height:72px;border-width:4px;" role="status"></div>
    <i class="bx bx-brain position-absolute text-white" style="font-size:1.6rem;"></i>
  </div>

  <!-- Animated dots text -->
  <p class="text-white fw-semibold fs-6 mb-0 text-center px-4">
    Analyzing your answers and preparing the best internship path for you
    <span id="loaderDots"></span>
  </p>
  <p class="text-white small mb-0" style="opacity:.55;">This will only take a moment…</p>

</div>


<!-- ═══════════════════════════════════════
     RADIO CARD HIGHLIGHT  +  VALIDATION
════════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {

  /* ── Highlight selected option card ── */
  document.querySelectorAll('.option-card input[type="radio"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      // Remove highlight from siblings in same group
      document.querySelectorAll('input[name="' + radio.name + '"]').forEach(function (r) {
        r.closest('.option-card').classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        r.closest('.option-card').style.borderColor = '';
      });
      // Highlight selected
      radio.closest('.option-card').classList.add('border-primary');
      radio.closest('.option-card').style.borderColor = '#2563eb';
      radio.closest('.option-card').style.background  = '#eff6ff';

      // Clear that question's error message
      const qBlock = radio.closest('.question-block');
      if (qBlock) {
        const err = qBlock.querySelector('.q-error');
        if (err) err.style.display = 'none';
        qBlock.classList.remove('border-danger');
      }
    });
  });


  /* ── Form Submit — Validate then show loader ── */
  const form    = document.getElementById('assessmentForm');
  const overlay = document.getElementById('analyzerOverlay');
  const dotsEl  = document.getElementById('loaderDots');

  // Question name → question block mapping
  const questions = [
    { name: 'skillLevel',      label: 'Q1' },
    { name: 'projectExperience', label: 'Q2' },
    { name: 'problemSolving',  label: 'Q3' },
    { name: 'supportExpected', label: 'Q4' },
    { name: 'hoursPerWeek',    label: 'Q5' },
    { name: 'mainGoal',        label: 'Q6' }
  ];

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    let firstUnanswered = null;
    let allValid = true;

    questions.forEach(function (q) {
      const selected = form.querySelector('input[name="' + q.name + '"]:checked');
      const block    = form.querySelector('[data-q="' + q.label.toLowerCase() + '"]');
      const errMsg   = block ? block.querySelector('.q-error') : null;

      if (!selected) {
        allValid = false;
        if (errMsg) errMsg.style.display = 'block';
        if (!firstUnanswered) firstUnanswered = block;
      } else {
        if (errMsg) errMsg.style.display = 'none';
      }
    });

    if (!allValid) {
      if (firstUnanswered) {
        firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    /* ✅ All answered — show loader overlay */
    overlay.classList.remove('d-none');
    overlay.classList.add('d-flex');

    // Animated dots  ". .. ..."
    let dotCount = 0;
    const dotInterval = setInterval(function () {
      dotCount = (dotCount % 3) + 1;
      dotsEl.textContent = '.'.repeat(dotCount);
    }, 400);

    // After 2 seconds, submit the form
    setTimeout(function () {
      clearInterval(dotInterval);
      form.submit();
    }, 2000);
  });

});
</script>

@endsection