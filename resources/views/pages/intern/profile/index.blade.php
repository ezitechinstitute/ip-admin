@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Carbon\Carbon;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

$startDate = Carbon::parse($intern->start_date);
$endDate = $startDate->copy()->addMonths(6);
$totalDays = $startDate->diffInDays($endDate);
$elapsedDays = $startDate->diffInDays(Carbon::now());
$remainingDays = max(0, $totalDays - $elapsedDays);
$progressPercent = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100) : 0;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'My Profile')

{{-- Bootstrap Icons CDN --}}
@section('page-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endsection

@section('content')
<div class="container-xxl py-5">
    
    {{-- Header Section --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="position-relative">
                        <img src="{{ $profileImage }}" alt="Profile" class="rounded-circle border border-4 border-light shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                        <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle shadow-sm" 
                                data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                            <i class="bi bi-camera-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <h2 class="fw-bold mb-1">{{ $intern->name }}</h2>
                    <p class="text-muted mb-3">
                        <span class="badge bg-light text-dark border me-2"><i class="bi bi-fingerprint me-1"></i> {{ $intern->eti_id }}</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2">
                            <i class="bi bi-code-slash me-1"></i> {{ $intern->int_technology ?? 'Developer' }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ $intern->int_status ?? 'Active' }}
                        </span>
                    </p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('intern.profile.edit') }}" class="btn btn-primary btn-sm px-4">
                            <i class="bi bi-pencil-square me-2"></i>Edit Profile
                        </a>
                        <a href="{{ route('intern.profile.public', $intern->eti_id) }}" class="btn btn-outline-secondary btn-sm px-4" target="_blank">
                            <i class="bi bi-share me-2"></i>Public View
                        </a>
                    </div>
                </div>
                <div class="col-md-auto mt-3 mt-md-0 text-md-end">
                    <div class="p-3 bg-light rounded-3 border">
                        <small class="text-uppercase fw-semibold text-muted d-block mb-1" style="font-size: 0.7rem;">Member Since</small>
                        <span class="h6 mb-0 fw-bold">{{ Carbon::parse($intern->created_at ?? $intern->start_date)->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        @php
            $stats_data = [
                ['label' => 'Total Tasks', 'value' => $stats['total_tasks'] ?? 0, 'icon' => 'bi-list-task', 'color' => 'primary'],
                ['label' => 'Completed', 'value' => $stats['completed_tasks'] ?? 0, 'icon' => 'bi-check2-all', 'color' => 'success'],
                ['label' => 'Projects', 'value' => $stats['total_projects'] ?? 0, 'icon' => 'bi-folder', 'color' => 'info'],
                ['label' => 'Achievements', 'value' => $stats['completed_projects'] ?? 0, 'icon' => 'bi-trophy', 'color' => 'warning']
            ];
        @endphp
        @foreach($stats_data as $item)
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-6 fw-bold text-{{ $item['color'] }} mb-1">{{ number_format($item['value']) }}</div>
                    <div class="small fw-bold text-uppercase text-muted">{{ $item['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Contact Info</h5>
                </div>
                <div class="card-body px-4">
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-primary-subtle text-primary rounded p-2 me-3"><i class="bi bi-envelope"></i></div>
                        <div><small class="text-muted d-block">Email</small><strong>{{ $intern->email }}</strong></div>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-success-subtle text-success rounded p-2 me-3"><i class="bi bi-telephone"></i></div>
                        <div><small class="text-muted d-block">Phone</small><strong>{{ $intern->phone ?? 'Not provided' }}</strong></div>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                        <div class="bg-info-subtle text-info rounded p-2 me-3"><i class="bi bi-geo-alt"></i></div>
                        <div><small class="text-muted d-block">Location</small><strong>{{ $intern->city ?? 'Not provided' }}</strong></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Internship Progress</h5>
                </div>
                <div class="card-body px-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small fw-bold">{{ $progressPercent }}% Complete</span>
                        <span class="small text-muted">{{ $remainingDays }} days left</span>
                    </div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <div class="alert alert-primary border-0 small mb-0">
                        <i class="bi bi-info-circle me-2"></i> End date: {{ $endDate->format('d M Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">About Me</h5>
                    <a href="{{ route('intern.profile.edit') }}" class="btn btn-link btn-sm text-decoration-none p-0">Edit</a>
                </div>
                <div class="card-body px-4 pb-4">
                    @if($intern->bio)
                        <p class="text-muted lh-lg mb-0">{{ $intern->bio }}</p>
                    @else
                        <div class="text-center py-4 bg-light rounded-3 border border-dashed">
                            <i class="bi bi-chat-left-dots text-muted display-6"></i>
                            <p class="mt-2 mb-0 small text-muted">Tell us about yourself</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Skills & Expertise</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    @if(isset($skills) && $skills->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <span class="badge bg-light text-primary border px-3 py-2">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No skills added yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div class="modal fade" id="uploadImageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Profile Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imagePreview" src="{{ $profileImage }}" class="rounded-circle mb-3 border" style="width: 100px; height: 100px; object-fit: cover;">
                    <input type="file" name="profile_image" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this)">
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
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
</script>
@endsection