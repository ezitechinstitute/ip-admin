@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Carbon\Carbon;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

$startDate = Carbon::parse($intern->start_date ?? now());
$endDate = $startDate->copy()->addMonths(6);
$totalDays = $startDate->diffInDays($endDate);
$elapsedDays = $startDate->diffInDays(Carbon::now());
$remainingDays = max(0, $totalDays - $elapsedDays);
$progressPercent = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100) : 0;

// Calculate stats for rings (CORRECT PERCENTAGES)
$taskCompletionRate = ($stats['total_tasks'] ?? 0) > 0 
    ? round((($stats['completed_tasks'] ?? 0) / ($stats['total_tasks'] ?? 1)) * 100) 
    : 0;
$projectCompletionRate = ($stats['total_projects'] ?? 0) > 0 
    ? round((($stats['completed_projects'] ?? 0) / ($stats['total_projects'] ?? 1)) * 100) 
    : 0;

// Ring circumference for correct animation
$ringCircumference = 2 * pi() * 80; // 502.65
$taskOffset = $ringCircumference - ($taskCompletionRate / 100) * $ringCircumference;
$projectOffset = $ringCircumference - ($projectCompletionRate / 100) * $ringCircumference;
$internshipOffset = $ringCircumference - ($progressPercent / 100) * $ringCircumference;

// Achievement Badges
$badges = [
    ['name' => 'Task Starter', 'icon' => 'bi-rocket-takeoff-fill', 'color' => 'primary', 'earned' => ($stats['completed_tasks'] ?? 0) >= 1],
    ['name' => 'Task Master', 'icon' => 'bi-trophy-fill', 'color' => 'warning', 'earned' => ($stats['completed_tasks'] ?? 0) >= 5],
    ['name' => 'Project Builder', 'icon' => 'bi-briefcase-fill', 'color' => 'success', 'earned' => ($stats['completed_projects'] ?? 0) >= 1],
    ['name' => 'Code Champion', 'icon' => 'bi-code-square', 'color' => 'info', 'earned' => ($stats['completed_projects'] ?? 0) >= 2],
    ['name' => 'Perfect Attendance', 'icon' => 'bi-calendar-check-fill', 'color' => 'purple', 'earned' => ($stats['total_tasks'] ?? 0) >= 10],
    ['name' => 'Productivity King', 'icon' => 'bi-graph-up', 'color' => 'danger', 'earned' => ($stats['completed_tasks'] ?? 0) >= 10],
];

$earnedCount = collect($badges)->where('earned', true)->count();
$totalBadges = count($badges);
@endphp

@extends('layouts/layoutMaster')

@section('title', 'My Profile')

@section('page-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.5);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .glass-card {
        background: var(--glass-bg) !important;
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--card-radius);
        box-shadow: var(--glass-shadow);
        transition: var(--transition-smooth);
    }

    .glass-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Progress Rings */
    .progress-card {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto;
    }
    .progress-ring {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }
    .progress-ring-bg {
        fill: none;
        stroke: rgba(0, 0, 0, 0.06);
        stroke-width: 10;
    }
    .progress-ring-fill {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        stroke-dasharray: 502.65;
        transition: stroke-dashoffset 1.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        filter: drop-shadow(0 0 4px rgba(0,0,0,0.1));
    }
    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    .progress-value {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1.2;
    }
    .progress-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c86a3;
        margin-top: 4px;
    }
    .progress-sub {
        font-size: 0.7rem;
        font-weight: 500;
        color: #94a3b8;
        margin-top: 4px;
    }
    .progress-detail {
        font-size: 0.65rem;
        color: #6c86a3;
        margin-top: 2px;
    }
    .ring-tasks .progress-ring-fill { stroke: #3b82f6; }
    .ring-projects .progress-ring-fill { stroke: #10b981; }
    .ring-internship .progress-ring-fill { stroke: #8b5cf6; }

    /* Badge Cards */
    .badge-card {
        text-align: center;
        padding: 0.75rem;
        border-radius: 1rem;
        transition: var(--transition-smooth);
        cursor: pointer;
    }
    .badge-card.earned {
        background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(16,185,129,0.05));
        border: 1px solid rgba(59,130,246,0.2);
    }
    .badge-card.locked {
        opacity: 0.5;
        filter: grayscale(0.3);
    }
    .badge-card:hover {
        transform: translateY(-4px);
    }
    .badge-icon {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
    }

    /* Contact Items */
    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        transition: var(--transition-smooth);
    }
    .contact-item:hover { background: rgba(0,0,0,0.02); transform: translateX(5px); }
    .contact-icon {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        flex-shrink: 0;
    }

    /* Skills Badges */
    .skill-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
        transition: var(--transition-smooth);
    }
    .skill-badge:hover { transform: translateY(-2px); }

    /* Progress Bar */
    .progress-custom {
        height: 8px;
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar-custom {
        height: 100%;
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-card { animation: fadeInUp 0.4s ease-out forwards; }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    
    {{-- Header Profile Card with 230px Banner --}}
    <div class="glass-card mb-4 animate-card overflow-hidden" style="padding: 0 !important;">
        <div style="height: 230px; background: linear-gradient(150deg, #f472b6 10%, #a78bfa 50%, #60a5fa 100%); position: relative;">
        </div>

        <div class="px-4 pb-4" style="position: relative; margin-top: -50px;">
            <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-end gap-3">
                    <div class="position-relative flex-shrink-0">
                        <img src="{{ $profileImage }}" alt="Profile"
                             class="rounded-circle border border-3 border-white shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover; display: block;">
                        <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle shadow-sm p-1"
                                data-bs-toggle="modal" data-bs-target="#uploadImageModal"
                                style="width: 34px; height: 34px;">
                            <i class="bi bi-camera-fill" style="font-size: 12px;"></i>
                        </button>
                    </div>

                    <div class="pb-1">
                        <h3 class="fw-bold mb-2">{{ $intern->name }}</h3>
                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                            <span><i class="bi bi-code-slash me-1"></i>{{ $intern->int_technology ?? 'Developer' }}</span>
                            <span><i class="bi bi-geo-alt me-1"></i>{{ $intern->city ?? 'N/A' }}</span>
                            <span><i class="bi bi-calendar3 me-1"></i>Joined {{ Carbon::parse($intern->created_at ?? $intern->start_date)->format('F Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 pb-1">
                    <a href="{{ route('intern.profile.edit') }}" class="btn btn-primary btn-sm px-4 rounded-pill">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>
                    <a href="{{ route('intern.profile.public', $intern->eti_id) }}" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" target="_blank">
                        <i class="bi bi-share me-1"></i> Public View
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 3 Progress Rings Row with Correct Percentages --}}
    <div class="row g-4 mb-4">
        {{-- Tasks Ring --}}
        <div class="col-md-4">
            <div class="glass-card p-4 text-center ring-tasks animate-card" style="animation-delay: 0.1s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill" cx="100" cy="100" r="80" stroke-dashoffset="{{ $ringCircumference }}" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value text-primary">{{ $taskCompletionRate }}%</div>
                        <div class="progress-label">Tasks Completed</div>
                        <div class="progress-sub">{{ $stats['completed_tasks'] ?? 0 }}/{{ $stats['total_tasks'] ?? 0 }}</div>
                        <div class="progress-detail">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $stats['completed_tasks'] ?? 0 }} done
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Projects Ring --}}
        <div class="col-md-4">
            <div class="glass-card p-4 text-center ring-projects animate-card" style="animation-delay: 0.2s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill" cx="100" cy="100" r="80" stroke-dashoffset="{{ $ringCircumference }}" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value text-success">{{ $projectCompletionRate }}%</div>
                        <div class="progress-label">Projects Completed</div>
                        <div class="progress-sub">{{ $stats['completed_projects'] ?? 0 }}/{{ $stats['total_projects'] ?? 0 }}</div>
                        <div class="progress-detail">
                            <i class="bi bi-briefcase-fill me-1"></i>{{ $stats['completed_projects'] ?? 0 }} completed
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Internship Ring --}}
        <div class="col-md-4">
            <div class="glass-card p-4 text-center ring-internship animate-card" style="animation-delay: 0.3s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill" cx="100" cy="100" r="80" stroke-dashoffset="{{ $ringCircumference }}" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value text-purple">{{ $progressPercent }}%</div>
                        <div class="progress-label">Internship</div>
                        <div class="progress-sub">{{ round($remainingDays) }} days left</div>
                        <div class="progress-detail">
                            <i class="bi bi-calendar-check me-1"></i>Ends {{ $endDate->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content 2 Columns --}}
    <div class="row g-4">
        
        {{-- LEFT COLUMN --}}
        <div class="col-lg-4">
            <div class="glass-card mb-4 animate-card" style="animation-delay: 0.4s;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Contact Information</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="contact-item">
                        <div class="contact-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-envelope fs-5"></i></div>
                        <div><small class="text-muted d-block">Email</small><span class="fw-medium">{{ $intern->email }}</span></div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon bg-success bg-opacity-10 text-success"><i class="bi bi-telephone fs-5"></i></div>
                        <div><small class="text-muted d-block">Phone</small><span class="fw-medium">{{ $intern->phone ?? 'Not provided' }}</span></div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon bg-info bg-opacity-10 text-info"><i class="bi bi-geo-alt fs-5"></i></div>
                        <div><small class="text-muted d-block">Location</small><span class="fw-medium">{{ $intern->city ?? 'Not provided' }}</span></div>
                    </div>
                </div>
            </div>

            <div class="glass-card animate-card" style="animation-delay: 0.45s;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Internship Details</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-semibold text-muted">Overall Progress</span>
                            <span class="small fw-bold text-primary">{{ $progressPercent }}%</span>
                        </div>
                        <div class="progress-custom"><div class="progress-bar-custom bg-primary" style="width: {{ $progressPercent }}%;"></div></div>
                    </div>
                    <div class="alert alert-primary border-0 small mb-0 rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Internship ends: <strong>{{ $endDate->format('d M Y') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-8">
            
            <div class="glass-card mb-4 animate-card" style="animation-delay: 0.5s;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>About Me</h5>
                    <a href="{{ route('intern.profile.edit') }}" class="btn btn-link btn-sm text-decoration-none p-0"><i class="bi bi-pencil-fill me-1"></i> Edit</a>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($intern->bio)
                        <p class="text-muted mb-0 lh-base">{{ $intern->bio }}</p>
                    @else
                        <div class="text-center py-4 bg-light bg-opacity-30 rounded-3">
                            <i class="bi bi-chat-left-dots text-muted fs-1"></i>
                            <p class="mt-2 mb-0 small text-muted">No bio added yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="glass-card mb-4 animate-card" style="animation-delay: 0.55s;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-code-square me-2 text-primary"></i>Skills & Expertise</h5>
                    <a href="{{ route('intern.profile.edit') }}" class="btn btn-link btn-sm text-decoration-none p-0"><i class="bi bi-plus-circle-fill me-1"></i> Add</a>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($skills && $skills->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <span class="skill-badge bg-light text-primary border border-primary border-opacity-25">
                                    <i class="bi bi-check-circle-fill me-1 small"></i>{{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-light bg-opacity-30 rounded-3">
                            <i class="bi bi-tags text-muted fs-1"></i>
                            <p class="mt-2 mb-0 small text-muted">No skills added yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="glass-card animate-card" style="animation-delay: 0.6s;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Achievement Badges</h5>
                    <span class="badge bg-primary rounded-pill">{{ $earnedCount }}/{{ $totalBadges }} Earned</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-2">
                        @foreach($badges as $badge)
                        <div class="col-4 col-md-3">
                            <div class="badge-card {{ $badge['earned'] ? 'earned' : 'locked' }}">
                                <div class="badge-icon text-{{ $badge['color'] }}"><i class="bi {{ $badge['icon'] }}"></i></div>
                                <div class="small fw-semibold">{{ $badge['name'] }}</div>
                                @if(!$badge['earned'])
                                <div class="small text-muted"><i class="bi bi-lock-fill"></i></div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Upload Image Modal --}}
<div class="modal fade" id="uploadImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content glass-card border-0">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Update Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreview" src="{{ $profileImage }}" class="rounded-circle mb-3 shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                    <input type="file" name="profile_image" class="form-control form-control-sm rounded-pill" accept="image/*" onchange="previewImage(this)">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}

// Animate rings with correct percentages
document.addEventListener('DOMContentLoaded', function() {
    const circumference = 2 * Math.PI * 80; // 502.65
    
    // Tasks Ring
    const taskPercent = {{ $taskCompletionRate }};
    const taskRing = document.querySelector('.ring-tasks .progress-ring-fill');
    if (taskRing) {
        const taskOffset = circumference - (taskPercent / 100) * circumference;
        taskRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            taskRing.style.strokeDashoffset = taskOffset;
        }, 100);
    }
    
    // Projects Ring
    const projectPercent = {{ $projectCompletionRate }};
    const projectRing = document.querySelector('.ring-projects .progress-ring-fill');
    if (projectRing) {
        const projectOffset = circumference - (projectPercent / 100) * circumference;
        projectRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            projectRing.style.strokeDashoffset = projectOffset;
        }, 200);
    }
    
    // Internship Ring
    const internshipPercent = {{ $progressPercent }};
    const internshipRing = document.querySelector('.ring-internship .progress-ring-fill');
    if (internshipRing) {
        const internshipOffset = circumference - (internshipPercent / 100) * circumference;
        internshipRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            internshipRing.style.strokeDashoffset = internshipOffset;
        }, 300);
    }
});
</script>
@endsection