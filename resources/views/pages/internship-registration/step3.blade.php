@extends('layouts.blankLayout')

@section('content')
<style>
.feat-row { display:flex; gap:6px; align-items:flex-start; margin-bottom:6px; font-size:13px; }
.feat-yes { color: #198754; font-size:14px; flex-shrink:0; margin-top:1px; }
.feat-no  { color: #dc3545; font-size:14px; flex-shrink:0; margin-top:1px; }
.feat-txt.dim { color:#6c757d; }
.pulse-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; flex-shrink:0; animation:pulse 2s infinite; display:inline-block; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

/* Recommended plan styling */
.plan-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
.plan-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
.plan-card.recommended { 
  transform: scale(1.05);
  box-shadow: 0 12px 32px rgba(13, 110, 253, 0.25);
}
.plan-card.recommended .card {
  border-width: 3px !important;
}
</style>

<div class="container py-5">

  {{-- Step indicator --}}
  <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10" style="width:28px;height:28px;font-size:12px;">✓</div>
      <small class="text-success">Step 1</small>
    </div>
    <div class="border-top" style="width:40px;"></div>
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10" style="width:28px;height:28px;font-size:12px;">✓</div>
      <small class="text-success">Step 2</small>
    </div>
    <div class="border-top" style="width:40px;"></div>
    <div class="d-flex align-items-center gap-2">
      <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 fw-semibold" style="width:28px;height:28px;font-size:12px;">3</div>
      <small class="text-primary fw-semibold">Step 3: Choose Plan</small>
    </div>
  </div>

  {{-- Header --}}
  <div class="text-center mb-4">
    <span class="badge bg-success bg-opacity-10 text-success mb-2 fs-6 px-3 py-2">✓ Assessment complete</span>
    <h3 class="fw-bold">Your Recommended Internship Path</h3>
    <p class="text-muted">Based on your answers, we've matched you with the most suitable program.</p>
  </div>

  {{-- Skill match section --}}
  <div class="card border rounded-3 p-4 mb-5 bg-light">
    <div class="row align-items-center">
      <div class="col-md-8">
        <div class="fw-semibold mb-2">Your Skill Assessment Results</div>
        <p class="text-muted mb-0" style="font-size:13px;">
          Based on your answers, here's how well you match with the recommended program. Students with similar skill levels usually choose this program.
        </p>
      </div>
      <div class="col-md-4 text-md-end text-center">
        <div class="d-flex align-items-center justify-content-md-end justify-content-center gap-2 mt-3 mt-md-0">
          <div>
            <div class="fw-bold text-primary" style="font-size: 28px;" id="pct-label">{{ $skill_match_percentage ?? 0 }}%</div>
            <div class="text-muted small">Skill Match</div>
          </div>
          <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">Strong Match</span>
          </div>
        </div>
      </div>
    </div>
    <div class="progress mt-3" style="height:12px;">
      <div id="skill-bar" class="progress-bar bg-primary" role="progressbar" style="width: {{ $skill_match_percentage ?? 0 }}%;transition:width 1.6s cubic-bezier(0.4,0,0.2,1);" aria-valuenow="{{ $skill_match_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
  </div>

  {{-- Social proof --}}
  <div class="row g-3 mb-5">
    <div class="col-md-4">
      <div class="bg-primary bg-opacity-10 rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-primary">500+</div>
        <small class="text-muted">Active Interns</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-success bg-opacity-10 rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-success">120+</div>
        <small class="text-muted">Successfully Placed</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="bg-warning bg-opacity-10 rounded-3 p-3 text-center">
        <div class="fs-4 fw-semibold text-warning">4.8/5</div>
        <small class="text-muted">Average Rating</small>
      </div>
    </div>
  </div>

  {{-- Live activity ticker --}}
  <div class="bg-light rounded-3 p-3 mb-5 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <span class="pulse-dot"></span>
      <span class="fw-semibold small">Active intern developers right now</span>
    </div>
    <div class="d-flex align-items-center" id="avatars-row"></div>
    <small class="text-muted" id="live-count">Loading...</small>
  </div>

  {{-- PAYMENT PLANS SECTION --}}
  <div class="mb-4">
    <h5 class="fw-bold text-center mb-2">Choose Your Internship Plan</h5>
    <p class="text-muted small text-center">Select the plan that best matches your goals and learning preferences</p>
  </div>

  <div class="row g-4 align-items-stretch mb-5">

    {{-- DYNAMICALLY RENDER PLANS BASED ON RECOMMENDATION --}}
    @php
      $plans = [
        'practice' => [
          'title' => 'Project Practice Internship',
          'badge' => 'Standard',
          'badgeBg' => 'bg-secondary bg-opacity-10 text-secondary',
          'description' => 'Best for students who already understand basic development concepts',
          'oldPrice' => 'PKR 4,000',
          'price' => 'PKR 3,000',
          'discount' => '25% OFF',
          'features' => [
            ['no', 'Dedicated Mentor Support'],
            ['no', 'Step-by-Step Training Guidance'],
            ['yes', 'Industrial Project Development'],
            ['yes', 'Weekly Progress Reviews'],
            ['yes', 'Portfolio Project Development'],
            ['yes', 'Internship Completion Certificate'],
            ['yes', 'Job Opportunity for Top Performers'],
            ['yes', 'Offer Letter for University (if required)'],
          ],
          'outcomes' => [
            ['yes', 'Hands-on Real Project Experience'],
            ['yes', 'Strong Portfolio Development'],
            ['yes', 'Freelance/Entry-Level Readiness'],
          ]
        ],
        'training' => [
          'title' => 'Training Internship',
          'badge' => '🎯 RECOMMENDED FOR YOU',
          'badgeBg' => 'bg-primary bg-opacity-10 text-primary fw-semibold',
          'description' => 'Best for guided learning with structured mentorship and professional guidance',
          'oldPrice' => 'PKR 10,000',
          'price' => 'PKR 6,000',
          'discount' => '40% OFF',
          'features' => [
            ['yes-bold', 'Dedicated Mentor Support'],
            ['yes-bold', 'Step-by-Step Training Guidance'],
            ['yes', 'Industrial Project Development'],
            ['yes', 'Weekly Progress Reviews'],
            ['yes', 'Portfolio Project Development'],
            ['yes', 'Internship Completion Certificate'],
            ['yes', 'Job Opportunity for Top Performers'],
            ['yes', 'Offer Letter for University (if required)'],
          ],
          'outcomes' => [
            ['yes', 'Build real-world development projects'],
            ['yes', 'Understand professional development workflows'],
            ['yes', 'Create portfolio-ready projects'],
            ['yes', 'Prepare for junior developer opportunities'],
          ],
          'note' => '💡 Students who complete this internship typically build 3–5 portfolio projects'
        ],
        'industrial' => [
          'title' => 'Industrial Environment Internship',
          'badge' => 'Basic',
          'badgeBg' => 'bg-dark bg-opacity-10 text-dark',
          'description' => 'For experienced developers seeking real-world exposure',
          'oldPrice' => 'PKR 1,000',
          'price' => 'PKR 500',
          'discount' => '50% OFF',
          'features' => [
            ['no', 'Dedicated Mentor Support'],
            ['no', 'Step-by-Step Training Guidance'],
            ['yes', 'Industrial Project Access'],
            ['no', 'Weekly Progress Reviews'],
            ['yes', 'Real Development Environment'],
            ['yes', 'Experience Letter'],
            ['yes', 'Team Collaboration Exposure'],
            ['yes', 'Production-Level Code Exposure'],
          ],
          'outcomes' => [
            ['yes', 'Real development environment experience'],
            ['yes', 'Professional team collaboration'],
            ['yes', 'Industry exposure and networking'],
          ]
        ]
      ];

      $planOrder = [];
      // Center position gets the recommended plan
      $planOrder[] = $recommended_program ?? 'training';
      // Add the other two plans on sides
      foreach (['training', 'practice', 'industrial'] as $plan) {
        if ($plan !== $recommended_program) {
          $planOrder[] = $plan;
        }
      }
    @endphp

    @foreach($planOrder as $index => $planKey)
      @php
        $plan = $plans[$planKey];
        $isRecommended = ($planKey === ($recommended_program ?? 'training'));
        $cardClass = $isRecommended ? 'plan-card recommended' : 'plan-card';
        $borderClass = $isRecommended ? 'border border-primary' : 'border';
      @endphp

      <div class="col-md-4 d-flex">
        <div class="{{ $cardClass }} w-100 d-flex">
          <div class="card {{ $borderClass }} rounded-3 p-4 w-100 d-flex flex-column h-100">

            <div class="text-center mb-3">
              <span class="badge {{ $plan['badgeBg'] }} px-3 py-2">{{ $plan['badge'] }}</span>
            </div>

            <h5 class="fw-bold text-center mb-2 {{ $isRecommended ? 'text-primary' : '' }}">{{ $plan['title'] }}</h5>

            <p class="text-muted small text-center mb-3">{{ $plan['description'] }}</p>

            <div class="text-center mb-4">
              <div class="text-decoration-line-through text-muted small">{{ $plan['oldPrice'] }}</div>
              <div class="fs-{{ $isRecommended ? '2' : '3' }} fw-bold {{ $isRecommended ? 'text-primary' : 'text-success' }}">{{ $plan['price'] }}</div>
              <small class="text-success fw-bold">{{ $plan['discount'] }}</small>
            </div>

            <div class="flex-grow-1">
              <h6 class="text-uppercase text-muted mb-3" style="font-size:12px; font-weight: 600;">What You Will Get</h6>

              @foreach($plan['features'] as $feature)
                <div class="feat-row">
                  <span class="feat-{{ $feature[0] === 'no' ? 'no' : 'yes' }}">{{ $feature[0] === 'no' ? '✖' : '✔' }}</span>
                  <span class="{{ $feature[0] === 'no' ? 'feat-txt dim' : ($feature[0] === 'yes-bold' ? 'fw-semibold' : '') }}">{{ $feature[1] }}</span>
                </div>
              @endforeach

              <div class="mt-3 pt-2 border-top">
                <h6 class="text-uppercase text-muted mb-2" style="font-size:12px; font-weight: 600;">What You Can Achieve</h6>
                @foreach($plan['outcomes'] as $outcome)
                  <div class="feat-row">
                    <span class="feat-yes">✔</span>
                    <span>{{ $outcome[1] }}</span>
                  </div>
                @endforeach
                @if(isset($plan['note']))
                  <small class="text-muted d-block mt-2" style="font-size: 12px;">{{ $plan['note'] }}</small>
                @endif
              </div>
            </div>

            @if($isRecommended)
              <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded-2 mb-3">
                <small class="text-primary d-block mb-1">
                  <strong>✓ This recommendation is based on your skill assessment answers.</strong>
                </small>
                <small class="text-muted d-block">Students with similar skill levels usually choose this program.</small>
              </div>
            @endif

            <form method="POST" action="{{ route('intern.register.complete') }}" class="mt-4">
              @csrf
              <input type="hidden" name="selected_plan" value="{{ $planKey }}">
              <button type="submit" class="btn btn-{{ $isRecommended ? 'primary fw-bold btn-lg' : 'outline-secondary fw-semibold' }} w-100">
                {{ $isRecommended ? 'Continue with this Program →' : 'Choose This Plan' }}
              </button>
            </form>

          </div>
        </div>
      </div>
    @endforeach

  </div>

  {{-- Back button --}}
  <div class="text-center">
    <a href="{{ route('intern.register.step2') }}" class="btn btn-light text-muted">
      ← Back to Assessment
    </a>
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
  var currentPct = parseInt(label.textContent);
  var target = currentPct, duration = 1600, startTime = null;
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