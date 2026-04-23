@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

$skillsArray = $skills->toArray();
$skillsArray = array_filter($skillsArray, function($skill) {
    return !filter_var($skill, FILTER_VALIDATE_EMAIL);
});
$skillsArray = array_values($skillsArray);
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit Profile')

@section('page-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .avatar-upload-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #f8f9fa;
    }
    .skill-tag {
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
    }
    .skill-tag .btn-close {
        font-size: 0.5rem;
        margin-left: 0.5rem;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
    }
</style>
@endsection

@section('content')
<div class="container-py-4">
    <form id="profileForm" action="{{ route('intern.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Profile</h4>
                <p class="text-muted small mb-0">Update your account information and public profile.</p>
            </div>
            <a href="{{ route('intern.profile') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Profile
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center py-4">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $profileImage }}" alt="Profile" class="rounded-circle avatar-upload-preview shadow-sm" id="liveAvatar">
                            <button type="button" class="btn btn-primary btn-sm position-absolute bottom-0 end-0 rounded-circle" 
                                    data-bs-toggle="modal" data-bs-target="#uploadImageModal" style="width: 32px; height: 32px; padding: 0;">
                                <i class="bi bi-camera"></i>
                            </button>
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $intern->name }}</h5>
                        <p class="text-muted small mb-0">{{ $intern->eti_id }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-code-slash me-2 text-primary"></i>Skills & Technologies</h6>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="border rounded p-2 bg-light mb-2 d-flex flex-wrap gap-2" id="skillsWrap">
                            <input type="text" id="skills_text" class="form-control form-control-sm border-0 bg-transparent" 
                                   placeholder="Add a skill..." style="box-shadow: none; min-width: 100px;">
                        </div>
                        <input type="hidden" name="skills" id="skills_hidden">
                        <small class="text-muted">Press Enter or comma to add tags.</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Personal Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $intern->name) }}" required>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" class="form-control border-start-0 bg-light" value="{{ $intern->email }}" disabled>
                                </div>
                                <small class="text-muted italic"><i class="bi bi-info-circle me-1"></i>Email cannot be modified.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                       value="{{ old('phone', $intern->phone) }}" placeholder="+92 3xx xxxxxxx">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                       value="{{ old('city', $intern->city) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">University</label>
                                <input type="text" name="university" class="form-control" value="{{ old('university', $intern->university) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control" rows="4" id="bioTextarea" maxlength="1000">{{ old('bio', $intern->bio) }}</textarea>
                                <div class="d-flex justify-content-end mt-1">
                                    <small class="text-muted"><span id="bioCount">0</span>/1000</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h6 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2 text-danger"></i>Security</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="Required for change">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" oninput="checkStrength(this.value)">
                                <div class="progress mt-2" style="height: 4px;">
                                    <div id="strengthFill" class="progress-bar" role="progressbar"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" oninput="checkMatch()">
                                <small id="matchMsg" class="mt-1 d-block"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mb-5">
                    <button type="reset" class="btn btn-light px-4">Reset</button>
                    <button type="submit" class="btn btn-primary px-5">Save Profile</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h6 class="fw-bold mb-0">Change Photo</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPreview" src="{{ $profileImage }}" class="rounded-circle mb-3 border" style="width: 80px; height: 80px; object-fit: cover;">
                    <input type="file" name="profile_image" class="form-control form-control-sm" accept="image/*" onchange="previewModal(this)" required>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Upload New Photo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    /* --- Skills Tagger --- */
    const wrap = document.getElementById("skillsWrap");
    const input = document.getElementById("skills_text");
    const hidden = document.getElementById("skills_hidden");
    let skills = @json($skillsArray);

    function renderTags() {
        wrap.querySelectorAll('.skill-tag').forEach(t => t.remove());
        skills.forEach((s, i) => {
            const tag = document.createElement('span');
            tag.className = 'badge bg-primary-subtle text-primary border border-primary-subtle skill-tag py-2 px-3';
            tag.innerHTML = `${s} <button type="button" class="btn-close" onclick="removeSkill(${i})"></button>`;
            wrap.insertBefore(tag, input);
        });
        hidden.value = JSON.stringify(skills);
    }

    window.removeSkill = function(index) {
        skills.splice(index, 1);
        renderTags();
    };

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            let val = input.value.trim().replace(/,$/, '');
            if (val && !skills.includes(val)) {
                skills.push(val);
                renderTags();
            }
            input.value = '';
        }
    });

    renderTags();

    /* --- Character Count --- */
    const bio = document.getElementById("bioTextarea");
    const cnt = document.getElementById("bioCount");
    bio.addEventListener('input', () => cnt.textContent = bio.value.length);
    cnt.textContent = bio.value.length;
});

function checkStrength(v) {
    const fill = document.getElementById('strengthFill');
    let strength = 0;
    if (v.length > 5) strength += 25;
    if (/[A-Z]/.test(v)) strength += 25;
    if (/[0-9]/.test(v)) strength += 25;
    if (/[^A-Za-z0-9]/.test(v)) strength += 25;
    
    fill.style.width = strength + '%';
    fill.className = 'progress-bar ' + (strength < 50 ? 'bg-danger' : (strength < 100 ? 'bg-warning' : 'bg-success'));
}

function checkMatch() {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('new_password_confirmation').value;
    const msg = document.getElementById('matchMsg');
    if (!cp) return msg.textContent = '';
    msg.textContent = (np === cp) ? 'Passwords match' : 'Passwords do not match';
    msg.className = 'mt-1 d-block small ' + (np === cp ? 'text-success' : 'text-danger');
}

function previewModal(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('modalPreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection