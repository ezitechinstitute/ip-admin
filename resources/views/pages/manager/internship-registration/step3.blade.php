@extends('layouts.blankLayout')

@section('title', 'Ezitech — Your Recommended Internship Path')

@section('content')

<!-- ALL your HTML here (remove second extends completely) -->


<div class="min-vh-100 py-5"
     style="background: linear-gradient(135deg, #e8f0fe 0%, #f1f5ff 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-11">

        <!-- ══════════════════════════════════════
             TOP CARD — Header + Progress + Skill Match
        ══════════════════════════════════════ -->
      <!-- CLEAN HEADER -->
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 mb-4">

  <!-- LEFT: BRAND + VALUE -->
  <div class="d-flex align-items-center gap-3">

    <!-- Logo -->
    <img src="{{ asset('assets/img/branding/logo.png') }}" alt="Ezitech Logo" width="130">

    <!-- Divider -->
    <div style="width:1px;height:38px;background:#e5e7eb;"></div>

    <!-- Text -->
    <div>
      <p class="fw-semibold text-dark mb-0" style="font-size:.95rem;">
        Internship Program Selection
      </p>
      <p class="text-muted mb-0" style="font-size:.75rem;">
        Final step — secure your learning path
      </p>
    </div>

  </div>

  <!-- RIGHT: PROGRESS + TRUST -->
  <div class="d-flex align-items-center gap-3 flex-wrap">

    <!-- Progress Pill -->
    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
         style="background:#eef2ff;">
      <div style="width:8px;height:8px;background:#2563eb;border-radius:50%;"></div>
      <span class="fw-semibold" style="color:#1d4ed8;font-size:.8rem;">
        Step 3 of 3
      </span>
    </div>

    <!-- Trust Indicator -->
    <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-pill"
         style="background:#f0fdf4;">
      <i class="bx bx-check-circle" style="color:#16a34a;"></i>
      <span class="fw-semibold" style="color:#166534;font-size:.8rem;">
        500+ Interns Joined
      </span>
    </div>

  </div>

</div>

<!-- MAIN CARD -->


<div class="min-vh-100 d-flex align-items-center justify-content-center py-5"
     style="background: linear-gradient(135deg, #e8f0fe 0%, #f1f5ff 100%);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-12 col-lg-9">

        {{-- ══════════════════════════════════════
             LIVE BADGE
        ═══════════════════════════════════════ --}}
        <div class="d-flex align-items-center gap-2 mb-3">
          <span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill fw-bold"
                style="background:#e6f1fb;color:#185fa5;font-size:11px;letter-spacing:.07em;text-transform:uppercase;">
            <span class="rounded-circle" id="liveDot"
                  style="width:7px;height:7px;background:#185fa5;display:inline-block;"></span>
            Live Assessment
          </span>
        </div>

        {{-- ══════════════════════════════════════
             HEADLINE
        ═══════════════════════════════════════ --}}
        <h2 class="fw-bold mb-1" style="font-size:clamp(20px,4vw,26px);color:#111827;line-height:1.25;">
          <span id="line1" style="display:inline-block;overflow:hidden;white-space:nowrap;width:0;">
            You're one step away
          </span>
          <br>
          <span id="line2" style="display:inline-block;overflow:hidden;white-space:nowrap;width:0;">
            from being job-ready
          </span>
          <span id="typeCursor"
                style="display:inline-block;width:2px;height:1em;background:#111827;vertical-align:text-bottom;opacity:0;"></span>
        </h2>
        <p class="text-muted mb-4" style="font-size:13px;">
          Based on your assessment, we've identified the most effective path for your growth.
        </p>

        {{-- ══════════════════════════════════════
             TWO BOXES — HORIZONTAL ROW
        ═══════════════════════════════════════ --}}
        <div class="row g-3 mb-3">

          {{-- ── BOX 1: AI Skill Match ── --}}
          <div class="col-md-6">
            <div id="box1"
                 class="h-100 bg-white rounded-4 p-4"
                 style="border:1px solid #e5e7eb;opacity:0;transform:translateY(18px);transition:opacity .55s ease,transform .55s ease;">

              {{-- Header --}}
              <div class="d-flex align-items-center justify-content-between mb-3">

                <div class="d-flex align-items-center gap-2">
                  <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                       style="width:30px;height:30px;background:#e6f1fb;">
                    <i class="bx bx-brain" style="color:#185fa5;font-size:16px;"></i>
                  </div>
                  <span class="fw-semibold text-uppercase"
                        style="font-size:11px;letter-spacing:.05em;color:#6b7280;">
                    AI Skill Match
                  </span>
                </div>

                {{-- Circular Ring --}}
               <div class="position-relative d-flex align-items-center justify-content-center" style="width:56px;height:56px;">

  <svg width="56" height="56" style="transform:rotate(-90deg);">
    
    <!-- Background -->
    <circle cx="28" cy="28" r="22"
            fill="none" stroke="#e5e7eb" stroke-width="5"/>

    <!-- Progress -->
    <circle id="ring"
            cx="28" cy="28" r="22"  <!-- ✅ FIXED (was 22, wrong) -->
            fill="none" stroke="#0d6efd" stroke-width="5"
            stroke-linecap="round"
            stroke-dasharray="138.2"
            stroke-dashoffset="138.2"/>
  </svg>

  <!-- Text -->
  <span id="ringLabel"
        class="position-absolute fw-bold text-primary"
        style="font-size:13px;">
    0%
  </span>

