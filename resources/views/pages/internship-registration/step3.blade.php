@extends('layouts.blankLayout')

@section('content')
<style>
.feat-row { display:flex; gap:6px; align-items:flex-start; margin-bottom:6px; font-size:13px; }
.feat-yes { color: #198754; font-size:14px; flex-shrink:0; margin-top:1px; }
.feat-no  { color: #dc3545; font-size:14px; flex-shrink:0; margin-top:1px; }
.feat-txt.dim { color:#6c757d; }
.pulse-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; flex-shrink:0; animation:pulse 2s infinite; display:inline-block; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
</style>

<div class="container py-5" >

  {{-- Step indicator --}}
  <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10" style="width:28px;height:28px;font-size:12px;">✓</div>
      <small class="text-success">Assessment</small>
    </div>
    <div class="border-top" style="width:40px;"></div>
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10" style="width:28px;height:28px;font-size:12px;">✓</div>
      <small class="text-success">Skill check</small>
    </div>
    <div class="border-top" style="width:40px;"></div>
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 fw-semibold" style="width:28px;height:28px;font-size:12px;">3</div>
      <small class="text-primary fw-semibold">Your path</small>
    </div>
  </div>

  {{-- Header --}}
  <div class="text-center mb-4">
    <span class="badge bg-success bg-opacity-10 text-success mb-2 fs-6 px-3 py-2">✓ Assessment complete</span>
    <h3 class="fw-semibold">Your recommended internship path</h3>
    <p class="text-muted">Based on your answers, we've matched you with the most suitable program.</p>
  </div>

  {{-- Social proof --}}
  <div class="row g-3 mb-4">
    <div class="col-4">
      <div class="bg-light rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-primary">500+</div>
        <small class="text-muted">Active interns</small>
      </div>
    </div>
    <div class="col-4">
      <div class="bg-light rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-success">120+</div>
        <small class="text-muted">Successfully placed</small>
      </div>
    </div>
    <div class="col-4">
      <div class="bg-light rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-warning">4.8/5</div>
        <small class="text-muted">Average rating</small>
      </div>
    </div>
  </div>

  {{-- Live activity ticker --}}
  <div class="bg-light rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <span class="pulse-dot"></span>
      <span class="fw-semibold small">Active intern developers right now</span>
    </div>
    <div class="d-flex align-items-center" id="avatars-row"></div>
    <small class="text-muted" id="live-count">Loading...</small>
  </div>

  {{-- Skill match --}}
  <div class="card border rounded-3 p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
      <div>
        <div class="fw-semibold small">Your skill match</div>
        <div class="text-muted" style="font-size:12px;">Recommended path: Training Internship</div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="fs-4 fw-semibold text-primary" id="pct-label">0%</span>
        <span class="badge bg-primary bg-opacity-10 text-primary">Strong match</span>
      </div>
    </div>
    <div class="progress" style="height:10px;">
      <div id="skill-bar" class="progress-bar bg-primary" role="progressbar" style="width:0%;transition:width 1.6s cubic-bezier(0.4,0,0.2,1);" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    <div class="d-flex justify-content-between mt-1">
      <small class="text-muted">Beginner</small>
      <small class="text-muted">Advanced</small>
    </div>
  </div>

  {{-- PAYMENT PLANS SECTION --}}
<div class="border-top pt-4 mb-3">
  <h6 class="fw-semibold text-center">Choose your internship plan</h6>
  <p class="text-muted small text-center">
    All plans are structured with equal access layout for better comparison.
  </p>
</div>

<div class="row g-3 align-items-stretch">

 
  {{-- PLAN 2 --}}
<div class="col-md-4 d-flex">
  <div class="card border rounded-3 p-3 w-100 d-flex flex-column h-100">

    <div class="text-center mb-2">
      <span class="badge bg-secondary bg-opacity-10 text-secondary">Standard</span>
    </div>

    <h6 class="fw-semibold text-center">Project Practice Internship</h6>

    <p class="text-muted small text-center mb-2">
      Best for students who already understand basic development concepts
    </p>

    <div class="text-center mb-3">
      <div class="text-decoration-line-through text-muted small">PKR 4,000</div>
      <div class="fs-4 fw-semibold">PKR 3,000</div>
      <small class="text-success">25% OFF</small>
    </div>

    <div class="flex-grow-1">

      <h3 class="text-uppercase text-muted mb-2" style="font-size:11px;">
        What You Will Get & Achieve
      </h3>

      <div class="feat-row"><span class="feat-yes">✔</span><span>Industrial Project Development (Hands-on Experience)</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Weekly Progress Reviews</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Portfolio Project Development</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Internship Completion Certificate</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Job Opportunity for Top Performers</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Offer Letter for University (if required)</span></div>

      <div class="feat-row"><span class="feat-yes">✔</span><span>Hands-on Real Project Experience</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Strong Portfolio Development</span></div>
    

    </div>

    <a href="#" class="btn btn-outline-primary w-100 mt-3">
      Choose Plan
    </a>

  </div>
</div>
 {{-- PLAN 1 (RECOMMENDED) --}}
  <div class="col-md-4 d-flex">
    <div class="card border border-primary rounded-3 p-3 w-100 d-flex flex-column h-100">

      <div class="text-center mb-2">
        <span class="badge bg-primary bg-opacity-10 text-primary">Recommended</span>
      </div>

      <h6 class="fw-semibold text-center">Training Internship</h6>

      <p class="text-muted small text-center mb-2">
        Best for guided learning with structured mentorship
      </p>

      <div class="text-center mb-3">
        <div class="text-decoration-line-through text-muted small">PKR 10,000</div>
        <div class="fs-4 fw-semibold text-primary">PKR 6,000</div>
        <small class="text-success">40% OFF</small>
      </div>

      <div class="flex-grow-1">
        <h3 class="text-uppercase text-muted mb-2" style="font-size:11px;">
          What You Will Get
        </h3>

        <div class="feat-row"><span class="feat-yes">✔</span><span>Dedicated Mentor Support</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Step-by-Step Training Guidance</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Industrial Project Development</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Weekly Progress Reviews</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Portfolio Development</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Certificate</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>Job Opportunity</span></div>
        <div class="feat-row"><span class="feat-yes">✔</span><span>University Offer Letter</span></div>
          <div class="feat-row"><span class="feat-yes">✔</span><span>Freelance / Entry-Level Readiness</span></div>
      </div>

      <form method="POST" action="{{ route('intern.register.complete') }}">
    @csrf
    <input type="hidden" name="selected_plan" value="training">

    <button type="submit" class="btn btn-primary w-100">
        Continue with this Program
    </button>
</form>

    </div>
  </div>

  {{-- PLAN 3 --}}
 <div class="col-md-4 d-flex">
  <div class="card border rounded-3 p-3 w-100 d-flex flex-column h-100">

    <div class="text-center mb-2">
      <span class="badge bg-dark bg-opacity-10 text-dark">Basic</span>
    </div>

    <h6 class="fw-semibold text-center">Industrial Access Internship</h6>

    <p class="text-muted small text-center mb-2">
      Real-world development environment exposure
    </p>

    <div class="text-center mb-3">
      <div class="text-decoration-line-through text-muted small">PKR 1,000</div>
      <div class="fs-4 fw-semibold">PKR 500</div>
      <small class="text-success">50% OFF</small>
    </div>

    <div class="flex-grow-1">

      <h3 class="text-uppercase text-muted mb-2" style="font-size:11px;">
        What You Will Get & Achieve
      </h3>

      <div class="feat-row"><span class="feat-no">✖</span><span class="feat-txt dim">Dedicated Mentor Support</span></div>
      <div class="feat-row"><span class="feat-no">✖</span><span class="feat-txt dim">Step-by-Step Training Guidance</span></div>

      <div class="feat-row"><span class="feat-yes">✔</span><span>Industrial Project Access</span></div>
      <div class="feat-row"><span class="feat-no">✖</span><span class="feat-txt dim">Weekly Progress Reviews</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Real Development Environment Exposure</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Experience Letter</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Team Collaboration Workflow</span></div>
      <div class="feat-row"><span class="feat-yes">✔</span><span>Production-Level Code Exposure</span></div>

    </div>

    <a href="#" class="btn btn-outline-primary w-100 mt-3">
      Choose Plan
    </a>

  </div>
</div>

</div>
      

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  var colors = ['#cfe2ff','#d1e7dd','#fff3cd','#e0d7ff','#d1f2eb','#fde8e0'];
  var textColors = ['#084298','#0a3622','#664d03','#3d2b8e','#0d4f3c','#6a2010'];
  var names = [
    ['A','Ahmed'],['S','Sara'],['U','Usman'],['F','Fatima'],['A','Ali'],
    ['Z','Zara'],['H','Hassan'],['M','Misha'],['O','Omar'],['N','Nadia'],
    ['R','Rafay'],['L','Laiba']
  ];

  var row = document.getElementById('avatars-row');
  var countEl = document.getElementById('live-count');
  var active = Math.floor(Math.random()*8)+14;
  countEl.textContent = active + ' developers active';

  names.slice(0,7).forEach(function(n, i) {
    var av = document.createElement('div');
    av.title = n[1];
    av.style.cssText = 'width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;margin-left:-6px;border:2px solid #fff;flex-shrink:0;';
    av.style.background = colors[i % colors.length];
    av.style.color = textColors[i % textColors.length];
    av.textContent = n[0];
    row.appendChild(av);
  });

  var more = document.createElement('div');
  more.style.cssText = 'width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;margin-left:-6px;border:2px solid #fff;background:#e9ecef;color:#6c757d;flex-shrink:0;';
  more.textContent = '+' + (active - 7);
  row.appendChild(more);

  var bar = document.getElementById('skill-bar');
  var label = document.getElementById('pct-label');
  var target = 82, duration = 1600, startTime = null;
  function ease(t){ return t<0.5?2*t*t:-1+(4-2*t)*t; }
  function animate(ts){
    if(!startTime) startTime = ts;
    var p = Math.min((ts - startTime) / duration, 1);
    var cur = Math.round(ease(p) * target);
    bar.style.width = cur + '%';
    bar.setAttribute('aria-valuenow', cur);
    label.textContent = cur + '%';
    if(p < 1) requestAnimationFrame(animate);
  }
  setTimeout(function(){ requestAnimationFrame(animate); }, 300);
});
</script>
@endpush