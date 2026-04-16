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
    <meta name="description" content="{{ $intern->name }} - Professional Portfolio">
    <title>{{ $intern->name }} | Professional Portfolio</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.27/dist/lenis.min.js"></script>

    <style>
        /* ════════════════════════════════════════════════
           RESET & BASE STYLES - CLEAN LAYOUT
        ════════════════════════════════════════════════ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(180deg, #0a0e27 0%, #0f1320 50%, #0d1524 100%);
            color: #d1d5db;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            width: 100%;
        }

        /* ════════════════════════════════════════════════
           DESIGN TOKENS
        ════════════════════════════════════════════════ */
        :root {
            --accent-primary: #6366f1;
            --accent-secondary: #f97316;
            --accent-tertiary: #ec4899;
            --accent-cyan: #06b6d4;
            --fg-main: #ffffff;
            --fg-secondary: #d1d5db;
            --fg-tertiary: #9ca3af;
            --border-color: rgba(99, 102, 241, 0.2);
            --bg-card: rgba(21, 29, 53, 0.5);
        }

        [data-theme="light"] {
            --accent-primary: #4f46e5;
            --accent-secondary: #ea580c;
            --accent-tertiary: #be185d;
            --fg-main: #1f2937;
            --fg-secondary: #374151;
            --fg-tertiary: #6b7280;
            --border-color: rgba(79, 70, 229, 0.2);
            --bg-card: rgba(243, 244, 246, 0.8);
        }

        [data-theme="light"] {
            background: linear-gradient(180deg, #ffffff 0%, #f9fafb 50%, #f3f4f6 100%);
        }

        /* ════════════════════════════════════════════════
           MAIN CONTAINER - NO LAYOUT BREAKING
        ════════════════════════════════════════════════ */
        .portfolio-main {
            max-width: 1100px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* ════════════════════════════════════════════════
           THEME TOGGLE - FIXED BUT NOT BREAKING
        ════════════════════════════════════════════════ */
        .theme-btn {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 99;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 0.7rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            color: var(--fg-main);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
        }

        .theme-btn:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
            transform: scale(1.1);
        }

        /* ════════════════════════════════════════════════
           SCROLL PROGRESS - THIN, NON-BREAKING
        ════════════════════════════════════════════════ */
        .scroll-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            z-index: 98;
            width: 0%;
        }

        /* ════════════════════════════════════════════════
           BACK BUTTON
        ════════════════════════════════════════════════ */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
            cursor: pointer;
        }

        .btn-back:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
            transform: translateX(-3px);
        }

        /* ════════════════════════════════════════════════
           TYPOGRAPHY
        ════════════════════════════════════════════════ */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
            font-weight: 800;
            color: var(--fg-main);
            margin-bottom: 0.5rem;
        }

        h1 { font-size: clamp(2.5rem, 8vw, 4rem); }
        h2 { font-size: clamp(1.4rem, 5vw, 2.2rem); }
        h3 { font-size: 1.2rem; }

        .label-small {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 700;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ════════════════════════════════════════════════
           HERO SECTION
        ════════════════════════════════════════════════ */
        .hero {
            text-align: center;
            padding: 4rem 2rem;
            border-radius: 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(236, 72, 153, 0.05) 100%);
            border: 1px solid var(--border-color);
            margin-bottom: 3rem;
            backdrop-filter: blur(20px);
            position: relative;
        }

        .avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.5rem;
            border: 3px solid var(--accent-primary);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.3);
            animation: avatarGlow 3s ease-in-out infinite;
        }

        @keyframes avatarGlow {
            0%, 100% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.3); }
            50% { box-shadow: 0 0 60px rgba(99, 102, 241, 0.6); }
        }

        .name {
            background: linear-gradient(135deg, var(--fg-main), var(--accent-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.3rem;
        }

        .email {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent-secondary);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            letter-spacing: 0.05em;
        }

        .badge-role {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.8rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(236, 72, 153, 0.15));
            border: 1.5px solid var(--accent-primary);
            border-radius: 50px;
            color: var(--fg-main);
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .badge-role:hover {
            background: var(--accent-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
        }

        /* ════════════════════════════════════════════════
           CARD STYLES
        ════════════════════════════════════════════════ */
        .card-section {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, rgba(236, 72, 153, 0.03) 100%);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            backdrop-filter: blur(20px);
            transition: all 0.4s ease;
        }

        .card-section:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.15);
        }

        .section-title {
            margin-bottom: 2rem;
        }

        .section-title h2 {
            margin-bottom: 0.8rem;
        }

        .accent-line {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 10px;
        }

        /* ════════════════════════════════════════════════
           ABOUT SECTION
        ════════════════════════════════════════════════ */
        .about-text {
            font-size: 1rem;
            line-height: 1.9;
            color: var(--fg-secondary);
            border-left: 4px solid var(--accent-primary);
            padding-left: 1.5rem;
            font-style: italic;
        }

        /* ════════════════════════════════════════════════
           STATS
        ════════════════════════════════════════════════ */
        .stat-box {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(236, 72, 153, 0.04) 100%);
            border: 1px solid var(--border-color);
            border-radius: 1.2rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.4s ease;
            cursor: pointer;
        }

        .stat-box:hover {
            transform: translateY(-8px);
            border-color: var(--accent-primary);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.2);
        }

        .stat-icon {
            font-size: 2.2rem;
            color: var(--accent-primary);
            margin-bottom: 0.8rem;
            transition: all 0.3s ease;
        }

        .stat-box:hover .stat-icon {
            transform: scale(1.25) rotate(8deg);
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.4rem;
        }

        .stat-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--fg-tertiary);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* ════════════════════════════════════════════════
           SKILLS
        ════════════════════════════════════════════════ */
        .skill-tag {
            display: inline-block;
            padding: 0.7rem 1.4rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(236, 72, 153, 0.08));
            border: 1.5px solid var(--border-color);
            border-radius: 50px;
            color: var(--fg-main);
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            cursor: pointer;
            margin: 0.4rem;
        }

        .skill-tag:hover {
            background: var(--accent-primary);
            color: white;
            border-color: var(--accent-primary);
            transform: translateY(-4px);
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.3);
        }

        /* ════════════════════════════════════════════════
           PROJECT CARDS
        ════════════════════════════════════════════════ */
        .project-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.06) 0%, rgba(236, 72, 153, 0.03) 100%);
            border: 1px solid var(--border-color);
            border-radius: 1.3rem;
            padding: 2rem;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .project-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
        }

        .project-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-primary);
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.25);
        }

        .project-icon {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-cyan));
            border-radius: 10px;
            color: white;
            font-size: 1.3rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .project-card:hover .project-icon {
            transform: scale(1.2) rotate(10deg);
        }

        .project-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--fg-main);
            margin-bottom: 0.6rem;
        }

        .project-date {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: var(--accent-secondary);
            margin-bottom: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        .project-desc {
            font-size: 0.9rem;
            color: var(--fg-tertiary);
            line-height: 1.7;
            flex-grow: 1;
        }

        /* ════════════════════════════════════════════════
           CERTIFICATE CARDS
        ════════════════════════════════════════════════ */
        .cert-card {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.08) 0%, rgba(236, 72, 153, 0.03) 100%);
            border: 1px solid rgba(249, 115, 22, 0.3);
            border-radius: 1.3rem;
            padding: 2.2rem;
            text-align: center;
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cert-card:hover {
            transform: translateY(-10px);
            border-color: var(--accent-secondary);
            box-shadow: 0 0 40px rgba(249, 115, 22, 0.2);
        }

        .cert-icon {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-secondary), #fb923c);
            border-radius: 14px;
            margin: 0 auto 1.2rem;
            font-size: 1.8rem;
            color: white;
            transition: all 0.3s ease;
        }

        .cert-card:hover .cert-icon {
            transform: scale(1.15) rotate(12deg);
        }

        .cert-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--fg-main);
            margin-bottom: 0.5rem;
        }

        .cert-date {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: var(--accent-secondary);
            margin-bottom: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .btn-download {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-cyan));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-download:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.4);
            color: white;
        }

        /* ════════════════════════════════════════════════
           MODAL
        ════════════════════════════════════════════════ */
        .modal-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 14, 39, 0.9);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }

        .modal-bg.active {
            display: flex;
        }

        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 1.5rem;
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            backdrop-filter: blur(20px);
        }

        .modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--fg-secondary);
            transition: all 0.3s ease;
            z-index: 201;
        }

        .modal-close:hover {
            color: var(--accent-primary);
            transform: rotate(90deg);
        }

        /* ════════════════════════════════════════════════
           FOOTER
        ════════════════════════════════════════════════ */
        .footer {
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-text {
            font-size: 0.95rem;
            color: var(--fg-secondary);
            margin-bottom: 0.5rem;
        }

        .footer-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            color: var(--fg-tertiary);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        /* ════════════════════════════════════════════════
           EMPTY STATE
        ════════════════════════════════════════════════ */
        .empty {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--fg-tertiary);
        }

        .empty-icon {
            font-size: 3.5rem;
            color: rgba(99, 102, 241, 0.1);
            margin-bottom: 1rem;
        }

        /* ════════════════════════════════════════════════
           RESPONSIVE DESIGN
        ════════════════════════════════════════════════ */
        @media (max-width: 768px) {
            .portfolio-main {
                padding: 2rem 1rem;
            }

            .hero {
                padding: 2.5rem 1.5rem;
            }

            .avatar {
                width: 110px;
                height: 110px;
            }

            .card-section {
                padding: 2rem 1.5rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .project-card,
            .cert-card {
                padding: 1.5rem;
            }

            .cert-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .modal-box {
                padding: 2rem;
            }
        }

        @media (max-width: 480px) {
            .portfolio-main {
                padding: 1.5rem 0.75rem;
            }

            .btn-back {
                width: 100%;
                justify-content: center;
            }

            .hero {
                padding: 2rem 1rem;
            }

            .avatar {
                width: 90px;
                height: 90px;
                margin-bottom: 1rem;
            }

            h1 {
                font-size: 1.4rem;
            }

            .card-section {
                padding: 1.5rem 1rem;
                margin-bottom: 1.5rem;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .skill-tag {
                padding: 0.6rem 1.1rem;
                font-size: 0.8rem;
            }

            .project-card,
            .cert-card {
                padding: 1.3rem;
            }

            .modal-box {
                width: 95%;
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
<div class="scroll-bar"></div>

<button class="theme-btn" id="themeBtn" title="Toggle Theme">
    <i class="ti ti-sun"></i>
</button>

<div class="portfolio-main">

    {{-- BACK BUTTON --}}
    @if(Auth::guard('intern')->check() && Auth::guard('intern')->user()->int_id == $intern->int_id)
        <a href="{{ route('intern.profile') }}" class="btn-back">
            <i class="ti ti-arrow-left"></i> Back
        </a>
    @endif

    {{-- ════════════════════════════════════════════════
         HERO
    ════════════════════════════════════════════════ --}}
    <section class="hero">
        <img src="{{ $profileImage }}" alt="{{ $intern->name }}" class="avatar">
        <h1 class="name">{{ $intern->name }}</h1>
        <p class="email">{{ $intern->eti_id }}</p>
        <span class="badge-role">
            <i class="ti ti-code"></i>
            {{ $intern->int_technology ?? 'Professional Intern' }}
        </span>
    </section>

    {{-- ════════════════════════════════════════════════
         ABOUT
    ════════════════════════════════════════════════ --}}
    @if($intern->bio)
    <section class="card-section">
        <div class="section-title">
            <p class="label-small">About</p>
            <h2>About Me</h2>
            <div class="accent-line"></div>
        </div>
        <p class="about-text">{{ $intern->bio }}</p>
    </section>
    @endif

    {{-- ════════════════════════════════════════════════
         STATS
    ════════════════════════════════════════════════ --}}
    <section class="card-section">
        <div class="section-title">
            <p class="label-small">Overview</p>
            <h2>Statistics</h2>
            <div class="accent-line"></div>
        </div>
        <div class="row g-3">
            @foreach($statItems as $item)
            <div class="col-6 col-md-3">
                <div class="stat-box">
                    <i class="ti {{ $item['icon'] }} stat-icon"></i>
                    <div class="stat-number counter" data-target="{{ $item['value'] }}">0</div>
                    <div class="stat-label">{{ $item['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ════════════════════════════════════════════════
         SKILLS
    ════════════════════════════════════════════════ --}}
    @if($skills->count() > 0)
    <section class="card-section">
        <div class="section-title">
            <p class="label-small">Expertise</p>
            <h2>Technical Skills</h2>
            <div class="accent-line"></div>
        </div>
        <div>
            @foreach($skills as $skill)
                <span class="skill-tag">{{ $skill }}</span>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════════
         PROJECTS
    ════════════════════════════════════════════════ --}}
    @if($projects->count() > 0)
    <section class="card-section">
        <div class="section-title">
            <p class="label-small">Work</p>
            <h2>Featured Projects</h2>
            <div class="accent-line"></div>
        </div>
        <div class="row g-3">
            @foreach($projects as $project)
            <div class="col-12 col-md-6">
                <div class="project-card" onclick="openModal({{ $loop->index }})">
                    <div class="project-icon">
                        <i class="ti ti-folder-open"></i>
                    </div>
                    <h3 class="project-title">{{ $project->title }}</h3>
                    @if(!empty($project->end_date))
                    <p class="project-date">
                        <i class="ti ti-calendar-check"></i>
                        {{ Carbon::parse($project->end_date)->format('M Y') }}
                    </p>
                    @endif
                    <p class="project-desc">{{ $project->description ?? 'No description' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @else
    <section class="card-section">
        <div class="empty">
            <i class="ti ti-inbox empty-icon"></i>
            <p>Projects will appear here.</p>
        </div>
    </section>
    @endif

    {{-- ════════════════════════════════════════════════
         CERTIFICATES
    ════════════════════════════════════════════════ --}}
    @if($certificates->count() > 0)
    <section class="card-section">
        <div class="section-title">
            <p class="label-small">Achievements</p>
            <h2>Certificates</h2>
            <div class="accent-line"></div>
        </div>
        <div class="row g-3">
            @foreach($certificates as $cert)
            <div class="col-12 col-sm-6 col-md-4">
                <div class="cert-card">
                    <div>
                        <div class="cert-icon">
                            <i class="ti ti-award"></i>
                        </div>
                        <h4 class="cert-title">{{ $cert->title ?? 'Certificate' }}</h4>
                        <p class="cert-date">{{ Carbon::parse($cert->created_at)->format('M Y') }}</p>
                    </div>
                    @if(!empty($cert->file_path))
                    <a href="{{ asset($cert->file_path) }}" class="btn-download" download>
                        <i class="ti ti-download"></i> Download
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @else
    <section class="card-section">
        <div class="empty">
            <i class="ti ti-trophy empty-icon"></i>
            <p>Certificates will appear here.</p>
        </div>
    </section>
    @endif

    {{-- FOOTER --}}
    <footer class="footer">
        <p class="footer-text">&copy; {{ date('Y') }} {{ $intern->name }}</p>
        <p class="footer-sub">Professional Portfolio | All Features Included | Production Ready</p>
    </footer>

</div>

{{-- MODAL --}}
<div class="modal-bg" id="modal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <div id="modalContent"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // SCROLL PROGRESS
    window.addEventListener('scroll', () => {
        const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
        document.querySelector('.scroll-bar').style.width = scrolled + '%';
    });

    // THEME TOGGLE
    const themeBtn = document.getElementById('themeBtn');
    const theme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', theme);
    updateIcon();

    themeBtn.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme');
        const newTheme = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateIcon();
    });

    function updateIcon() {
        const theme = document.documentElement.getAttribute('data-theme');
        themeBtn.innerHTML = theme === 'dark' ? '<i class="ti ti-sun"></i>' : '<i class="ti ti-moon"></i>';
    }

    // SCROLL COUNTERS
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target'));
                let current = 0;
                const increment = target / 50;
                
                const update = () => {
                    if (current < target) {
                        current += increment;
                        el.textContent = Math.floor(current);
                        requestAnimationFrame(update);
                    } else {
                        el.textContent = target;
                    }
                };
                update();
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));

    // MODAL
    function openModal(index) {
        const projects = @json($projects);
        const p = projects[index];
        document.getElementById('modalContent').innerHTML = `
            <h2>${p.title}</h2>
            <p style="color: var(--accent-secondary); margin-bottom: 1rem; font-size: 0.9rem;">
                ${p.end_date ? new Date(p.end_date).toLocaleDateString('en-US', {month: 'short', year: 'numeric'}) : 'In Progress'}
            </p>
            <p style="line-height: 1.8;">${p.description || 'No description'}</p>
        `;
        document.getElementById('modal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('modal').classList.remove('active');
    }

    document.getElementById('modal').addEventListener('click', (e) => {
        if (e.target.id === 'modal') closeModal();
    });

    // SMOOTH SCROLL
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t))
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    console.log('✅ Portfolio loaded - Clean, no layout breaking!');
</script>
</body>
</html>