</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

  const score = 82; 

  const circle = document.getElementById("ring");
  const label = document.getElementById("ringLabel");

  const radius = 22;
  const circumference = 2 * Math.PI * radius; // 138.2

  const offset = circumference - (score / 100) * circumference;

  circle.style.strokeDashoffset = offset;
  label.innerText = score + "%";

});
</script>

              </div>

              {{-- Score --}}
              <div class="d-flex align-items-baseline gap-1 mb-1">
                <span id="counter" class="fw-bold" style="font-size:36px;color:#111827;line-height:1;">0</span>
                <span class="fw-medium" style="font-size:14px;color:#9ca3af;">/ 100</span>
              </div>
              <p class="text-muted mb-3" style="font-size:12px;line-height:1.55;">
                You're <strong class="text-dark">2× faster</strong> with structured guidance vs self-learning.
              </p>

              {{-- Skill Tags --}}
              <div class="d-flex flex-wrap gap-2">
                <span class="badge rounded-pill px-2 py-1 fw-medium"
                      style="font-size:11px;background:#eaf3de;color:#3b6d11;border:1px solid #c0dd97;">
                  Problem Solving
                </span>
                <span class="badge rounded-pill px-2 py-1 fw-medium"
                      style="font-size:11px;background:#eaf3de;color:#3b6d11;border:1px solid #c0dd97;">
                  Logic
                </span>
                <span class="badge rounded-pill px-2 py-1 fw-medium"
                      style="font-size:11px;background:#faeeda;color:#854f0b;border:1px solid #fac775;">
                  Frameworks
                </span>
                <span class="badge rounded-pill px-2 py-1 fw-medium"
                      style="font-size:11px;background:#f3f4f6;color:#6b7280;border:1px solid #e5e7eb;">
                  Deployment
                </span>
              </div>

            </div>
          </div>
          {{-- /BOX 1 --}}

          {{-- ── BOX 2: Recommendation ── --}}
          <div class="col-md-6">
            <div id="box2"
                 class="h-100 bg-white rounded-4 p-4"
                 style="border:1px solid #e5e7eb;opacity:0;transform:translateY(18px);transition:opacity .55s ease .12s,transform .55s ease .12s;">

              {{-- Header --}}
              <div class="d-flex align-items-center gap-2 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                     style="width:30px;height:30px;background:#eeedfe;">
                  <i class="bx bx-shield-check" style="color:#534ab7;font-size:16px;"></i>
                </div>
                <span class="fw-semibold text-uppercase"
                      style="font-size:11px;letter-spacing:.05em;color:#6b7280;">
                  Recommendation
                </span>
              </div>

              {{-- Title --}}
              <p class="fw-bold mb-1" style="font-size:17px;color:#111827;">
                Training Internship
              </p>
              <p class="text-muted mb-3" style="font-size:12px;line-height:1.55;">
                Most chosen path for students at your profile level.
              </p>

              {{-- Bullet Points --}}
              <ul class="list-unstyled d-flex flex-column gap-2 mb-0">
                <li class="d-flex align-items-center gap-2" style="font-size:12px;color:#6b7280;">
                  <span class="rounded-circle flex-shrink-0"
                        style="width:6px;height:6px;background:#185fa5;display:inline-block;"></span>
                  Guided mentorship weekly
                </li>
                <li class="d-flex align-items-center gap-2" style="font-size:12px;color:#6b7280;">
                  <span class="rounded-circle flex-shrink-0"
                        style="width:6px;height:6px;background:#185fa5;display:inline-block;"></span>
                  Real-world project exposure
                </li>
                <li class="d-flex align-items-center gap-2" style="font-size:12px;color:#6b7280;">
                  <span class="rounded-circle flex-shrink-0"
                        style="width:6px;height:6px;background:#185fa5;display:inline-block;"></span>
                  Faster job placement rate
                </li>
                <li class="d-flex align-items-center gap-2" style="font-size:12px;color:#6b7280;">
                  <span class="rounded-circle flex-shrink-0"
                        style="width:6px;height:6px;background:#185fa5;display:inline-block;"></span>
                  Certificate on completion
                </li>
              </ul>

            </div>
          </div>
          {{-- /BOX 2 --}}

        </div>
        {{-- /Two boxes row --}}

        {{-- ══════════════════════════════════════
             WHY THIS RECOMMENDATION BAR
        ═══════════════════════════════════════ --}}
        <div id="whyBox"
             class="d-flex align-items-start gap-3 bg-white rounded-4 p-4 mb-3"
             style="border-left:3px solid #185fa5;border-top:1px solid #e5e7eb;border-right:1px solid #e5e7eb;border-bottom:1px solid #e5e7eb;opacity:0;transform:translateY(12px);transition:opacity .5s ease .28s,transform .5s ease .28s;">

          <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
               style="width:34px;height:34px;background:#e6f1fb;">
            <i class="bx bx-info-circle" style="color:#185fa5;font-size:17px;"></i>
          </div>

          <div>
            <p class="fw-semibold mb-1" style="font-size:13px;color:#111827;">
              Why this recommendation?
            </p>
            <p class="text-muted mb-0" style="font-size:12px;line-height:1.6;">
              Students at your level typically struggle without mentorship. This path ensures
              guided learning, real projects, and faster job readiness.
            </p>
          </div>

        </div>

        {{-- ══════════════════════════════════════
             SOCIAL PROOF BAR
        ═══════════════════════════════════════ --}}
        <div id="proofBar"
             class="d-flex align-items-center gap-3 bg-white rounded-4 px-4 py-3"
             style="border:1px solid #e5e7eb;opacity:0;transform:translateY(10px);transition:opacity .45s ease .42s,transform .45s ease .42s;">

          {{-- Stacked Avatars --}}
          <div class="d-flex" style="margin-left:6px;">
            <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                 style="width:24px;height:24px;background:#185fa5;font-size:9px;border:2px solid #fff;margin-left:-6px;">
              AK
            </div>
            <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                 style="width:24px;height:24px;background:#534ab7;font-size:9px;border:2px solid #fff;margin-left:-6px;">
              SR
            </div>
            <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                 style="width:24px;height:24px;background:#0f6e56;font-size:9px;border:2px solid #fff;margin-left:-6px;">
              MZ
            </div>
            <div class="d-flex align-items-center justify-content-center rounded-circle fw-bold text-white flex-shrink-0"
                 style="width:24px;height:24px;background:#993c1d;font-size:9px;border:2px solid #fff;margin-left:-6px;">
              FH
            </div>
          </div>

          <p class="mb-0 text-muted" style="font-size:12px;">
            <span id="trendDot"
                  class="rounded-circle d-inline-block me-1"
                  style="width:7px;height:7px;background:#3b6d11;vertical-align:middle;"></span>
            <strong class="text-dark">247 students</strong> with your profile enrolled this month
          </p>

        </div>
        {{-- /Social proof --}}

      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

  var SCORE        = 82;
  var CIRCUMF      = 138.2;   // 2π × 22
  var ring         = document.getElementById('ring');
  var ringLabel    = document.getElementById('ringLabel');
  var counter      = document.getElementById('counter');
  var animated     = false;

  /* ── Typewriter ─────────────────────────────────────────── */
  function typeEl(el, delay, duration) {
    setTimeout(function () {
      el.style.transition = 'width ' + duration + 'ms steps(28,end)';
      el.style.width = '100%';
    }, delay);
  }

  typeEl(document.getElementById('line1'), 300, 700);
  typeEl(document.getElementById('line2'), 1100, 700);

  /* Blinking cursor then hide */
  var cursor = document.getElementById('typeCursor');
  var blinks = 0;
  setTimeout(function () {
    var t = setInterval(function () {
      cursor.style.opacity = cursor.style.opacity === '1' ? '0' : '1';
      blinks++;
      if (blinks >= 8) { clearInterval(t); cursor.style.opacity = '0'; }
    }, 400);
  }, 1100);

  /* ── Live dot pulse ─────────────────────────────────────── */
  var dot = document.getElementById('liveDot');
  setInterval(function () {
    dot.style.opacity = dot.style.opacity === '0.2' ? '1' : '0.2';
  }, 700);

  /* ── Trend dot pulse ────────────────────────────────────── */
  var tdot = document.getElementById('trendDot');
  setInterval(function () {
    tdot.style.opacity = tdot.style.opacity === '0.2' ? '1' : '0.2';
  }, 900);

  /* ── Ring + counter animation ───────────────────────────── */
  function animateRing() {
    if (animated) return;
    animated = true;

    var offset = CIRCUMF - (SCORE / 100) * CIRCUMF;
    ring.style.strokeDashoffset = offset;

    var start = null;
    function step(ts) {
      if (!start) start = ts;
      var progress = Math.min((ts - start) / 1400, 1);
      var val = Math.round(progress * SCORE);
      ringLabel.textContent = val + '%';
      counter.textContent   = val;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  /* ── Intersection Observer — fade-up + ring trigger ─────── */
  function observe(el, cb, threshold) {
    var obs = new IntersectionObserver(function (entries) {
      if (entries[0].isIntersecting) { cb(); obs.disconnect(); }
    }, { threshold: threshold || 0.2 });
    obs.observe(el);
  }

  var boxes = ['box1', 'box2', 'whyBox', 'proofBar'];
  boxes.forEach(function (id, i) {
    var el = document.getElementById(id);
    observe(el, function () {
      el.style.opacity   = '1';
      el.style.transform = 'translateY(0)';
    });
  });

  /* Trigger ring when box1 enters view */
  observe(document.getElementById('box1'), animateRing);

});
</script>
    <!-- ══════════════════════════════════════
     OUTCOME & TRUST SECTION
══════════════════════════════════════ -->
<div class="card border-0 shadow-sm rounded-4 mb-5" style="border:1.5px solid #e2e8f0 !important;">
    <div class="card-body p-5">

        <div class="row g-5 align-items-start">

            <!-- Internship Outcomes -->
            <div class="col-lg-7">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bx bx-target-lock me-2" style="color:#2563eb;"></i>
                    Internship Outcomes
                </h5>
                <p class="text-muted mb-4">
                    By completing this internship, you will gain skills and experience to launch your tech career:
                </p>

                {{-- <div class="row g-3">
                    @foreach([
                        'Build real-world development projects',
                        'Understand professional development workflows',
                        'Gain hands-on industrial experience',
                        'Create portfolio-ready projects',
                        'Enhance coding and problem-solving skills',
                        'Prepare for junior developer roles'
                    ] as $outcome)
                        <div class="col-12">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bx bx-check-circle flex-shrink-0 mt-1" style="color:#16a34a;font-size:1rem;"></i>
                                <span>{{ $outcome }}</span>
                            </div>
                        </div>
                    @endforeach
                </div> --}}

                <div class="mt-4 rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2"
                     style="background:#fef9c3;border:1px solid #fde68a;">
                    <i class="bx bx-info-circle" style="color:#92400e;"></i>
                    <span class="small fw-medium" style="color:#92400e;">
                        Students who complete this internship typically build <strong>3–5 portfolio projects</strong>.
                    </span>
                </div>
            </div>

            <!-- Trust & Stats -->
            <div class="col-lg-5">
                <div class="d-flex flex-row align-item-center gap-4">

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <div class="fw-bold fs-3 text-primary mb-1">500+</div>
                        <div class="text-muted small">Interns Enrolled</div>
                    </div>

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div class="fw-bold fs-3 text-success mb-1">92%</div>
                        <div class="text-muted small">Completion Rate</div>
                    </div>

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#fff7ed;border:1px solid #fed7aa;">
                        <div class="fw-bold fs-3 text-warning mb-1">4.8 ★</div>
                        <div class="text-muted small">Student Satisfaction</div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


      {{-- ══════════════════════════════════════════════════════
     INTERNSHIP PRICING PLANS — Bootstrap 5 Only
     Animations via Animate.css · Icons via Boxicons
══════════════════════════════════════════════════════ --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Internship Programs — Choose Your Plan</title>

  {{-- Bootstrap 5 --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  {{-- Google Fonts --}}
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet" />

  {{-- Boxicons --}}
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

  {{-- Animate.css --}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" />

  <style>
    body { font-family: 'Sora', sans-serif; background: #f0f4ff; }

    /* Card hover lift */
    .plan-card {
      transition: transform .35s ease, box-shadow .35s ease;
      cursor: pointer;
    }
    .plan-card:hover { transform: translateY(-10px); box-shadow: 0 24px 60px rgba(37,99,235,.18) !important; }
    .plan-card.selected { border-color: #2563eb !important; box-shadow: 0 0 0 4px rgba(37,99,235,.2) !important; }

    /* Popular glow pulse */
    .plan-card.popular { animation: glowPulse 3s ease-in-out infinite; }
    @keyframes glowPulse {
      0%,100% { box-shadow: 0 16px 48px rgba(37,99,235,.22); }
      50%      { box-shadow: 0 20px 64px rgba(37,99,235,.42); }
    }

    /* Feature icon bounce on card hover */
    .plan-card:hover .feature-icon { animation: iconBounce .4s ease; }
    @keyframes iconBounce {
      0%  { transform: scale(1); }
      50% { transform: scale(1.35); }
      100%{ transform: scale(1); }
    }

    /* Price shimmer */
    .price-shimmer {
      background: linear-gradient(90deg,#1e40af,#2563eb,#60a5fa,#2563eb,#1e40af);
      background-size: 300%;
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
      animation: shimmer 4s linear infinite;
    }
    @keyframes shimmer { 0%{background-position:0% center} 100%{background-position:300% center} }

    /* Floating badge */
    .floating-badge { animation: floatBadge 2.5s ease-in-out infinite; }
    @keyframes floatBadge { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-5px)} }

    /* CTA ripple */
    .btn-select { position: relative; overflow: hidden; transition: all .3s ease; }
    .btn-select::after {
      content:''; position:absolute; inset:0;
      background:rgba(255,255,255,.15);
      transform:scaleX(0); transform-origin:left; transition:transform .35s ease;
    }
    .btn-select:hover::after { transform:scaleX(1); }

    /* Top accent bar */
    .accent-bar { height: 5px; border-radius: 22px 22px 0 0; }
  </style>
</head>
<body>

<section style="background:linear-gradient(135deg,#eff6ff 0%,#f0f9ff 50%,#f5f3ff 100%);" class="py-5">
<div class="container py-4">

  {{-- ── Header ── --}}
  <div class="text-center mb-5">

    <span class="badge rounded-pill px-4 py-2 mb-3 d-inline-flex align-items-center gap-2 animate__animated animate__fadeInDown"
          style="background:rgba(37,99,235,.1);color:#2563eb;font-size:.7rem;letter-spacing:.8px;text-transform:uppercase;font-weight:700;border:1px solid rgba(37,99,235,.2);">
      <i class="bx bx-rocket fs-6"></i> Internship Programs
    </span>

    <h1 class="display-5 fw-bold text-dark mb-3 animate__animated animate__fadeInUp">
      Launch Your Career With The
      <span class="text-primary">Right Plan</span>
    </h1>

    <p class="fs-5 text-secondary mb-4 mx-auto animate__animated animate__fadeInUp animate__delay-1s" style="max-width:560px;line-height:1.75;">
      Choose a program tailored to your skill level. Build real projects, earn certificates, and open doors to your first tech job.
    </p>

    {{-- Stats --}}
    <div class="d-flex flex-wrap gap-3 justify-content-center mb-2 animate__animated animate__fadeInUp animate__delay-1s">
      <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-pill bg-white shadow-sm border">
        <i class="bx bx-group fs-5 text-primary"></i>
        <span class="fw-semibold text-dark small">500+ Students Enrolled</span>
      </div>
      <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-pill bg-white shadow-sm border">
        <i class="bx bx-star fs-5 text-warning"></i>
        <span class="fw-semibold text-dark small">4.9 / 5 Rating</span>
      </div>
      <div class="d-flex align-items-center gap-2 px-4 py-2 rounded-pill bg-white shadow-sm border">
        <i class="bx bx-trophy fs-5 text-success"></i>
        <span class="fw-semibold text-dark small">90% Job Placement</span>
      </div>
    </div>

  </div>

  {{-- ── Form ── --}}
  <form method="POST" action="">
    @csrf
    <input type="hidden" name="selectedPlan" id="selectedPlan" value="training" />

    <div class="row g-4 align-items-stretch justify-content-center">

      {{-- ══ PLAN 1 — Project Practice ══ --}}
      <div class="col-xl-4 col-md-6 animate__animated animate__fadeInLeft">
        <div class="card h-100 border-2 rounded-4 overflow-hidden plan-card shadow-sm bg-white"
             data-plan="practice" style="border-color:#e2e8f0 !important;">

          <div class="accent-bar" style="background:linear-gradient(90deg,#64748b,#94a3b8);"></div>

          <div class="card-body d-flex flex-column p-4">

            <div class="mb-3">
              <span class="badge rounded-pill px-3 py-2 fw-semibold"
                    style="background:#f1f5f9;color:#475569;font-size:.7rem;border:1px solid #e2e8f0;">
                <i class="bx bx-code-alt me-1"></i> Self-Paced Option
              </span>
            </div>

            <h4 class="fw-bold text-dark mb-1">Project Practice Internship</h4>
            <p class="text-secondary small mb-4" style="line-height:1.65;">
              Best for students who already understand basic development concepts and want real project experience.
            </p>

            {{-- Price Box --}}
            <div class="rounded-3 p-3 mb-4" style="background:#f8fafc;border:1.5px dashed #cbd5e1;">
              <span class="price-shimmer fw-bold d-block" style="font-size:2.5rem;line-height:1;">PKR 3,000</span>
              <div class="d-flex align-items-center gap-2 mt-2">
                <span class="badge rounded-2 px-2 py-1" style="background:#f0fdf4;color:#16a34a;font-size:.7rem;">
                  <i class="bx bx-trending-down me-1"></i> Best Value
                </span>
                <span class="text-secondary small">· One-time payment</span>
              </div>
            </div>

            {{-- Features --}}
            <ul class="list-unstyled d-flex flex-column gap-2 mb-4 flex-grow-1">
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Dedicated Mentor Support</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Step-by-Step Training Guidance</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Industrial Project Development
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Weekly Progress Reviews
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Portfolio Project Development
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Internship Completion Certificate
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Job Opportunity for Top Performers
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Offer Letter for University
              </li>
            </ul>

            {{-- Achieve Box --}}
            <div class="rounded-3 p-3 mb-4 bg-light border">
              <p class="small fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                <i class="bx bx-trophy" style="color:#f59e0b;"></i> What You Can Achieve
              </p>
              <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Hands-on experience on real projects
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Strengthen practical development skills
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Improve debugging &amp; problem-solving
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Prepare for freelance or entry-level roles
                </li>
              </ul>
            </div>

            <button type="button"
                    class="btn btn-outline-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn"
                    data-plan="practice">
              <i class="bx bx-right-arrow-alt me-1"></i> Select this Plan
            </button>

          </div>
        </div>
      </div>
      {{-- /Plan 1 --}}

      {{-- ══ PLAN 2 — Training (Popular) ══ --}}
      <div class="col-xl-4 col-md-6 animate__animated animate__fadeInUp" style="z-index:2;">
        <div class="card h-100 border-2 rounded-4 overflow-hidden plan-card popular shadow-lg bg-white"
             data-plan="training"
             style="border-color:#2563eb !important; transform:scale(1.04);">

          <div class="accent-bar" style="background:linear-gradient(90deg,#1d4ed8,#2563eb,#60a5fa);"></div>

          <div class="card-body d-flex flex-column p-4">

            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
              <span class="badge rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-1"
                    style="background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;font-size:.72rem;">
                <i class="bx bx-star"></i> Recommended
              </span>
              <span class="badge rounded-pill px-3 py-2 fw-bold floating-badge"
                    style="background:linear-gradient(135deg,#f59e0b,#fbbf24);color:#fff;font-size:.7rem;">
                🔥 Most Popular
              </span>
            </div>

            <h4 class="fw-bold text-dark mb-1">Training Internship</h4>
            <p class="text-secondary small mb-4" style="line-height:1.65;">
              Best for students who need proper guidance, mentorship, and a structured learning path.
            </p>

            {{-- Price Box --}}
            <div class="rounded-3 p-3 mb-4" style="background:#eff6ff;border:1.5px dashed #93c5fd;">
              <span class="price-shimmer fw-bold d-block" style="font-size:2.5rem;line-height:1;">PKR 6,000</span>
              <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                <span class="badge rounded-2 px-2 py-1" style="background:#eff6ff;color:#2563eb;font-size:.7rem;">
                  <i class="bx bx-calendar me-1"></i> 3 Months Duration
                </span>
                <span class="text-secondary small">· Full mentorship included</span>
              </div>
            </div>

            {{-- Features --}}
            <ul class="list-unstyled d-flex flex-column gap-2 mb-4 flex-grow-1">
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Dedicated Mentor Support
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Step-by-Step Training Guidance
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Industrial Project Development
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Weekly Progress Reviews
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Portfolio Project Development
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Internship Completion Certificate
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Job Opportunity for Top Performers
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Offer Letter for University
              </li>
            </ul>

            {{-- Achieve Box --}}
            <div class="rounded-3 p-3 mb-4" style="background:#f0f9ff;border:1px solid #bae6fd;">
              <p class="small fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                <i class="bx bx-trophy" style="color:#f59e0b;"></i> What You Can Achieve
              </p>
              <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Build real-world development projects
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Understand professional development workflows
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Create portfolio-ready projects
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Prepare for junior developer roles
                </li>
              </ul>
            </div>

            <button type="button"
                    class="btn btn-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn"
                    data-plan="training"
                    style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border:none;">
              <i class="bx bx-check me-1"></i> Continue with this Program
            </button>

            <p class="text-center text-secondary mt-2 mb-0" style="font-size:.72rem;">
              <i class="bx bx-lock-alt me-1"></i> Secure enrollment · No hidden fees
            </p>

          </div>
        </div>
      </div>
      {{-- /Plan 2 --}}

      {{-- ══ PLAN 3 — Industrial ══ --}}
      <div class="col-xl-4 col-md-6 animate__animated animate__fadeInRight">
        <div class="card h-100 border-2 rounded-4 overflow-hidden plan-card shadow-sm bg-white"
             data-plan="industrial" style="border-color:#e2e8f0 !important;">

          <div class="accent-bar" style="background:linear-gradient(90deg,#0891b2,#06b6d4);"></div>

          <div class="card-body d-flex flex-column p-4">

            <div class="mb-3">
              <span class="badge rounded-pill px-3 py-2 fw-semibold"
                    style="background:#ecfeff;color:#0891b2;font-size:.7rem;border:1px solid #a5f3fc;">
                <i class="bx bx-buildings me-1"></i> For Experienced Students
              </span>
            </div>

            <h4 class="fw-bold text-dark mb-1">Industrial Environment Internship</h4>
            <p class="text-secondary small mb-4" style="line-height:1.65;">
              Best for students who already have development experience and want real industrial exposure.
            </p>

            {{-- Price Box --}}
            <div class="rounded-3 p-3 mb-4" style="background:#f8fafc;border:1.5px dashed #cbd5e1;">
              <span class="price-shimmer fw-bold d-block" style="font-size:2.5rem;line-height:1;">PKR 500</span>
              <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                <span class="badge rounded-2 px-2 py-1" style="background:#fff7ed;color:#c2410c;font-size:.7rem;">
                  <i class="bx bx-time me-1"></i> 4 Weeks · Platform Fee
                </span>
              </div>
            </div>

            {{-- Features --}}
            <ul class="list-unstyled d-flex flex-column gap-2 mb-4 flex-grow-1">
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Dedicated Mentor Support</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Step-by-Step Training Guidance</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Industrial Project Access
              </li>
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Weekly Progress Reviews</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Real Development Environment
              </li>
              <li class="d-flex align-items-center gap-2 small text-dark fw-medium">
                <i class="bx bx-check-circle fs-5 feature-icon flex-shrink-0" style="color:#16a34a;"></i>
                Experience Letter
              </li>
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Job Opportunity for Top Performers</span>
              </li>
              <li class="d-flex align-items-center gap-2 small text-secondary">
                <i class="bx bx-x-circle fs-5 feature-icon flex-shrink-0" style="color:#dc2626;"></i>
                <span style="text-decoration:line-through;">Offer Letter for University</span>
              </li>
            </ul>

            {{-- Achieve Box --}}
            <div class="rounded-3 p-3 mb-4 bg-light border">
              <p class="small fw-bold text-dark mb-2 d-flex align-items-center gap-1">
                <i class="bx bx-trophy" style="color:#f59e0b;"></i> What You Can Achieve
              </p>
              <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Experience a real development environment
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Understand team collaboration workflow
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Gain exposure to real project structures
                </li>
                <li class="small text-secondary d-flex align-items-start gap-1">
                  <i class="bx bx-check text-success flex-shrink-0 mt-1"></i> Build confidence with production-level code
                </li>
              </ul>
            </div>

            <button type="button"
                    class="btn btn-outline-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn"
                    data-plan="industrial">
              <i class="bx bx-right-arrow-alt me-1"></i> Select this Plan
            </button>

          </div>
        </div>
      </div>
      {{-- /Plan 3 --}}

    </div>
    {{-- /Cards Row --}}

    {{-- ── Guarantee Strip ── --}}
    <div class="row justify-content-center mt-5">
      <div class="col-lg-10">
        <div class="card border-0 rounded-4 shadow"
             style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8);">
          <div class="card-body py-4 px-4">
            <div class="row g-3 align-items-center text-center text-md-start justify-content-center">
              <div class="col-md-auto">
                <div class="d-flex align-items-center gap-3 justify-content-center justify-content-md-start">
                  <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                       style="width:52px;height:52px;background:rgba(255,255,255,.15);">
                    <i class="bx bx-shield-check text-white fs-3"></i>
                  </div>
                  <div>
                    <p class="fw-bold text-white mb-0 fs-6">Satisfaction Guaranteed</p>
                    <p class="mb-0 small" style="color:rgba(255,255,255,.6);">Not satisfied in 7 days? Full refund — no questions asked.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-auto ms-md-auto">
                <div class="d-flex gap-3 align-items-center justify-content-center flex-wrap">
                  <span class="d-flex align-items-center gap-1 small" style="color:rgba(255,255,255,.65);">
                    <i class="bx bx-credit-card"></i> Secure Payment
                  </span>
                  <span class="d-flex align-items-center gap-1 small" style="color:rgba(255,255,255,.65);">
                    <i class="bx bx-support"></i> 24/7 Support
                  </span>
                  <span class="d-flex align-items-center gap-1 small" style="color:rgba(255,255,255,.65);">
                    <i class="bx bx-check-shield"></i> Verified Certificates
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Submit Button ── --}}
    <div class="text-center mt-4">
      <button type="submit" id="submitBtn"
              class="btn btn-primary btn-lg px-5 py-3 rounded-3 fw-bold shadow btn-select"
              style="background:linear-gradient(135deg,#1d4ed8,#2563eb);border:none;font-size:1.05rem;">
        <i class="bx bx-rocket me-2"></i> Enroll Now — Start Your Journey
      </button>
    </div>

  </form>

  {{-- ── FAQ Teaser ── --}}
  <div class="text-center mt-4">
    <p class="text-secondary small mb-0">
      Have questions?
      <a href="#faq" class="text-primary fw-semibold text-decoration-none">
        Read our FAQs <i class="bx bx-right-arrow-alt"></i>
      </a>
      or
      <a href="#contact" class="text-primary fw-semibold text-decoration-none">contact us directly</a>
    </p>
  </div>

</div>
</section>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const cards     = document.querySelectorAll('.plan-card');
  const hidden    = document.getElementById('selectedPlan');

  // Default: training selected
  document.querySelector('[data-plan="training"]').classList.add('selected');

  cards.forEach(card => {
    card.addEventListener('click', () => {
      const plan = card.dataset.plan;

      // Update selection
      cards.forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      hidden.value = plan;

      // Update all CTA buttons
      document.querySelectorAll('.select-plan-btn').forEach(btn => {
        const isChosen = btn.dataset.plan === plan;

        if (btn.dataset.plan === 'training') {
          btn.className = isChosen
            ? 'btn btn-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn'
            : 'btn btn-outline-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn';
          if (isChosen) {
            btn.style.background = 'linear-gradient(135deg,#1d4ed8,#2563eb)';
            btn.style.border = 'none';
          } else {
            btn.style.background = '';
            btn.style.border = '';
          }
          btn.innerHTML = isChosen
            ? '<i class="bx bx-check me-1"></i> Continue with this Program'
            : '<i class="bx bx-right-arrow-alt me-1"></i> Select this Plan';
        } else {
          btn.className = isChosen
            ? 'btn btn-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn'
            : 'btn btn-outline-primary w-100 fw-semibold rounded-3 py-2 btn-select select-plan-btn';
          btn.innerHTML = isChosen
            ? '<i class="bx bx-check me-1"></i> Selected ✓'
            : '<i class="bx bx-right-arrow-alt me-1"></i> Select this Plan';
        }
      });
    });
  });
</script>



        {{-- <!-- ══════════════════════════════════════
     OUTCOME & TRUST SECTION
══════════════════════════════════════ -->
<div class="card border-0 shadow-sm rounded-4 mb-5" style="border:1.5px solid #e2e8f0 !important;">
    <div class="card-body p-5">

        <div class="row g-5 align-items-start">

            <!-- Internship Outcomes -->
            <div class="col-lg-7">
                <h5 class="fw-bold text-dark mb-3">
                    <i class="bx bx-target-lock me-2" style="color:#2563eb;"></i>
                    Internship Outcomes
                </h5>
                <p class="text-muted mb-4">
                    By completing this internship, you will gain skills and experience to launch your tech career:
                </p>

                <div class="row g-3">
                    @foreach([
                        'Build real-world development projects',
                        'Understand professional development workflows',
                        'Gain hands-on industrial experience',
                        'Create portfolio-ready projects',
                        'Enhance coding and problem-solving skills',
                        'Prepare for junior developer roles'
                    ] as $outcome)
                        <div class="col-12">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bx bx-check-circle flex-shrink-0 mt-1" style="color:#16a34a;font-size:1rem;"></i>
                                <span>{{ $outcome }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-3 px-3 py-2 d-inline-flex align-items-center gap-2"
                     style="background:#fef9c3;border:1px solid #fde68a;">
                    <i class="bx bx-info-circle" style="color:#92400e;"></i>
                    <span class="small fw-medium" style="color:#92400e;">
                        Students who complete this internship typically build <strong>3–5 portfolio projects</strong>.
                    </span>
                </div>
            </div>

            <!-- Trust & Stats -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-4">

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <div class="fw-bold fs-3 text-primary mb-1">500+</div>
                        <div class="text-muted small">Interns Enrolled</div>
                    </div>

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div class="fw-bold fs-3 text-success mb-1">92%</div>
                        <div class="text-muted small">Completion Rate</div>
                    </div>

                    <div class="rounded-4 p-4 text-center shadow-sm" style="background:#fff7ed;border:1px solid #fed7aa;">
                        <div class="fw-bold fs-3 text-warning mb-1">4.8 ★</div>
                        <div class="text-muted small">Student Satisfaction</div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div> --}}

          <!-- Final CTA -->
          <div class="card border-0 shadow-sm rounded-4 mb-2" style="border:1.5px solid #e2e8f0 !important;">
            <div class="card-body p-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
              <div>
                <p class="fw-semibold text-dark mb-1">
                  <i class="bx bx-shield-check me-2 text-primary"></i>
                  Ready to begin your internship journey?
                </p>
                <p class="text-muted small mb-0">
                  <i class="bx bx-lock-alt me-1"></i>
                  Your selected plan will be confirmed after registration. You can change it before payment.
                </p>
              </div>
              <button type="submit"
                      class="btn btn-primary btn-lg fw-semibold rounded-3 px-5 py-3 flex-shrink-0"
                      style="background:linear-gradient(90deg,#1d4ed8,#2563eb);border:none;white-space:nowrap;">
                Continue Registration
                <i class="bx bx-right-arrow-alt ms-2 fs-5 align-middle"></i>
              </button>
            </div>
          </div>

        </form>
        <!-- /Form -->

      </div>
    </div>
  </div>
</div>


<!-- ══════════════════════════════════════
     PLAN SELECTION LOGIC
══════════════════════════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function () {

  const selectedPlanInput = document.getElementById('selectedPlan');
  const planCards         = document.querySelectorAll('.plan-card');
  const selectBtns        = document.querySelectorAll('.select-plan-btn');

  selectBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const chosen = btn.getAttribute('data-plan');
      selectedPlanInput.value = chosen;

      // ── Reset all cards ──
      planCards.forEach(function (card) {
        card.style.border    = '1.5px solid #e2e8f0';
        card.style.transform = '';
        card.style.boxShadow = '0 2px 12px rgba(0,0,0,.06)';
        card.querySelector('.plan-accent-bar').style.background = '#e2e8f0';
      });

      // ── Reset all buttons ──
      selectBtns.forEach(function (b) {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-primary');
        b.style.background = '';
        b.style.border     = '';
        b.innerHTML        = 'Select this Plan';
      });

      // ── Highlight chosen card ──
      const chosenCard = document.querySelector('.plan-card[data-plan="' + chosen + '"]');
      if (chosenCard) {
        chosenCard.style.border    = '2px solid #2563eb';
        chosenCard.style.transform = 'scale(1.03)';
        chosenCard.style.boxShadow = '0 12px 40px rgba(37,99,235,.18)';
        chosenCard.querySelector('.plan-accent-bar').style.background =
          'linear-gradient(90deg,#1d4ed8,#2563eb)';
      }

      // ── Highlight chosen button ──
      btn.classList.remove('btn-outline-primary');
      btn.classList.add('btn-primary');
      btn.style.background = 'linear-gradient(90deg,#1d4ed8,#2563eb)';
      btn.style.border     = 'none';
      btn.innerHTML        = '<i class="bx bx-check me-1"></i> Continue with this Program';
    });
  });

});
</script>

@endsection