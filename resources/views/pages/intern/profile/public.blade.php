@php
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    $statItems = [
        ['icon' => 'ti-list-check',   'value' => $stats['total_tasks']        ?? 0, 'label' => 'Total Tasks'],
        ['icon' => 'ti-circle-check', 'value' => $stats['completed_tasks']    ?? 0, 'label' => 'Tasks Completed'],
        ['icon' => 'ti-briefcase',    'value' => $stats['total_projects']     ?? 0, 'label' => 'Total Projects'],
        ['icon' => 'ti-trophy',       'value' => $stats['completed_projects'] ?? 0, 'label' => 'Projects Done'],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $intern->name }} - Intern Portfolio at Ezitech">
    <title>{{ $intern->name }} | Intern Portfolio - Ezitech</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    {{-- Google Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Minimal styles: only what Bootstrap cannot provide via utilities --}}
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-bg   { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); min-height: 100vh; }
        .banner-bg { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .accent-bar { width: 36px; height: 3px; background: linear-gradient(135deg, #4f46e5, #7c3aed); border-radius: 99px; }
        .hover-lift { transition: transform .2s, box-shadow .2s; }
        .hover-lift:hover { transform: translateY(-4px); box-shadow: 0 12px 28px rgba(79,70,229,.15) !important; }
        .skill-badge { transition: background .2s, color .2s, transform .2s; cursor: default; }
        .skill-badge:hover { background-color: #4f46e5 !important; color: #fff !important; transform: translateY(-2px); }
        .project-card { border-left: 3px solid #4f46e5 !important; }
        .btn-gradient { background: linear-gradient(135deg, #4f46e5, #7c3aed); border: none; transition: opacity .2s, transform .2s; }
        .btn-gradient:hover { opacity: .88; transform: translateY(-1px); }
        .glass-btn { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.35); backdrop-filter: blur(4px); }
        .glass-btn:hover { background: rgba(255,255,255,.28); }
        .cert-icon { background: linear-gradient(135deg, rgba(79,70,229,.1), rgba(124,58,237,.1)); }
        .section-eyebrow { font-size: .7rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    </style>
</head>

<body class="page-bg py-4 py-md-5">
<div class="container" style="max-width: 960px;">

    {{-- ── Back link (shown only to the authenticated intern viewing their own portfolio) ── --}}
    @if(Auth::guard('intern')->check() && Auth::guard('intern')->user()->int_id == $intern->int_id)
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('intern.profile') }}"
               class="btn btn-sm glass-btn rounded-pill text-white fw-semibold d-inline-flex align-items-center gap-1">
                <i class="ti ti-arrow-left"></i> Back to My Profile
            </a>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         PROFILE CARD
    ══════════════════════════════════════════════════ --}}
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">

        {{-- ── Banner / Hero ── --}}
        <div class="banner-bg text-white text-center px-4 py-5">
            <img src="{{ $profileImage }}"
                 alt="{{ $intern->name }}"
                 class="rounded-circle object-fit-cover border border-4 border-white shadow mb-3"
                 width="112" height="112">

            <h1 class="fs-4 fw-bold mb-1">{{ $intern->name }}</h1>
            <p class="mb-0 opacity-75 small">{{ $intern->eti_id }}</p>

            <span class="badge rounded-pill px-3 py-2 small fw-semibold mt-2 d-inline-flex align-items-center gap-1"
                  style="background: rgba(255,255,255,.2); border: 1px solid rgba(255,255,255,.35);">
                <i class="ti ti-device-laptop"></i>
                {{ $intern->int_technology ?? 'Technology Intern' }}
            </span>
        </div>

        <div class="card-body p-0">

            {{-- ══════════════════════
                 ABOUT ME
            ══════════════════════ --}}
            @if($intern->bio)
            <div class="px-4 px-md-5 py-4 border-bottom">
                <p class="section-eyebrow text-primary mb-1">About</p>
                <h2 class="fs-5 fw-bold mb-2">About Me</h2>
                <div class="accent-bar mb-3"></div>
                <p class="text-secondary lh-lg mb-0">{{ $intern->bio }}</p>
            </div>
            @endif

            {{-- ══════════════════════
                 STATISTICS
            ══════════════════════ --}}
            <div class="px-4 px-md-5 py-4 border-bottom">
                <p class="section-eyebrow text-primary mb-1">Overview</p>
                <h2 class="fs-5 fw-bold mb-2">Statistics</h2>
                <div class="accent-bar mb-4"></div>

                <div class="row g-3">
                    @foreach($statItems as $item)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 bg-light rounded-3 text-center py-3 px-2 h-100 shadow-sm hover-lift">
                            <i class="ti {{ $item['icon'] }} fs-2 text-primary mb-2"></i>
                            <div class="fw-bold text-primary mb-1" style="font-size: 1.6rem;">
                                {{ number_format($item['value']) }}
                            </div>
                            <div class="text-muted fw-semibold" style="font-size: .72rem; letter-spacing: .04em;">
                                {{ $item['label'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════
                 TECHNICAL SKILLS
            ══════════════════════ --}}
            @if($skills->count() > 0)
            <div class="px-4 px-md-5 py-4 border-bottom">
                <p class="section-eyebrow text-primary mb-1">Expertise</p>
                <h2 class="fs-5 fw-bold mb-2">Technical Skills</h2>
                <div class="accent-bar mb-3"></div>

                <div class="d-flex flex-wrap gap-2">
                    @foreach($skills as $skill)
                        <span class="skill-badge badge rounded-pill border border-primary text-primary bg-white px-3 py-2"
                              style="font-size: .8rem; font-weight: 500;">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══════════════════════
                 FEATURED PROJECTS
            ══════════════════════ --}}
            @if($projects->count() > 0)
            <div class="px-4 px-md-5 py-4 border-bottom">
                <p class="section-eyebrow text-primary mb-1">Work</p>
                <h2 class="fs-5 fw-bold mb-2">Featured Projects</h2>
                <div class="accent-bar mb-4"></div>

                <div class="row g-3">
                    @foreach($projects as $project)
                    <div class="col-12 col-md-6">
                        <div class="project-card card border-0 bg-light rounded-3 h-100 p-3 shadow-sm hover-lift">

                            {{-- Project title row --}}
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span class="d-flex align-items-center justify-content-center rounded-2 bg-white shadow-sm flex-shrink-0"
                                      style="width: 36px; height: 36px;">
                                    <i class="ti ti-folder text-primary"></i>
                                </span>
                                <h5 class="fw-bold lh-sm mb-0" style="font-size: .95rem;">
                                    {{ $project->title }}
                                </h5>
                            </div>

                            {{-- Completion date --}}
                            @if(!empty($project->end_date))
                            <p class="text-muted d-flex align-items-center gap-1 mb-2" style="font-size: .72rem;">
                                <i class="ti ti-calendar-check"></i>
                                Completed {{ Carbon::parse($project->end_date)->format('M Y') }}
                            </p>
                            @endif

                            {{-- Description --}}
                            <p class="text-secondary lh-lg mb-0" style="font-size: .82rem;">
                                {{ $project->description ?? 'No description available.' }}
                            </p>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ══════════════════════
                 CERTIFICATES
            ══════════════════════ --}}
            @if($certificates->count() > 0)
            <div class="px-4 px-md-5 py-4">
                <p class="section-eyebrow text-primary mb-1">Achievements</p>
                <h2 class="fs-5 fw-bold mb-2">Certificates</h2>
                <div class="accent-bar mb-4"></div>

                <div class="row g-3">
                    @foreach($certificates as $certificate)
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card border-0 bg-light rounded-3 h-100 p-3 shadow-sm text-center hover-lift">

                            {{-- Icon block --}}
                            <div class="cert-icon d-flex align-items-center justify-content-center rounded-3 mx-auto mb-3"
                                 style="width: 56px; height: 56px;">
                                <i class="ti ti-certificate fs-3 text-primary"></i>
                            </div>

                            <h6 class="fw-bold mb-1" style="font-size: .88rem;">
                                {{ $certificate->title ?? 'Internship Certificate' }}
                            </h6>

                            <p class="text-muted mb-3" style="font-size: .72rem;">
                                <i class="ti ti-calendar me-1"></i>
                                Issued {{ Carbon::parse($certificate->created_at)->format('M Y') }}
                            </p>

                            @if(!empty($certificate->file_path))
                            <a href="{{ asset($certificate->file_path) }}"
                               class="btn btn-sm btn-gradient rounded-pill px-3 text-white fw-semibold d-inline-flex align-items-center gap-1 mx-auto"
                               download>
                                <i class="ti ti-download"></i> Download
                            </a>
                            @endif

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /card-body --}}
    </div>{{-- /profile card --}}

    {{-- ── Footer ── --}}
    <footer class="text-center text-white pb-4">
        <p class="mb-1 small fw-semibold opacity-75">
            &copy; {{ date('Y') }} {{ $intern->name }} &bull; Intern at Ezitech
        </p>
        <p class="mb-0 opacity-50" style="font-size: .7rem;">
            Powered by Ezitech Internship Program
        </p>
    </footer>

</div>{{-- /container --}}

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>