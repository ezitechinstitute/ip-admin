@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Carbon\Carbon;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

// Calculate days properly with clean numbers
$startDate = Carbon::parse($intern->start_date);
$endDate = $startDate->copy()->addMonths(6);
$totalDays = $startDate->diffInDays($endDate);
$elapsedDays = $startDate->diffInDays(Carbon::now());
$remainingDays = max(0, $totalDays - $elapsedDays);
$progressPercent = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100) : 0;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'My Profile')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
])
@endsection

@section('page-style')
<style>
/* ============================================
   PROFILE HEADER STYLES
   ============================================ */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    padding: 1.5rem;
}

.profile-avatar-wrapper {
    position: relative;
    display: inline-block;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.edit-avatar-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    background: #667eea;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    border: 2px solid white;
    font-size: 12px;
    transition: all 0.3s ease;
}

.edit-avatar-btn:hover {
    background: #5a67d8;
    transform: scale(1.1);
}

/* ============================================
   MEMBER SINCE CARD
   ============================================ */
.member-since-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 0.75rem;
    padding: 0.5rem 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.member-since-card:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.member-since-icon {
    width: 32px;
    height: 32px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.member-since-icon i {
    font-size: 16px;
    color: white;
}

.member-since-info {
    text-align: left;
}

.member-since-info small {
    font-size: 0.65rem;
    color: rgba(255, 255, 255, 0.7);
    display: block;
    line-height: 1.2;
}

.member-since-info h6 {
    font-size: 0.85rem;
    color: white;
    margin: 0;
    font-weight: 600;
}

/* ============================================
   STATISTICS CARDS
   ============================================ */
.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 0.75rem;
    text-align: center;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem auto;
}

.stat-icon i {
    font-size: 20px;
    color: white;
}

.stat-card h3 {
    font-size: 1.3rem;
    margin-bottom: 0.25rem;
    color: #1e293b;
}

.stat-card small {
    font-size: 0.7rem;
    color: #64748b;
}

/* ============================================
   CLICKABLE INFO CARDS
   ============================================ */
.info-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-card.clickable {
    cursor: pointer;
}

.info-card.clickable::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.info-card.clickable:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.info-card.clickable:hover::before {
    opacity: 0.03;
}

.info-card > * {
    position: relative;
    z-index: 1;
}

.card-edit-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    background: #f1f5f9;
    border-radius: 6px;
    padding: 0.25rem 0.5rem;
    font-size: 0.65rem;
    color: #667eea;
    transition: all 0.3s ease;
    z-index: 2;
    opacity: 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.info-card.clickable:hover .card-edit-btn {
    opacity: 1;
    background: #667eea;
    color: white;
}

/* ============================================
   CONTACT SECTION WITH PROFESSIONAL ICONS
   ============================================ */
.contact-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.contact-header i {
    font-size: 1.1rem;
    color: #667eea;
}

.contact-header h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}

.contact-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: rgba(102, 126, 234, 0.05);
    transform: translateX(3px);
}

.contact-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.email-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.phone-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.location-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.education-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.contact-icon i {
    font-size: 16px;
    color: white;
}

.contact-info {
    flex: 1;
}

.contact-type {
    display: block;
    font-size: 0.6rem;
    color: #94a3b8;
    margin-bottom: 0.15rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.contact-text {
    display: block;
    font-size: 0.8rem;
    color: #1e293b;
    font-weight: 500;
}

/* ============================================
   ABOUT & SKILLS SECTIONS
   ============================================ */
.section-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.section-header i {
    font-size: 1rem;
    color: #667eea;
}

.section-header h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
}

.bio-text {
    font-size: 0.8rem;
    color: #475569;
    line-height: 1.5;
    margin-bottom: 0;
}

.skill-badge {
    display: inline-block;
    padding: 0.3rem 0.75rem;
    background: #f1f5f9;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 500;
    color: #667eea;
    margin: 0.25rem;
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.skill-badge:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: translateY(-2px);
    border-color: transparent;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.empty-state {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.5rem;
}

.empty-state i {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
    color: #667eea;
}

.empty-state p {
    font-size: 0.7rem;
    margin-bottom: 0;
    color: #64748b;
}

/* ============================================
   TIMELINE SECTION
   ============================================ */
.timeline-item {
    position: relative;
    padding-left: 1.5rem;
    padding-bottom: 1rem;
    border-left: 2px solid #e2e8f0;
}

.timeline-item:last-child {
    padding-bottom: 0;
    border-left: 2px solid transparent;
}

.timeline-badge {
    position: absolute;
    left: -0.5rem;
    top: 0;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    background: #667eea;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e2e8f0;
}

.timeline-content h6 {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    font-weight: 600;
    color: #1e293b;
}

.timeline-content small {
    font-size: 0.6rem;
    color: #94a3b8;
}

.timeline-content p {
    font-size: 0.7rem;
    margin-bottom: 0;
    color: #64748b;
}

/* ============================================
   PROGRESS BAR
   ============================================ */
.progress {
    height: 6px;
    border-radius: 10px;
    background-color: #e2e8f0;
}

.progress-bar-custom {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

/* ============================================
   BADGES & ALERTS
   ============================================ */
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25rem 0.6rem;
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.7rem;
}

.alert-info {
    background-color: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}

.btn-sm-custom {
    padding: 0.3rem 0.75rem;
    font-size: 0.75rem;
}

/* ============================================
   UTILITY CLASSES
   ============================================ */
.text-muted {
    color: #64748b !important;
}
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar-wrapper">
                        <img src="{{ $profileImage }}" alt="Profile" class="profile-avatar">
                        <button type="button" class="edit-avatar-btn" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                            <i class="ti ti-camera"></i>
                        </button>
                    </div>
                    <div>
                        <h4 class="text-white mb-1">{{ $intern->name }}</h4>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge bg-white text-primary badge-sm">
                                <i class="ti ti-id me-1"></i>{{ $intern->eti_id }}
                            </span>
                            <span class="badge bg-white text-primary badge-sm">
                                <i class="ti ti-code me-1"></i>{{ $intern->int_technology ?? 'Not Assigned' }}
                            </span>
                            <span class="badge bg-success badge-sm">
                                <i class="ti ti-circle-check me-1"></i>{{ $intern->int_status ?? 'Active' }}
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('intern.profile.edit') }}" class="btn btn-light btn-sm-custom">
                                <i class="ti ti-edit me-1"></i> Edit Profile
                            </a>
                            <a href="{{ route('intern.profile.public', $intern->eti_id) }}" class="btn btn-outline-light btn-sm-custom" target="_blank">
                                <i class="ti ti-world me-1"></i> Public View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="member-since-card">
                    <div class="member-since-icon">
                        <i class="ti ti-calendar"></i>
                    </div>
                    <div class="member-since-info">
                        <small>Member Since</small>
                        <h6 class="mb-0">{{ Carbon::parse($intern->created_at ?? $intern->start_date)->format('M Y') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon mx-auto">
                    <i class="ti ti-tasks"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['total_tasks'] ?? 0) }}</h3>
                <small>Total Tasks</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="ti ti-check-circle"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['completed_tasks'] ?? 0) }}</h3>
                <small>Completed</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <i class="ti ti-briefcase"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['total_projects'] ?? 0) }}</h3>
                <small>Total Projects</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card">
                <div class="stat-icon mx-auto" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="ti ti-rocket"></i>
                </div>
                <h3 class="mb-0">{{ number_format($stats['completed_projects'] ?? 0) }}</h3>
                <small>Completed</small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Contact Info Card - Clickable -->
            <div class="info-card clickable" onclick="window.location='{{ route('intern.profile.edit') }}'">
                <div class="card-edit-btn">
                    <i class="ti ti-edit"></i> Edit
                </div>
                <div class="contact-header">
                    <i class="ti ti-address-book"></i>
                    <h5>Contact Information</h5>
                </div>
                <div class="contact-list">
                    <div class="contact-item">
                        <div class="contact-icon email-icon">
                            <i class="ti ti-mail"></i>
                        </div>
                        <div class="contact-info">
                            <span class="contact-type">Email Address</span>
                            <span class="contact-text">{{ $intern->email }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon phone-icon">
                            <i class="ti ti-phone-call"></i>
                        </div>
                        <div class="contact-info">
                            <span class="contact-type">Phone Number</span>
                            <span class="contact-text">{{ $intern->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon location-icon">
                            <i class="ti ti-map-pin"></i>
                        </div>
                        <div class="contact-info">
                            <span class="contact-type">Location</span>
                            <span class="contact-text">{{ $intern->city ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon education-icon">
                            <i class="ti ti-school"></i>
                        </div>
                        <div class="contact-info">
                            <span class="contact-type">Education</span>
                            <span class="contact-text">{{ $intern->university ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Internship Progress -->
            <div class="info-card">
                <div class="section-header">
                    <i class="ti ti-chart-line"></i>
                    <h5>Internship Progress</h5>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Overall Progress</small>
                        <small class="fw-bold">{{ $progressPercent }}%</small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">
                        <i class="ti ti-calendar-start me-1"></i>{{ $startDate->format('d M Y') }}
                    </small>
                    <small class="text-muted">
                        <i class="ti ti-calendar-end me-1"></i>{{ $endDate->format('d M Y') }}
                    </small>
                </div>
                <div class="alert alert-info alert-sm mb-0">
                    <i class="ti ti-clock me-1"></i>
                    <strong>{{ number_format($remainingDays) }} days</strong> remaining
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- About Me Card - Clickable -->
            <div class="info-card clickable" onclick="window.location='{{ route('intern.profile.edit') }}'">
                <div class="card-edit-btn">
                    <i class="ti ti-edit"></i> Edit
                </div>
                <div class="section-header">
                    <i class="ti ti-user"></i>
                    <h5>About Me</h5>
                </div>
                @if($intern->bio)
                    <p class="bio-text">{{ $intern->bio }}</p>
                @else
                    <div class="empty-state">
                        <i class="ti ti-message-circle"></i>
                        <p>No bio added yet. Click to add a professional introduction.</p>
                    </div>
                @endif
            </div>

         <!-- Skills Card - Clickable -->
<div class="info-card clickable" onclick="window.location='{{ route('intern.profile.edit') }}'">
    <div class="card-edit-btn">
        <i class="ti ti-edit"></i> Edit
    </div>
    <div class="section-header">
        <i class="ti ti-code"></i>
        <h5>Skills & Technologies</h5>
    </div>
    @if(isset($skills) && $skills->count() > 0)
        <div class="d-flex flex-wrap">
            @foreach($skills as $skill)
                <span class="skill-badge">{{ $skill }}</span>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="ti ti-tools"></i>
            <p>No skills added yet. Click to add your technical skills.</p>
        </div>
    @endif
</div>

<!-- Timeline Section -->
            <div class="info-card mb-0">
                <div class="section-header">
                    <i class="ti ti-calendar-stats"></i>
                    <h5>Internship Timeline</h5>
                </div>
                <div>
                    <div class="timeline-item">
                        <div class="timeline-badge bg-success"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <h6>Internship Started</h6>
                                <small>{{ $startDate->format('d M Y') }}</small>
                            </div>
                            <p>Your journey began with Ezitech</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-badge bg-primary"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <h6>Current Progress</h6>
                                <small>{{ Carbon::now()->format('d M Y') }}</small>
                            </div>
                            <p>{{ $progressPercent }}% completed • {{ number_format($remainingDays) }} days remaining</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-badge bg-warning"></div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between">
                                <h6>Expected Completion</h6>
                                <small>{{ $endDate->format('d M Y') }}</small>
                            </div>
                            <p>Target completion date for your internship</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Update Profile Picture</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="imagePreview" src="{{ $profileImage }}" alt="Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #667eea;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label form-label-sm">Choose New Image</label>
                        <input type="file" name="profile_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this)">
                        <small class="text-muted d-block mt-1">Max 2MB. Recommended: Square image (400x400px). JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection