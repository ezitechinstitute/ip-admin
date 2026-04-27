@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

// Get skills from controller or database
$skillsList = [];
if (isset($skills) && $skills->count() > 0) {
    $skillsList = $skills->toArray();
} elseif (isset($skillsArray) && is_array($skillsArray) && count($skillsArray) > 0) {
    $skillsList = $skillsArray;
} else {
    // Direct database fetch
    if (Schema::hasTable('intern_skills')) {
        $skillsList = DB::table('intern_skills')
            ->where('intern_id', $intern->int_id)
            ->pluck('skill')
            ->toArray();
    }
}
$skillsList = array_values(array_filter($skillsList, function($skill) {
    return !empty(trim($skill));
}));
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit Profile')

@section('page-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .avatar-upload-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #f8f9fa;
    }
    .skill-tag {
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .skill-tag:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .skill-tag .btn-close {
        font-size: 0.5rem;
        margin-left: 0.5rem;
        width: 0.5rem;
        height: 0.5rem;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .skill-tag .btn-close:hover {
        opacity: 1;
    }
    .form-label {
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
    }
    .skills-container {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.5rem;
        background: #f8f9fa;
        min-height: 80px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.25rem;
    }
    .skills-container input {
        flex: 1;
        min-width: 150px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <form id="profileForm" action="{{ route('intern.profile.update') }}" method="POST">
        @csrf

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
                        <div class="skills-container mb-2" id="skillsWrap">
                            @foreach($skillsList as $skill)
                            <span class="skill-tag badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3" data-skill="{{ $skill }}">
                                {{ $skill }}
                                <button type="button" class="btn-close" onclick="confirmRemoveSkill('{{ addslashes($skill) }}', {{ $loop->index }})"></button>
                            </span>
                            @endforeach
                            <input type="text" id="skills_text" class="form-control form-control-sm border-0 bg-transparent" 
                                   placeholder="Type a skill and press Enter..." style="box-shadow: none; width: auto; flex: 1;">
                        </div>
                        <input type="hidden" name="skills" id="skills_hidden" value='@json($skillsList)'>
                        <small class="text-muted">Type a skill and press Enter to add. Click the × to remove.</small>
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
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Email cannot be modified.</small>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Global skills array
let globalSkills = @json($skillsList);

function renderSkills() {
    const wrap = document.getElementById('skillsWrap');
    const input = document.getElementById('skills_text');
    const hidden = document.getElementById('skills_hidden');
    
    if (!wrap || !input) return;
    
    // Remove all existing skill tags
    const tags = wrap.querySelectorAll('.skill-tag');
    tags.forEach(tag => tag.remove());
    
    // Add skill tags
    if (globalSkills && globalSkills.length > 0) {
        globalSkills.forEach((skill, index) => {
            if (skill && skill.trim() !== '') {
                const tag = document.createElement('span');
                tag.className = 'skill-tag badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3';
                tag.innerHTML = `${escapeHtml(skill)} <button type="button" class="btn-close" onclick="confirmRemoveSkill('${escapeHtml(skill)}', ${index})"></button>`;
                wrap.insertBefore(tag, input);
            }
        });
    }
    
    // Update hidden field
    if (hidden) {
        hidden.value = JSON.stringify(globalSkills);
    }
}

function confirmRemoveSkill(skillName, index) {
    Swal.fire({
        title: 'Remove Skill?',
        text: `Remove "${skillName}" from your skills?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel',
        toast: false,
        position: 'center',
        background: 'rgba(255, 255, 255, 0.95)',
        backdrop: 'rgba(0,0,0,0.2)'
    }).then((result) => {
        if (result.isConfirmed) {
            removeSkill(index, skillName);
        }
    });
}

function removeSkill(index, skillName) {
    globalSkills.splice(index, 1);
    renderSkills();
    
    // Center toast message
    Swal.fire({
        icon: 'success',
        title: 'Removed!',
        text: `"${skillName}" has been removed from your skills.`,
        toast: false,
        position: 'center',
        showConfirmButton: true,
        confirmButtonColor: '#2b9a82',
        confirmButtonText: 'OK',
        timer: 2500,
        timerProgressBar: true,
        background: 'rgba(255, 255, 255, 0.95)',
        backdrop: 'rgba(0,0,0,0.1)'
    });
}

function addSkill(skill) {
    if (skill && !globalSkills.includes(skill)) {
        globalSkills.push(skill);
        renderSkills();
        
        // Center toast message for add
        Swal.fire({
            icon: 'success',
            title: 'Added!',
            text: `"${skill}" has been added to your skills.`,
            toast: false,
            position: 'center',
            showConfirmButton: true,
            confirmButtonColor: '#2b9a82',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true,
            background: 'rgba(255, 255, 255, 0.95)'
        });
        return true;
    } else if (skill && globalSkills.includes(skill)) {
        // Center warning for duplicate
        Swal.fire({
            icon: 'warning',
            title: 'Already Exists!',
            text: `"${skill}" is already in your skills list.`,
            toast: false,
            position: 'center',
            showConfirmButton: true,
            confirmButtonColor: '#2b9a82',
            confirmButtonText: 'OK',
            timer: 2000,
            timerProgressBar: true,
            background: 'rgba(255, 255, 255, 0.95)'
        });
        return false;
    }
    return false;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener("DOMContentLoaded", function () {
    // Initial render
    renderSkills();
    
    // Handle Enter key for adding skills
    const input = document.getElementById("skills_text");
    if (input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                
                let newSkill = this.value.trim();
                if (newSkill) {
                    addSkill(newSkill);
                    this.value = '';
                }
                return false;
            }
        });
    }
    
    // Form submit - ensure hidden field is updated
    const form = document.getElementById('profileForm');
    if (form) {
        form.addEventListener('submit', function() {
            const hidden = document.getElementById('skills_hidden');
            if (hidden) {
                hidden.value = JSON.stringify(globalSkills);
            }
        });
    }

    /* --- Character Count --- */
    const bio = document.getElementById("bioTextarea");
    const cnt = document.getElementById("bioCount");
    if (bio && cnt) {
        bio.addEventListener('input', () => cnt.textContent = bio.value.length);
        cnt.textContent = bio.value.length;
    }
});

function checkStrength(v) {
    const fill = document.getElementById('strengthFill');
    let strength = 0;
    if (v.length > 5) strength += 25;
    if (/[A-Z]/.test(v)) strength += 25;
    if (/[0-9]/.test(v)) strength += 25;
    if (/[^A-Za-z0-9]/.test(v)) strength += 25;
    
    if (fill) {
        fill.style.width = strength + '%';
        fill.className = 'progress-bar ' + (strength < 50 ? 'bg-danger' : (strength < 100 ? 'bg-warning' : 'bg-success'));
    }
}

function checkMatch() {
    const np = document.getElementById('new_password');
    const cp = document.getElementById('new_password_confirmation');
    const msg = document.getElementById('matchMsg');
    if (!np || !cp || !msg) return;
    
    if (!cp.value) {
        msg.textContent = '';
        return;
    }
    msg.textContent = (np.value === cp.value) ? 'Passwords match' : 'Passwords do not match';
    msg.className = 'mt-1 d-block small ' + (np.value === cp.value ? 'text-success' : 'text-danger');
}

function previewModal(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('modalPreview');
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection