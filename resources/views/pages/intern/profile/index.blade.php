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

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
])
@endsection

@section('page-style')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --ink:        #0b0f1a;
    --ink-muted:  #3d4460;
    --surface:    #f4f5fa;
    --card-bg:    #ffffff;
    --accent1:    #5b5ef4;
    --accent2:    #e84393;
    --accent3:    #00c896;
    --accent4:    #f7a440;
    --border:     rgba(91,94,244,.12);
    --shadow-sm:  0 2px 12px rgba(11,15,26,.06);
    --shadow-md:  0 8px 32px rgba(11,15,26,.10);
    --shadow-lg:  0 20px 60px rgba(11,15,26,.14);
    --radius:     1.1rem;
    --radius-sm:  .6rem;
}

/* ─── base ─── */
body { font-family: 'DM Sans', sans-serif; background: var(--surface); color: var(--ink); }
h1,h2,h3,h4,h5,h6 { font-family: 'Syne', sans-serif; }

/* ─── hero banner ─── */
.hero-banner {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    padding: 2.5rem 2rem 1.75rem;
    margin-bottom: 1.75rem;
    background: var(--ink);
    isolation: isolate;
}

/* decorative blobs */
.hero-banner::before,
.hero-banner::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    z-index: -1;
}
.hero-banner::before {
    width: 380px; height: 380px;
    background: radial-gradient(circle, #5b5ef4 0%, transparent 70%);
    top: -120px; left: -100px;
    opacity: .7;
}
.hero-banner::after {
    width: 320px; height: 320px;
    background: radial-gradient(circle, #e84393 0%, transparent 70%);
    bottom: -100px; right: -60px;
    opacity: .5;
}

/* noise grain overlay */
.hero-banner .grain {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
    z-index: -1;
}

/* hero avatar */
.hero-avatar {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,.2);
    box-shadow: 0 0 0 6px rgba(91,94,244,.25), var(--shadow-md);
    transition: transform .35s ease, box-shadow .35s ease;
}
.hero-avatar:hover {
    transform: scale(1.06);
    box-shadow: 0 0 0 8px rgba(91,94,244,.35), var(--shadow-lg);
}

.avatar-wrap { position: relative; display: inline-block; }
.avatar-cam-btn {
    position: absolute; bottom: 2px; right: 2px;
    width: 26px; height: 26px;
    background: var(--accent1);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 11px;
    border: 2px solid #fff;
    cursor: pointer;
    transition: transform .25s, background .25s;
}
.avatar-cam-btn:hover { background: var(--accent2); transform: scale(1.15); }

.hero-name {
    font-size: 1.7rem;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
    line-height: 1.1;
    margin-bottom: .35rem;
}

/* pill badges in hero */
.hero-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .28rem .75rem;
    border-radius: 2rem;
    font-size: .68rem;
    font-weight: 600;
    letter-spacing: .02em;
    font-family: 'DM Sans', sans-serif;
    backdrop-filter: blur(8px);
}
.hp-id       { background: rgba(255,255,255,.13); color: #fff; border: 1px solid rgba(255,255,255,.2); }
.hp-tech     { background: rgba(91,94,244,.25);   color: #b4b6fc; border: 1px solid rgba(91,94,244,.35); }
.hp-active   { background: rgba(0,200,150,.2);    color: #00c896; border: 1px solid rgba(0,200,150,.35); }

/* hero cta buttons */
.btn-hero-primary {
    background: var(--accent1);
    color: #fff;
    border: none;
    padding: .45rem 1.1rem;
    border-radius: var(--radius-sm);
    font-size: .78rem;
    font-weight: 600;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background .25s, transform .2s, box-shadow .25s;
    box-shadow: 0 4px 14px rgba(91,94,244,.35);
}
.btn-hero-primary:hover {
    background: #4547e0;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(91,94,244,.45);
    color: #fff;
}
.btn-hero-outline {
    background: rgba(255,255,255,.07);
    color: rgba(255,255,255,.85);
    border: 1px solid rgba(255,255,255,.18);
    padding: .45rem 1.1rem;
    border-radius: var(--radius-sm);
    font-size: .78rem;
    font-weight: 500;
    display: inline-flex; align-items: center; gap: .4rem;
    transition: background .25s, transform .2s;
    backdrop-filter: blur(6px);
}
.btn-hero-outline:hover {
    background: rgba(255,255,255,.15);
    transform: translateY(-2px);
    color: #fff;
}

/* member since chip */
.member-chip {
    display: inline-flex; align-items: center; gap: .6rem;
    padding: .5rem 1rem;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: .75rem;
    backdrop-filter: blur(10px);
    transition: background .3s, transform .3s;
}
.member-chip:hover { background: rgba(255,255,255,.13); transform: translateY(-2px); }
.member-chip-icon {
    width: 34px; height: 34px;
    border-radius: .5rem;
    background: rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
}
.member-chip-icon i { font-size: 17px; color: #fff; }
.member-chip small { font-size: .6rem; color: rgba(255,255,255,.55); display: block; }
.member-chip strong { font-size: .85rem; color: #fff; font-weight: 700; }

/* ─── stat cards ─── */
.stat-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    padding: 1.25rem 1rem;
    text-align: center;
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    transition: transform .3s, box-shadow .3s;
    position: relative;
    overflow: hidden;
}
.stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 3px;
    background: var(--stat-accent, var(--accent1));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .35s ease;
}
.stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
.stat-card:hover::after { transform: scaleX(1); }

.stat-icon-wrap {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .75rem;
    font-size: 22px;
    color: #fff;
    background: var(--stat-accent, var(--accent1));
    box-shadow: 0 6px 18px color-mix(in srgb, var(--stat-accent, var(--accent1)) 40%, transparent);
}
.stat-num {
    font-family: 'Syne', sans-serif;
    font-size: 1.7rem;
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
    margin-bottom: .2rem;
}
.stat-label { font-size: .68rem; color: var(--ink-muted); font-weight: 500; letter-spacing: .03em; }

/* ─── info cards ─── */
.glass-card {
    background: var(--card-bg);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    padding: 1.4rem;
    margin-bottom: 1.25rem;
    position: relative;
    overflow: hidden;
    transition: transform .3s, box-shadow .3s, border-color .3s;
}
.glass-card.clickable { cursor: pointer; }
.glass-card.clickable:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
    border-color: rgba(91,94,244,.3);
}

/* left accent bar */
.glass-card::before {
    content: '';
    position: absolute;
    top: 1.5rem; left: 0;
    width: 3px; height: 0;
    background: linear-gradient(180deg, var(--accent1), var(--accent2));
    border-radius: 0 3px 3px 0;
    transition: height .35s ease;
}
.glass-card.clickable:hover::before { height: calc(100% - 3rem); }

/* hover edit label */
.edit-hint {
    position: absolute;
    top: 1rem; right: 1rem;
    font-size: .63rem;
    font-weight: 600;
    letter-spacing: .05em;
    color: var(--accent1);
    background: rgba(91,94,244,.08);
    border: 1px solid rgba(91,94,244,.2);
    border-radius: .4rem;
    padding: .2rem .55rem;
    display: flex; align-items: center; gap: .3rem;
    opacity: 0;
    transition: opacity .25s, background .25s;
    z-index: 2;
}
.glass-card.clickable:hover .edit-hint { opacity: 1; background: var(--accent1); color: #fff; border-color: var(--accent1); }

/* card section header */
.card-head {
    display: flex; align-items: center; gap: .55rem;
    margin-bottom: 1.1rem;
}
.card-head-icon {
    width: 30px; height: 30px;
    border-radius: .5rem;
    background: linear-gradient(135deg, var(--accent1), #8486f8);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    color: #fff;
    flex-shrink: 0;
}
.card-head h5 {
    font-size: .9rem;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}

/* ─── contact items ─── */
.contact-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .55rem .5rem;
    border-radius: .6rem;
    transition: background .25s, transform .2s;
    margin-bottom: .25rem;
}
.contact-row:hover { background: rgba(91,94,244,.05); transform: translateX(4px); }
.ci-icon {
    width: 36px; height: 36px;
    border-radius: .6rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    color: #fff;
}
.ci-email    { background: linear-gradient(135deg,#5b5ef4,#8486f8); }
.ci-phone    { background: linear-gradient(135deg,#00c896,#00a87b); }
.ci-location { background: linear-gradient(135deg,#3b82f6,#1d4ed8); }
.ci-edu      { background: linear-gradient(135deg,#f7a440,#e08000); }

.ci-label { font-size: .6rem; color: var(--ink-muted); display: block; margin-bottom: .1rem; text-transform: uppercase; letter-spacing: .04em; }
.ci-value { font-size: .8rem; font-weight: 500; color: var(--ink); }

/* ─── progress section ─── */
.prog-track {
    height: 8px;
    background: #eef0fa;
    border-radius: 100px;
    overflow: hidden;
}
.prog-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent1), var(--accent2));
    border-radius: 100px;
    position: relative;
    transition: width 1.2s cubic-bezier(.25,.8,.25,1);
}
.prog-fill::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.35) 50%, transparent 100%);
    background-size: 200% 100%;
    animation: shimmer 2.2s infinite;
}
@keyframes shimmer { from { background-position: -200% 0; } to { background-position: 200% 0; } }

.prog-alert {
    background: linear-gradient(135deg, rgba(91,94,244,.08), rgba(232,67,147,.06));
    border: 1px solid rgba(91,94,244,.15);
    border-radius: .65rem;
    padding: .65rem .9rem;
    font-size: .75rem;
    color: var(--accent1);
    display: flex; align-items: center; gap: .5rem;
}

/* ─── skill badges ─── */
.skill-tag {
    display: inline-flex; align-items: center;
    padding: .32rem .8rem;
    background: #f1f2fd;
    border: 1px solid rgba(91,94,244,.18);
    border-radius: 2rem;
    font-size: .7rem;
    font-weight: 600;
    color: var(--accent1);
    margin: .2rem;
    transition: all .25s;
    letter-spacing: .01em;
}
.skill-tag:hover {
    background: var(--accent1);
    color: #fff;
    border-color: var(--accent1);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(91,94,244,.3);
}

/* ─── bio ─── */
.bio-text {
    font-size: .82rem;
    line-height: 1.75;
    color: var(--ink-muted);
}

/* ─── empty state ─── */
.empty-box {
    text-align: center;
    padding: 1.25rem;
    background: #f8f9fe;
    border-radius: .75rem;
    border: 1.5px dashed rgba(91,94,244,.2);
}
.empty-box i { font-size: 1.4rem; color: rgba(91,94,244,.4); display: block; margin-bottom: .4rem; }
.empty-box p { font-size: .72rem; color: var(--ink-muted); margin: 0; }

/* ─── timeline ─── */
.tl-wrapper { position: relative; padding-left: 1.5rem; }
.tl-wrapper::before {
    content: '';
    position: absolute;
    left: .35rem;
    top: .5rem; bottom: .5rem;
    width: 2px;
    background: linear-gradient(180deg, var(--accent1), var(--accent4), #eef0fa);
    border-radius: 2px;
}
.tl-item { position: relative; padding-bottom: 1.2rem; }
.tl-item:last-child { padding-bottom: 0; }
.tl-dot {
    position: absolute;
    left: -1.5rem;
    top: .1rem;
    width: 13px; height: 13px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
}
.tl-dot.c1 { color: var(--accent3); background: var(--accent3); }
.tl-dot.c2 { color: var(--accent1); background: var(--accent1); }
.tl-dot.c3 { color: var(--accent4); background: var(--accent4); }
.tl-title { font-size: .78rem; font-weight: 700; color: var(--ink); margin-bottom: .15rem; }
.tl-date  { font-size: .62rem; color: var(--ink-muted); }
.tl-desc  { font-size: .72rem; color: var(--ink-muted); margin-top: .25rem; }

/* ─── responsive tweaks ─── */
@media (max-width: 576px) {
    .hero-name { font-size: 1.3rem; }
    .hero-banner { padding: 1.5rem 1rem 1.25rem; }
    .stat-num { font-size: 1.4rem; }
}

/* ─── entrance animations ─── */
@keyframes fadeUp {
    from { opacity:0; transform: translateY(20px); }
    to   { opacity:1; transform: translateY(0); }
}
.fade-up { animation: fadeUp .55s ease both; }
.delay-1 { animation-delay: .08s; }
.delay-2 { animation-delay: .16s; }
.delay-3 { animation-delay: .24s; }
.delay-4 { animation-delay: .32s; }
.delay-5 { animation-delay: .40s; }
.delay-6 { animation-delay: .48s; }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ══════════ HERO BANNER ══════════ --}}
    <div class="hero-banner fade-up">
        <div class="grain"></div>
        <div class="row align-items-center gy-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-wrap">
                        <img src="{{ $profileImage }}" alt="Profile" class="hero-avatar">
                        <button type="button" class="avatar-cam-btn border-0"
                                data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                            <i class="ti ti-camera"></i>
                        </button>
                    </div>
                    <div>
                        <div class="hero-name">{{ $intern->name }}</div>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <span class="hero-pill hp-id">
                                <i class="ti ti-fingerprint"></i> {{ $intern->eti_id }}
                            </span>
                            <span class="hero-pill hp-tech">
                                <i class="ti ti-code"></i> {{ $intern->int_technology ?? 'Not Assigned' }}
                            </span>
                            <span class="hero-pill hp-active">
                                <i class="ti ti-circle-check"></i> {{ $intern->int_status ?? 'Active' }}
                            </span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('intern.profile.edit') }}" class="btn-hero-primary">
                                <i class="ti ti-edit"></i> Edit Profile
                            </a>
                            <a href="{{ route('intern.profile.public', $intern->eti_id) }}" class="btn-hero-outline" target="_blank">
                                <i class="ti ti-world"></i> Public View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="member-chip d-inline-flex">
                    <div class="member-chip-icon">
                        <i class="ti ti-calendar-event"></i>
                    </div>
                    <div class="text-start">
                        <small>Member Since</small>
                        <strong class="d-block">{{ Carbon::parse($intern->created_at ?? $intern->start_date)->format('M Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ STAT CARDS ══════════ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 fade-up delay-1">
            <div class="stat-card" style="--stat-accent: var(--accent1);">
                <div class="stat-icon-wrap"><i class="ti ti-clipboard-list"></i></div>
                <div class="stat-num">{{ number_format($stats['total_tasks'] ?? 0) }}</div>
                <div class="stat-label">Total Tasks</div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-up delay-2">
            <div class="stat-card" style="--stat-accent: var(--accent3);">
                <div class="stat-icon-wrap"><i class="ti ti-circle-check"></i></div>
                <div class="stat-num">{{ number_format($stats['completed_tasks'] ?? 0) }}</div>
                <div class="stat-label">Completed Tasks</div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-up delay-3">
            <div class="stat-card" style="--stat-accent: #3b82f6;">
                <div class="stat-icon-wrap"><i class="ti ti-briefcase"></i></div>
                <div class="stat-num">{{ number_format($stats['total_projects'] ?? 0) }}</div>
                <div class="stat-label">Total Projects</div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-up delay-4">
            <div class="stat-card" style="--stat-accent: var(--accent4);">
                <div class="stat-icon-wrap"><i class="ti ti-rocket"></i></div>
                <div class="stat-num">{{ number_format($stats['completed_projects'] ?? 0) }}</div>
                <div class="stat-label">Completed Projects</div>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ══════════ LEFT COLUMN ══════════ --}}
        <div class="col-lg-4">

            {{-- Contact Info --}}
            <div class="glass-card clickable fade-up delay-2"
                 onclick="window.location='{{ route('intern.profile.edit') }}'">
                <span class="edit-hint"><i class="ti ti-edit"></i> Edit</span>
                <div class="card-head">
                    <div class="card-head-icon"><i class="ti ti-address-book"></i></div>
                    <h5>Contact Information</h5>
                </div>
                <div>
                    <div class="contact-row">
                        <div class="ci-icon ci-email"><i class="ti ti-mail"></i></div>
                        <div>
                            <span class="ci-label">Email</span>
                            <span class="ci-value">{{ $intern->email }}</span>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="ci-icon ci-phone"><i class="ti ti-phone-call"></i></div>
                        <div>
                            <span class="ci-label">Phone</span>
                            <span class="ci-value">{{ $intern->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="ci-icon ci-location"><i class="ti ti-map-pin"></i></div>
                        <div>
                            <span class="ci-label">Location</span>
                            <span class="ci-value">{{ $intern->city ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="ci-icon ci-edu"><i class="ti ti-school"></i></div>
                        <div>
                            <span class="ci-label">University</span>
                            <span class="ci-value">{{ $intern->university ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Internship Progress --}}
            <div class="glass-card fade-up delay-3">
                <div class="card-head">
                    <div class="card-head-icon"><i class="ti ti-chart-bar"></i></div>
                    <h5>Internship Progress</h5>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="ci-label mb-0" style="font-size:.72rem;">Overall Completion</span>
                    <span style="font-family:'Syne',sans-serif;font-size:.9rem;font-weight:800;color:var(--accent1);">{{ $progressPercent }}%</span>
                </div>
                <div class="prog-track mb-3">
                    <div class="prog-fill" style="width:{{ $progressPercent }}%;"></div>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <div class="ci-label">Start</div>
                        <div class="ci-value">{{ $startDate->format('d M Y') }}</div>
                    </div>
                    <div class="text-end">
                        <div class="ci-label">End</div>
                        <div class="ci-value">{{ $endDate->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="prog-alert">
                    <i class="ti ti-hourglass-half" style="font-size:1rem;"></i>
                    <span><strong>{{ number_format($remainingDays) }} days</strong> remaining in your internship</span>
                </div>
            </div>

        </div>

        {{-- ══════════ RIGHT COLUMN ══════════ --}}
        <div class="col-lg-8">

            {{-- About Me --}}
            <div class="glass-card clickable fade-up delay-2"
                 onclick="window.location='{{ route('intern.profile.edit') }}'">
                <span class="edit-hint"><i class="ti ti-edit"></i> Edit</span>
                <div class="card-head">
                    <div class="card-head-icon"><i class="ti ti-user-circle"></i></div>
                    <h5>About Me</h5>
                </div>
                @if($intern->bio)
                    <p class="bio-text mb-0">{{ $intern->bio }}</p>
                @else
                    <div class="empty-box">
                        <i class="ti ti-message-dots"></i>
                        <p>No bio added yet. Click to write a professional introduction.</p>
                    </div>
                @endif
            </div>

            {{-- Skills --}}
            <div class="glass-card clickable fade-up delay-3"
                 onclick="window.location='{{ route('intern.profile.edit') }}'">
                <span class="edit-hint"><i class="ti ti-edit"></i> Edit</span>
                <div class="card-head">
                    <div class="card-head-icon"><i class="ti ti-code"></i></div>
                    <h5>Skills &amp; Technologies</h5>
                </div>
                @if(isset($skills) && $skills->count() > 0)
                    <div>
                        @foreach($skills as $skill)
                            <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="empty-box">
                        <i class="ti ti-tool"></i>
                        <p>No skills added yet. Click to showcase your technical expertise.</p>
                    </div>
                @endif
            </div>

            {{-- Timeline --}}
            <div class="glass-card mb-0 fade-up delay-4">
                <div class="card-head">
                    <div class="card-head-icon"><i class="ti ti-timeline"></i></div>
                    <h5>Internship Timeline</h5>
                </div>
                <div class="tl-wrapper">
                    <div class="tl-item">
                        <div class="tl-dot c1"></div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="tl-title">🚀 Internship Started</div>
                            <span class="tl-date">{{ $startDate->format('d M Y') }}</span>
                        </div>
                        <div class="tl-desc">Your journey at Ezitech began — welcome aboard!</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot c2"></div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="tl-title">📍 Current Progress</div>
                            <span class="tl-date">{{ Carbon::now()->format('d M Y') }}</span>
                        </div>
                        <div class="tl-desc">{{ $progressPercent }}% completed &middot; {{ number_format($remainingDays) }} days remaining</div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot c3"></div>
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="tl-title">🏆 Expected Completion</div>
                            <span class="tl-date">{{ $endDate->format('d M Y') }}</span>
                        </div>
                        <div class="tl-desc">Target completion date — keep pushing!</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════ UPLOAD IMAGE MODAL ══════════ --}}
<div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius: 1.1rem; overflow:hidden; box-shadow: var(--shadow-lg);">
            <form action="{{ route('intern.update-profile-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0" style="background: var(--ink); padding: 1.2rem 1.4rem .8rem;">
                    <h6 class="modal-title text-white" style="font-family:'Syne',sans-serif;font-size:.95rem;font-weight:700;">
                        <i class="ti ti-camera me-2" style="color:var(--accent1);"></i>Update Profile Picture
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <img id="imagePreview" src="{{ $profileImage }}" alt="Preview"
                             style="width:100px;height:100px;object-fit:cover;border-radius:50%;
                                    border:3px solid var(--accent1);box-shadow:0 0 0 5px rgba(91,94,244,.15);">
                    </div>
                    <label class="form-label" style="font-size:.75rem;font-weight:600;color:var(--ink-muted);">Choose New Image</label>
                    <input type="file" name="profile_image" class="form-control form-control-sm"
                           accept="image/*" onchange="previewImage(this)"
                           style="border-color:rgba(91,94,244,.25);font-size:.78rem;">
                    <small class="text-muted d-block mt-2" style="font-size:.65rem;">Max 2MB · Square recommended (400×400px) · JPG, PNG, GIF</small>
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
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection