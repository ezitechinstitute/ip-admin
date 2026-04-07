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
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --ink:       #0b0f1a;
    --ink-muted: #3d4460;
    --surface:   #f4f5fa;
    --card-bg:   #ffffff;
    --accent1:   #5b5ef4;
    --accent2:   #e84393;
    --accent3:   #00c896;
    --accent4:   #f7a440;
    --border:    rgba(91,94,244,.13);
    --border-soft: #e8eaf6;
    --shadow-sm: 0 2px 12px rgba(11,15,26,.06);
    --shadow-md: 0 8px 32px rgba(11,15,26,.10);
    --shadow-lg: 0 20px 60px rgba(11,15,26,.14);
    --radius:    1.1rem;
    --radius-sm: .6rem;
}

body { font-family: 'DM Sans', sans-serif; background: var(--surface); color: var(--ink); }
h1,h2,h3,h4,h5,h6 { font-family: 'Syne', sans-serif; }

/* ─── page header ─── */
.page-header {
    background: var(--ink);
    border-radius: var(--radius);
    padding: 1.75rem 2rem;
    margin-bottom: 1.75rem;
    position: relative;
    overflow: hidden;
    isolation: isolate;
}
.page-header::before {
    content: '';
    position: absolute;
    width: 300px; height: 300px;
    background: radial-gradient(circle, #5b5ef4 0%, transparent 70%);
    top: -130px; left: -80px;
    filter: blur(60px);
    opacity: .65;
    z-index: -1;
}
.page-header::after {
    content: '';
    position: absolute;
    width: 250px; height: 250px;
    background: radial-gradient(circle, #e84393 0%, transparent 70%);
    bottom: -100px; right: -40px;
    filter: blur(60px);
    opacity: .45;
    z-index: -1;
}
.page-header-inner { position: relative; z-index: 1; }
.page-header h4 { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0 0 .25rem; letter-spacing: -.02em; }
.page-header p  { font-size: .8rem; color: rgba(255,255,255,.55); margin: 0; }

/* back button */
.btn-back {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .38rem .9rem;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: var(--radius-sm);
    color: rgba(255,255,255,.8);
    font-size: .75rem; font-weight: 600;
    text-decoration: none;
    transition: background .25s, transform .2s;
    backdrop-filter: blur(6px);
}
.btn-back:hover { background: rgba(255,255,255,.16); transform: translateX(-2px); color: #fff; }

/* ─── section card ─── */
.section-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.25rem;
    overflow: hidden;
    transition: box-shadow .3s;
}
.section-card:hover { box-shadow: var(--shadow-md); }

.section-card-header {
    padding: 1.2rem 1.5rem .9rem;
    border-bottom: 1px solid var(--border-soft);
    display: flex; align-items: center; gap: .6rem;
}
.section-card-header-icon {
    width: 34px; height: 34px;
    border-radius: .55rem;
    background: linear-gradient(135deg, var(--accent1), #8486f8);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: #fff; flex-shrink: 0;
}
.section-card-header h5 { font-size: .92rem; font-weight: 700; color: var(--ink); margin: 0; }
.section-card-header p  { font-size: .68rem; color: var(--ink-muted); margin: 0; }
.section-card-body { padding: 1.4rem 1.5rem; }

/* ─── avatar section ─── */
.avatar-section {
    display: flex; flex-direction: column; align-items: center;
    padding: 2rem 1.5rem;
    border-right: 1px solid var(--border-soft);
    background: linear-gradient(160deg, #f8f9fe 0%, #fff 100%);
    position: relative;
}
@media (max-width: 767px) {
    .avatar-section {
        border-right: none;
        border-bottom: 1px solid var(--border-soft);
        padding: 1.5rem;
    }
}
.avatar-ring {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
}
.avatar-ring::before {
    content: '';
    position: absolute; inset: -5px;
    border-radius: 50%;
    background: conic-gradient(var(--accent1), var(--accent2), var(--accent3), var(--accent1));
    z-index: 0;
    animation: spin 6s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.avatar-ring::after {
    content: '';
    position: absolute; inset: -2px;
    background: #f8f9fe;
    border-radius: 50%;
    z-index: 1;
}
.avatar-img {
    width: 120px; height: 120px;
    border-radius: 50%;
    object-fit: cover;
    position: relative;
    z-index: 2;
    display: block;
    border: 3px solid #fff;
}
.avatar-name { font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: .2rem; text-align: center; }
.avatar-id   { font-size: .7rem; color: var(--ink-muted); text-align: center; margin-bottom: 1rem; }

.btn-change-photo {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .4rem 1rem;
    background: linear-gradient(135deg, var(--accent1), #8486f8);
    color: #fff;
    border: none;
    border-radius: 2rem;
    font-size: .72rem; font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(91,94,244,.35);
    transition: transform .25s, box-shadow .25s;
}
.btn-change-photo:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(91,94,244,.4); }

.avatar-tip {
    font-size: .62rem;
    color: var(--ink-muted);
    margin-top: .6rem;
    text-align: center;
    line-height: 1.5;
}

/* ─── form fields ─── */
.form-label-custom {
    font-size: .72rem;
    font-weight: 600;
    color: var(--ink-muted);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: .45rem;
    display: block;
}
.form-label-custom .req { color: var(--accent2); margin-left: 2px; }

.field-wrap { position: relative; }
.field-icon {
    position: absolute;
    left: .9rem; top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #aab0cc;
    pointer-events: none;
    transition: color .25s;
    z-index: 2;
}
.field-icon-top { top: 1rem; transform: none; }

.form-control-custom {
    width: 100%;
    padding: .65rem .9rem .65rem 2.5rem;
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-sm);
    font-size: .82rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
    background: #fafbff;
    transition: border-color .25s, box-shadow .25s, background .25s;
    outline: none;
}
.form-control-custom:focus {
    border-color: var(--accent1);
    box-shadow: 0 0 0 3px rgba(91,94,244,.12);
    background: #fff;
}
.form-control-custom:focus + .field-icon,
.field-wrap:focus-within .field-icon { color: var(--accent1); }

.form-control-custom.is-invalid { border-color: var(--accent2) !important; }
.form-control-custom.is-invalid:focus { box-shadow: 0 0 0 3px rgba(232,67,147,.12); }

textarea.form-control-custom { resize: vertical; padding-top: .75rem; line-height: 1.65; }

.invalid-feedback-custom { font-size: .68rem; color: var(--accent2); margin-top: .3rem; display: flex; align-items: center; gap: .3rem; }

.field-hint { font-size: .65rem; color: var(--ink-muted); margin-top: .35rem; }

/* no-icon variant */
.form-control-custom.no-icon { padding-left: .9rem; }

/* ─── password field toggle ─── */
.pass-wrap { position: relative; }
.pass-wrap .field-icon-right {
    position: absolute;
    right: .9rem; top: 50%;
    transform: translateY(-50%);
    font-size: 16px;
    color: #aab0cc;
    cursor: pointer;
    z-index: 3;
    transition: color .2s;
}
.pass-wrap .field-icon-right:hover { color: var(--accent1); }

/* ─── divider ─── */
.section-divider {
    display: flex; align-items: center; gap: .75rem;
    margin: 1.75rem 0 1.25rem;
}
.section-divider span {
    font-size: .7rem;
    font-weight: 700;
    color: var(--ink-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
    white-space: nowrap;
}
.section-divider::before,
.section-divider::after {
    content: ''; flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border-soft), transparent);
}

/* ─── skills tagger ─── */
.skills-input-wrap {
    border: 1.5px solid var(--border-soft);
    border-radius: var(--radius-sm);
    padding: .55rem .75rem;
    background: #fafbff;
    transition: border-color .25s, box-shadow .25s;
    min-height: 52px;
    display: flex; flex-wrap: wrap; align-items: center; gap: .4rem;
    cursor: text;
}
.skills-input-wrap:focus-within {
    border-color: var(--accent1);
    box-shadow: 0 0 0 3px rgba(91,94,244,.12);
    background: #fff;
}
.skills-input-wrap input {
    border: none;
    outline: none;
    background: transparent;
    font-size: .8rem;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink);
    flex: 1;
    min-width: 120px;
    padding: .2rem .2rem;
}
.skill-tag {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .28rem .65rem;
    background: linear-gradient(135deg, rgba(91,94,244,.1), rgba(132,134,248,.08));
    border: 1px solid rgba(91,94,244,.25);
    border-radius: 2rem;
    font-size: .7rem;
    font-weight: 600;
    color: var(--accent1);
    transition: all .25s;
    white-space: nowrap;
}
.skill-tag:hover { background: linear-gradient(135deg, var(--accent1), #8486f8); color: #fff; border-color: transparent; }
.skill-tag .rm {
    cursor: pointer;
    font-size: .75rem;
    line-height: 1;
    opacity: .6;
    transition: opacity .2s;
    padding: 0 1px;
}
.skill-tag:hover .rm { opacity: 1; }
.skills-hint { font-size: .63rem; color: var(--ink-muted); margin-top: .4rem; }

/* ─── password strength ─── */
.strength-bar {
    height: 4px;
    border-radius: 100px;
    background: #eef0fa;
    margin-top: .5rem;
    overflow: hidden;
}
.strength-fill {
    height: 100%;
    border-radius: 100px;
    width: 0;
    transition: width .4s ease, background .4s ease;
}

/* ─── action bar ─── */
.action-bar {
    background: var(--card-bg);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.btn-save {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .6rem 1.75rem;
    background: linear-gradient(135deg, var(--accent1), #8486f8);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: .82rem;
    font-weight: 700;
    font-family: 'Syne', sans-serif;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(91,94,244,.38);
    transition: transform .25s, box-shadow .25s;
    letter-spacing: .01em;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(91,94,244,.45); color: #fff; }

.btn-cancel {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .6rem 1.4rem;
    background: #f1f2fd;
    color: var(--ink-muted);
    border: 1px solid var(--border-soft);
    border-radius: var(--radius-sm);
    font-size: .8rem; font-weight: 600;
    text-decoration: none;
    transition: background .25s, color .25s, transform .2s;
}
.btn-cancel:hover { background: #e8eaf6; color: var(--ink); transform: translateX(-2px); }

/* ─── entrance anim ─── */
@keyframes fadeUp { from { opacity:0; transform: translateY(18px); } to { opacity:1; transform: translateY(0); } }
.fade-up { animation: fadeUp .5s ease both; }
.d1 { animation-delay:.06s; } .d2 { animation-delay:.12s; }
.d3 { animation-delay:.18s; } .d4 { animation-delay:.24s; }
.d5 { animation-delay:.30s; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ══════════ PAGE HEADER ══════════ --}}
    <div class="page-header fade-up">
        <div class="page-header-inner">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h4><i class="ti ti-edit me-2" style="color:var(--accent1);"></i>Edit Profile</h4>
                    <p>Update your personal information, skills and security settings</p>
                </div>
                <a href="{{ route('intern.profile') }}" class="btn-back">
                    <i class="ti ti-arrow-left"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>

    <form id="profileForm" action="{{ route('intern.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ══════════ PERSONAL INFO CARD ══════════ --}}
        <div class="section-card fade-up d1">
            <div class="section-card-header">
                <div class="section-card-header-icon"><i class="ti ti-user"></i></div>
                <div>
                    <h5>Personal Information</h5>
                    <p>Your basic details and contact info</p>
                </div>
            </div>

            <div class="row g-0">
                {{-- Avatar Sidebar --}}
                <div class="col-md-3">
                    <div class="avatar-section h-100">
                        <div class="avatar-ring">
                            <img src="{{ $profileImage }}" alt="Profile" class="avatar-img" id="liveAvatar">
                        </div>
                        <div class="avatar-name">{{ $intern->name }}</div>
                        <div class="avatar-id">{{ $intern->eti_id }}</div>
                        <button type="button" class="btn-change-photo"
                                data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                            <i class="ti ti-camera"></i> Change Photo
                        </button>
                        <p class="avatar-tip">JPG, PNG or GIF<br>Max 2MB · 400×400px</p>
                    </div>
                </div>

                {{-- Fields --}}
                <div class="col-md-9">
                    <div class="section-card-body">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name <span class="req">*</span></label>
                                <div class="field-wrap">
                                    <i class="ti ti-user field-icon"></i>
                                    <input type="text" name="name"
                                           class="form-control-custom @error('name') is-invalid @enderror"
                                           value="{{ old('name', $intern->name) }}"
                                           placeholder="Your full name" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback-custom"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address</label>
                                <div class="field-wrap">
                                    <i class="ti ti-mail field-icon"></i>
                                    <input type="email" class="form-control-custom no-icon"
                                           style="padding-left:2.5rem;"
                                           value="{{ $intern->email }}" disabled
                                           title="Email cannot be changed">
                                </div>
                                <p class="field-hint"><i class="ti ti-lock" style="font-size:10px;"></i> Email address cannot be changed</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <div class="field-wrap">
                                    <i class="ti ti-phone field-icon"></i>
                                    <input type="text" name="phone"
                                           class="form-control-custom @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $intern->phone) }}"
                                           placeholder="+92 300 0000000">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback-custom"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-custom">City / Location</label>
                                <div class="field-wrap">
                                    <i class="ti ti-map-pin field-icon"></i>
                                    <input type="text" name="city"
                                           class="form-control-custom @error('city') is-invalid @enderror"
                                           value="{{ old('city', $intern->city) }}"
                                           placeholder="e.g. Lahore, Pakistan">
                                </div>
                                @error('city')
                                    <div class="invalid-feedback-custom"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">University / Institution</label>
                                <div class="field-wrap">
                                    <i class="ti ti-school field-icon"></i>
                                    <input type="text" name="university"
                                           class="form-control-custom @error('university') is-invalid @enderror"
                                           value="{{ old('university', $intern->university) }}"
                                           placeholder="Your university or institution name">
                                </div>
                                @error('university')
                                    <div class="invalid-feedback-custom"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-custom">Bio / Professional Summary</label>
                                <div class="field-wrap">
                                    <i class="ti ti-pencil field-icon field-icon-top" style="top:.85rem;"></i>
                                    <textarea name="bio" rows="4"
                                              class="form-control-custom @error('bio') is-invalid @enderror"
                                              placeholder="Tell us about yourself, your goals, and what you're passionate about..."
                                              maxlength="1000"
                                              id="bioTextarea">{{ old('bio', $intern->bio) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <p class="field-hint mb-0">Displayed on your public profile.</p>
                                    <p class="field-hint mb-0"><span id="bioCount">0</span>/1000</p>
                                </div>
                                @error('bio')
                                    <div class="invalid-feedback-custom"><i class="ti ti-alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════ SKILLS CARD ══════════ --}}
        <div class="section-card fade-up d2">
            <div class="section-card-header">
                <div class="section-card-header-icon" style="background: linear-gradient(135deg,#00c896,#00a87b);"><i class="ti ti-code"></i></div>
                <div>
                    <h5>Skills &amp; Technologies</h5>
                    <p>Showcase your technical expertise</p>
                </div>
            </div>
            <div class="section-card-body">
                <label class="form-label-custom">Your Skills</label>
                <div class="skills-input-wrap" id="skillsWrap">
                    <input type="text" id="skills_text" placeholder="Type a skill and press Enter…" autocomplete="off">
                </div>
                <input type="hidden" name="skills" id="skills_hidden">
                <p class="skills-hint"><i class="ti ti-keyboard" style="font-size:11px;"></i> Press <kbd style="font-size:.6rem;padding:.1rem .35rem;border-radius:.3rem;background:#f1f2fd;border:1px solid #dde0f5;">Enter</kbd> or <kbd style="font-size:.6rem;padding:.1rem .35rem;border-radius:.3rem;background:#f1f2fd;border:1px solid #dde0f5;">,</kbd> to add · Click tag to remove</p>
            </div>
        </div>

        {{-- ══════════ SECURITY CARD ══════════ --}}
        <div class="section-card fade-up d3">
            <div class="section-card-header">
                <div class="section-card-header-icon" style="background: linear-gradient(135deg,#e84393,#c0165b);"><i class="ti ti-shield-lock"></i></div>
                <div>
                    <h5>Change Password</h5>
                    <p>Leave blank if you don't want to change it</p>
                </div>
            </div>
            <div class="section-card-body">
                <div class="row g-4">

                    <div class="col-md-4">
                        <label class="form-label-custom">Current Password</label>
                        <div class="field-wrap pass-wrap">
                            <i class="ti ti-lock field-icon"></i>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control-custom" placeholder="Enter current password">
                            <i class="ti ti-eye field-icon-right" onclick="togglePass('current_password', this)"></i>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">New Password</label>
                        <div class="field-wrap pass-wrap">
                            <i class="ti ti-lock-open field-icon"></i>
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control-custom" placeholder="Enter new password"
                                   oninput="checkStrength(this.value); checkMatch()">
                            <i class="ti ti-eye field-icon-right" onclick="togglePass('new_password', this)"></i>
                        </div>
                        <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                        <p class="field-hint mt-1" id="strengthLabel"></p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Confirm New Password</label>
                        <div class="field-wrap pass-wrap">
                            <i class="ti ti-lock-check field-icon"></i>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="form-control-custom" placeholder="Repeat new password"
                                   oninput="checkMatch()">
                            <i class="ti ti-eye field-icon-right" onclick="togglePass('new_password_confirmation', this)"></i>
                        </div>
                        <p class="field-hint mt-1" id="matchMsg"></p>
                    </div>

                </div>
            </div>
        </div>

        {{-- ══════════ ACTION BAR ══════════ --}}
        <div class="action-bar fade-up d4">
            <a href="{{ route('intern.profile') }}" class="btn-cancel">
                <i class="ti ti-x"></i> Discard Changes
            </a>
            <button type="submit" class="btn-save">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
        </div>

    </form>
</div>

{{-- ══════════ UPLOAD IMAGE MODAL ══════════ --}}
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:1.1rem;overflow:hidden;box-shadow:var(--shadow-lg);">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0" style="background:var(--ink);padding:1.2rem 1.4rem .8rem;">
                    <h6 class="modal-title text-white" style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;">
                        <i class="ti ti-camera me-2" style="color:var(--accent1);"></i>Update Profile Picture
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <img id="modalPreview" src="{{ $profileImage }}" alt="Preview"
                             style="width:100px;height:100px;object-fit:cover;border-radius:50%;
                                    border:3px solid var(--accent1);box-shadow:0 0 0 5px rgba(91,94,244,.15);">
                    </div>
                    <label class="form-label-custom">Choose New Image</label>
                    <input type="file" name="profile_image" class="form-control form-control-sm"
                           accept="image/*" onchange="previewModal(this)" required
                           style="border-color:rgba(91,94,244,.25);font-size:.78rem;">
                    <p class="field-hint mt-2">Max 2MB · Square recommended (400×400px) · JPG, PNG, GIF</p>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-sm btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm px-4 text-white border-0"
                            style="background:var(--accent1);box-shadow:0 4px 14px rgba(91,94,244,.35);">
                        <i class="ti ti-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ── skills tagger ── */
    const wrap   = document.getElementById("skillsWrap");
    const input  = document.getElementById("skills_text");
    const hidden = document.getElementById("skills_hidden");
    let skills   = @json($skillsArray);

    function renderTags() {
        wrap.querySelectorAll('.skill-tag').forEach(t => t.remove());
        skills.forEach((s, i) => {
            const tag = document.createElement('span');
            tag.className = 'skill-tag';
            tag.innerHTML = `${escHtml(s)}<span class="rm" data-i="${i}">×</span>`;
            wrap.insertBefore(tag, input);
        });
        hidden.value = JSON.stringify(skills);
    }

    function addSkill(val) {
        val = val.trim().replace(/,$/, '').trim();
        if (val && !skills.includes(val) && !val.includes('@')) {
            skills.push(val);
            renderTags();
        }
        input.value = '';
    }

    function escHtml(t) {
        const d = document.createElement('div');
        d.textContent = t; return d.innerHTML;
    }

    input.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); addSkill(input.value); }
        if (e.key === 'Backspace' && !input.value && skills.length) {
            skills.pop(); renderTags();
        }
    });
    wrap.addEventListener('click', e => {
        if (e.target.dataset.i !== undefined) { skills.splice(+e.target.dataset.i, 1); renderTags(); }
        else input.focus();
    });
    document.getElementById("profileForm").addEventListener("submit", () => {
        if (input.value.trim()) addSkill(input.value);
        hidden.value = JSON.stringify(skills);
    });
    renderTags();

    /* ── bio char count ── */
    const bio = document.getElementById("bioTextarea");
    const cnt = document.getElementById("bioCount");
    function updateCount() { cnt.textContent = bio.value.length; }
    bio.addEventListener('input', updateCount);
    updateCount();
});

/* ── password toggle ── */
function togglePass(id, icon) {
    const f = document.getElementById(id);
    if (f.type === 'password') { f.type = 'text'; icon.classList.replace('ti-eye','ti-eye-off'); }
    else { f.type = 'password'; icon.classList.replace('ti-eye-off','ti-eye'); }
}

/* ── password strength ── */
function checkStrength(v) {
    const fill  = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (v.length >= 8)           score++;
    if (/[A-Z]/.test(v))         score++;
    if (/[0-9]/.test(v))         score++;
    if (/[^A-Za-z0-9]/.test(v))  score++;
    const levels = [
        { w:'0%',   c:'transparent', t:'' },
        { w:'25%',  c:'#e84393',     t:'Weak' },
        { w:'50%',  c:'#f7a440',     t:'Fair' },
        { w:'75%',  c:'#3b82f6',     t:'Good' },
        { w:'100%', c:'#00c896',     t:'Strong' },
    ];
    const l = levels[score];
    fill.style.width = l.w; fill.style.background = l.c;
    label.textContent = l.t; label.style.color = l.c;
}

/* ── password match ── */
function checkMatch() {
    const np = document.getElementById('new_password').value;
    const cp = document.getElementById('new_password_confirmation').value;
    const msg = document.getElementById('matchMsg');
    if (!cp) { msg.textContent = ''; return; }
    if (np === cp) { msg.textContent = '✓ Passwords match'; msg.style.color = '#00c896'; }
    else           { msg.textContent = '✗ Passwords do not match'; msg.style.color = '#e84393'; }
}

/* ── modal image preview ── */
function previewModal(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => document.getElementById('modalPreview').src = e.target.result;
        r.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection