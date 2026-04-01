@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;

$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

// Get skills from database
$skillsArray = $skills->toArray();

// Debug - See what skills are coming from controller
echo "<!-- DEBUG: Skills from controller: " . json_encode($skillsArray) . " -->";

// Filter out any email addresses
$skillsArray = array_filter($skillsArray, function($skill) {
    return !filter_var($skill, FILTER_VALIDATE_EMAIL);
});

$skillsString = implode(', ', $skillsArray);
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Edit Profile')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Profile</h4>
                <p class="text-muted mb-0">Update your personal information and professional details</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image Section -->
                    <div class="col-md-3 text-center mb-4 mb-md-0">
                        <img src="{{ $profileImage }}" 
                             alt="Profile" 
                             class="rounded-circle mb-3"
                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                                <i class="ti ti-camera me-1"></i> Change Photo
                            </button>
                        </div>
                    </div>
                    
                    <!-- Edit Form -->
                    <div class="col-md-9">
                        <form id="profileForm" action="{{ route('intern.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <h5 class="mb-3">Personal Information</h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $intern->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $intern->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" 
                                           value="{{ old('city', $intern->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">University / Institution</label>
                                    <input type="text" name="university" class="form-control @error('university') is-invalid @enderror" 
                                           value="{{ old('university', $intern->university) }}">
                                    @error('university')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Bio / Professional Summary</label>
                                    <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="4" 
                                              placeholder="Tell us about yourself, your skills, and what you're passionate about...">{{ old('bio', $intern->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Maximum 1000 characters. This will be displayed on your public profile.</small>
                                </div>
                            </div>
                            
                            <hr class="my-5">
                            
                            <h5 class="mb-3">Skills & Technologies</h5>
                            <div class="mb-3">
                                <label class="form-label">Skills</label>
                                
                                <!-- Visible input -->
                                <input type="text"
                                       id="skills_text"
                                       class="form-control"
                                       placeholder="Type skill and press Enter">
                                
                                <!-- Hidden input (actual data for submission) -->
                                <input type="hidden" name="skills" id="skills_hidden">
                                
                                <div id="skills_tags" class="mt-2"></div>
                                
                                <small class="text-muted">
                                    Press Enter to add skill. Click × to remove.
                                </small>
                            </div>
                            
                            <hr class="my-5">
                            
                            <h5 class="mb-3">Change Password</h5>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" id="current_password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" id="new_password">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" id="new_password_confirmation">
                                    <small id="password_match_error" class="text-danger" style="display: none;">Passwords do not match</small>
                                </div>
                            </div>
                            <small class="text-muted">Leave password fields empty if you don't want to change it.</small>
                            
                            <div class="mt-5 d-flex justify-content-between">
                                <a href="{{ route('intern.profile') }}" class="btn btn-secondary">
                                    <i class="ti ti-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Image Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Choose Image</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Max 2MB. Recommended: Square image (400x400px). Supported formats: JPG, PNG, GIF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const input = document.getElementById("skills_text");
    const hidden = document.getElementById("skills_hidden");
    const container = document.getElementById("skills_tags");

    // Get existing skills from PHP (filtered, no emails)
    let skills = @json($skillsArray);
    
    // Render existing skills as tags
    function renderTags() {
        container.innerHTML = "";
        skills.forEach((skill, index) => {
            let tag = document.createElement("span");
            tag.className = "badge bg-primary me-2 mb-2";
            tag.style.fontSize = "14px";
            tag.style.padding = "6px 12px";
            tag.style.cursor = "default";

            tag.innerHTML = `
                ${escapeHtml(skill)}
                <span style="cursor:pointer; margin-left:8px; font-weight:bold;" data-index="${index}">&times;</span>
            `;

            container.appendChild(tag);
        });

        hidden.value = JSON.stringify(skills);
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Add skill on Enter
    input.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();

            let value = input.value.trim();

            if (value && !skills.includes(value)) {
                // Check if it's an email - don't add
                if (value.includes('@')) {
                    alert('Email addresses cannot be added as skills');
                    input.value = '';
                    return;
                }
                skills.push(value);
                input.value = "";
                renderTags();
            }
        }
    });

    // Remove skill
    container.addEventListener("click", function (e) {
        if (e.target.dataset.index !== undefined) {
            skills.splice(parseInt(e.target.dataset.index), 1);
            renderTags();
        }
    });
    
    // On form submit - ensure all skills are saved
    document.getElementById("profileForm").addEventListener("submit", function () {
        // Add any pending text in input field before submit
        let currentInput = input.value.trim();
        if (currentInput && !skills.includes(currentInput) && !currentInput.includes('@')) {
            skills.push(currentInput);
        }
        hidden.value = JSON.stringify(skills);
        return true;
    });
    
    // Initial render
    renderTags();
    
    // Password match validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('new_password_confirmation');
    const errorMsg = document.getElementById('password_match_error');
    
    if (newPassword && confirmPassword) {
        newPassword.addEventListener('keyup', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
    }
    
    function validatePassword() {
        if (newPassword.value != confirmPassword.value) {
            errorMsg.style.display = 'block';
        } else {
            errorMsg.style.display = 'none';
        }
    }
});
</script>

<style>
.badge.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    transition: all 0.3s ease;
}
.badge.bg-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}
</style>

@endsection