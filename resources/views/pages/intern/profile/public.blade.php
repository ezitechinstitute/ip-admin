@extends('layouts/layoutMaster')

@section('title', $intern->name . ' | Professional Portfolio')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* HERO SECTION */
    .hero-section {
        position: relative;
        min-height: 480px;
        border-radius: 2rem;
        overflow: hidden;
        margin-bottom: 3rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    }
    
    .hero-bg-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 20% 80%, rgba(59,130,246,0.15) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(139,92,246,0.15) 0%, transparent 50%),
            repeating-linear-gradient(45deg, rgba(255,255,255,0.02) 0px, rgba(255,255,255,0.02) 2px, transparent 2px, transparent 8px);
        pointer-events: none;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
        padding: 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }
    
    .hero-left {
        flex: 1;
        min-width: 280px;
    }
    
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(59,130,246,0.2);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #3b82f6;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(59,130,246,0.3);
    }
    
    .hero-title {
        font-size: 3rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .hero-subtitle {
        font-size: 1rem;
        color: #94a3b8;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    
    .hero-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 2rem;
    }
    
    .hero-tag {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(10px);
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-size: 0.8rem;
        color: #cbd5e1;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .hero-stats {
        display: flex;
        gap: 2rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .hero-stat {
        text-align: center;
    }
    
    .hero-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
        display: block;
    }
    
    .hero-stat-label {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .hero-right {
        position: relative;
    }
    
    .hero-avatar-wrapper {
        position: relative;
        width: 220px;
        height: 220px;
    }
    
    .hero-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(59,130,246,0.5);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        transition: transform 0.3s ease;
    }
    
    .hero-avatar:hover {
        transform: scale(1.02);
    }
    
    .hero-avatar-ring {
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        border-radius: 50%;
        border: 2px dashed rgba(59,130,246,0.4);
        animation: spin 20s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .hero-status {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 16px;
        height: 16px;
        background: #10b981;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 10px rgba(16,185,129,0.5);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.1); }
    }
    
    .float-1, .float-2, .float-3 {
        position: absolute;
        background: rgba(59,130,246,0.1);
        backdrop-filter: blur(5px);
        border-radius: 50%;
        pointer-events: none;
    }
    
    .float-1 {
        width: 60px;
        height: 60px;
        top: 10%;
        right: 5%;
        animation: float 6s ease-in-out infinite;
    }
    
    .float-2 {
        width: 40px;
        height: 40px;
        bottom: 15%;
        left: 5%;
        animation: float 8s ease-in-out infinite reverse;
    }
    
    .float-3 {
        width: 80px;
        height: 80px;
        top: 50%;
        right: 15%;
        animation: float 10s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }

    /* ENHANCED RING SECTION */
    .ring-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.85) 100%);
        backdrop-filter: blur(12px);
        border-radius: 2rem;
        padding: 2rem 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.5);
        box-shadow: 0 20px 35px -10px rgba(0,0,0,0.1);
        height: 100%;
    }

    .ring-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 30px 45px -15px rgba(0,0,0,0.2);
    }

    .ring-card-tasks:hover { border-color: #3b82f6; }
    .ring-card-projects:hover { border-color: #10b981; }
    .ring-card-internship:hover { border-color: #f59e0b; }

    .progress-card {
        position: relative;
        width: 170px;
        height: 170px;
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
        stroke-dasharray: 8 12;
    }

    .progress-ring-fill {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dashoffset 1.5s cubic-bezier(0.2, 0.9, 0.4, 1.1);
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

    .ring-card-tasks .progress-value { color: #3b82f6; }
    .ring-card-projects .progress-value { color: #10b981; }
    .ring-card-internship .progress-value { color: #f59e0b; }

    .progress-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        margin-top: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .ring-details {
        text-align: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }

    .ring-detail-header {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #6c86a3;
        margin-bottom: 0.75rem;
    }

    .ring-stats {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }

    .ring-stats .completed { color: #1e293b; }
    .ring-stats .separator {
        font-size: 1.2rem;
        color: #94a3b8;
        margin: 0 4px;
    }
    .ring-stats .total { color: #94a3b8; }
    .ring-stats .unit {
        font-size: 0.8rem;
        font-weight: 500;
        color: #94a3b8;
        margin-left: 4px;
    }

    .ring-progress-bar {
        height: 6px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.75rem;
    }

    .ring-progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 1s ease;
    }
    .ring-card-tasks .ring-progress-fill { background: linear-gradient(90deg, #3b82f6, #1e40af); }
    .ring-card-projects .ring-progress-fill { background: linear-gradient(90deg, #10b981, #047857); }
    .ring-card-internship .ring-progress-fill { background: linear-gradient(90deg, #f59e0b, #b45309); }

    .ring-stats-sub {
        display: flex;
        justify-content: center;
        gap: 1rem;
        font-size: 0.7rem;
        flex-wrap: wrap;
    }
    .ring-stats-sub .done { color: #10b981; }
    .ring-stats-sub .pending { color: #f59e0b; }

    .ring-status-badge {
        margin-top: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 100px;
        text-align: center;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        width: 100%;
        transition: all 0.3s ease;
    }
    .task-status {
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05));
        color: #3b82f6;
        border: 1px solid rgba(59,130,246,0.2);
    }
    .project-status {
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(16,185,129,0.05));
        color: #10b981;
        border: 1px solid rgba(16,185,129,0.2);
    }
    .internship-status {
        background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(245,158,11,0.05));
        color: #f59e0b;
        border: 1px solid rgba(245,158,11,0.2);
    }

    /* STATS CARDS */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card-modern {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 1.75rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 10px 30px -8px rgba(0,0,0,0.1);
    }
    
    .stat-card-modern:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.2);
    }
    
    .stat-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--stat-color), transparent);
    }
    
    .stat-icon-wrapper {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(59,130,246,0.05));
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .stat-icon-modern {
        font-size: 2rem;
    }
    
    .stat-value-modern {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .stat-label-modern {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
    }

    /* ABOUT SECTION */
    .about-section {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.6);
        transition: all 0.4s ease;
    }
    
    .about-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
    }
    
    .about-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .about-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .about-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .about-quote {
        font-size: 0.9rem;
        color: #6c86a3;
        line-height: 1.8;
        position: relative;
        padding-left: 1rem;
        border-left: 3px solid #3b82f6;
        font-style: italic;
    }

    /* SKILLS SECTION */
    .skills-section {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.4s ease;
    }
    
    .skills-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
    }
    
    .skills-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .skills-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #10b981, #047857);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .skills-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .skills-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0;
    }
    
    .skill-tag-advanced {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(16,185,129,0.05));
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid rgba(59,130,246,0.2);
        margin: 0.35rem;
    }
    
    .skill-tag-advanced:hover {
        transform: translateY(-3px);
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        color: white;
        border-color: transparent;
        box-shadow: 0 10px 20px -8px rgba(59,130,246,0.4);
    }

    /* PROJECTS SECTION */
    .projects-section {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.4s ease;
    }
    
    .projects-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
    }
    
    .project-card-modern {
        background: rgba(255,255,255,0.6);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }
    
    .project-card-modern:hover {
        transform: translateY(-5px);
        background: white;
        border-color: #3b82f6;
        box-shadow: 0 10px 25px -8px rgba(0,0,0,0.1);
    }
    
    .project-icon-modern {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .project-icon-modern i {
        font-size: 1.5rem;
        color: white;
    }
    
    .project-title-modern {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .project-date-modern {
        font-size: 0.7rem;
        color: #f59e0b;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .project-desc-modern {
        font-size: 0.85rem;
        color: #6c86a3;
        line-height: 1.5;
    }

    /* CERTIFICATES SECTION */
    .certificates-section {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.4s ease;
    }
    
    .certificates-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
    }
    
    .cert-card-modern {
        background: rgba(255,255,255,0.6);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }
    
    .cert-card-modern:hover {
        transform: translateY(-5px);
        background: white;
        border-color: #f59e0b;
        box-shadow: 0 10px 25px -8px rgba(0,0,0,0.1);
    }
    
    .cert-icon-modern {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #f59e0b, #ea580c);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    
    .cert-icon-modern i {
        font-size: 1.8rem;
        color: white;
    }
    
    .cert-title-modern {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .cert-date-modern {
        font-size: 0.7rem;
        color: #f59e0b;
        margin-bottom: 1rem;
    }
    
    .btn-download-modern {
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .btn-download-modern:hover {
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 5px 15px rgba(59,130,246,0.3);
    }

    /* BADGES SECTION */
    .badges-section {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 2rem;
        transition: all 0.4s ease;
    }
    
    .badges-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15);
    }
    
    .badge-card-modern {
        text-align: center;
        padding: 1rem;
        border-radius: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: rgba(255,255,255,0.5);
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .badge-card-modern.earned {
        background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(16,185,129,0.08));
        border: 1px solid rgba(59,130,246,0.3);
    }
    
    .badge-card-modern.locked {
        opacity: 0.5;
        filter: grayscale(0.3);
    }
    
    .badge-card-modern:hover {
        transform: translateY(-5px);
        background: rgba(255,255,255,0.9);
    }
    
    .badge-icon-modern {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .empty-state-modern {
        text-align: center;
        padding: 3rem;
        color: #6c86a3;
    }
    
    .empty-state-modern i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @media (max-width: 768px) {
        .hero-content {
            padding: 2rem;
            flex-direction: column;
            text-align: center;
        }
        .hero-title {
            font-size: 2rem;
        }
        .hero-tags {
            justify-content: center;
        }
        .hero-stats {
            justify-content: center;
        }
        .hero-avatar-wrapper {
            width: 160px;
            height: 160px;
        }
        .ring-card {
            padding: 1.5rem 1rem;
        }
        .progress-card {
            width: 140px;
            height: 140px;
        }
        .progress-value {
            font-size: 1.4rem;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .stat-value-modern {
            font-size: 1.5rem;
        }
        .stat-icon-wrapper {
            width: 45px;
            height: 45px;
        }
        .stat-icon-modern {
            font-size: 1.3rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    
    {{-- HERO SECTION --}}
    <div class="hero-section">
        <div class="hero-bg-pattern"></div>
        <div class="float-1"></div>
        <div class="float-2"></div>
        <div class="float-3"></div>
        
        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-badge">
                    <i class="bi bi-star-fill"></i>
                    <span>Professional Portfolio</span>
                </div>
                <h1 class="hero-title">{{ $intern->name }}</h1>
                <p class="hero-subtitle">
                    {{ $intern->int_technology ?? 'Passionate Developer' }} | Creating digital experiences that matter
                </p>
                <div class="hero-tags">
                    <span class="hero-tag"><i class="bi bi-envelope me-1"></i>{{ $intern->email }}</span>
                    @if($intern->city)
                    <span class="hero-tag"><i class="bi bi-geo-alt me-1"></i>{{ $intern->city }}</span>
                    @endif
                    <span class="hero-tag"><i class="bi bi-calendar3 me-1"></i>Member since {{ \Carbon\Carbon::parse($intern->created_at ?? $intern->start_date)->format('Y') }}</span>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-value">{{ $stats['total_tasks'] }}</span>
                        <span class="hero-stat-label">Tasks</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">{{ $stats['total_projects'] }}</span>
                        <span class="hero-stat-label">Projects</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-value">{{ $internshipData['remaining_days'] }}</span>
                        <span class="hero-stat-label">Days Left</span>
                    </div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-avatar-wrapper">
                    <div class="hero-avatar-ring"></div>
                    <img src="{{ $profileImage }}" alt="{{ $intern->name }}" class="hero-avatar">
                    <div class="hero-status"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ENHANCED RING SECTION --}}
    <div class="row g-4 mb-5">
        <!-- Tasks Ring -->
        <div class="col-md-4">
            <div class="ring-card ring-card-tasks animate-card" style="animation-delay: 0.1s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <defs>
                            <linearGradient id="taskGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#3b82f6" />
                                <stop offset="100%" stop-color="#1d4ed8" />
                            </linearGradient>
                        </defs>
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill tasks-ring" cx="100" cy="100" r="80" 
                                stroke="url(#taskGradient)"
                                stroke-dasharray="502.65" stroke-dashoffset="502.65" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value tasks-percent">{{ $taskRate }}%</div>
                        <div class="progress-label"><i class="bi bi-check-circle-fill"></i> Completion</div>
                    </div>
                </div>
                <div class="ring-details">
                    <div class="ring-detail-header"><i class="bi bi-list-check"></i> Task Progress</div>
                    <div class="ring-stats">
                        <span class="completed">{{ $stats['completed_tasks'] }}</span>
                        <span class="separator">/</span>
                        <span class="total">{{ $stats['total_tasks'] }}</span>
                    </div>
                    <div class="ring-progress-bar">
                        <div class="ring-progress-fill tasks-progress-bar" style="width: {{ $taskRate }}%"></div>
                    </div>
                    <div class="ring-stats-sub">
                        <span class="done"><i class="bi bi-check-circle-fill"></i> {{ $stats['completed_tasks'] }} Completed</span>
                        <span class="pending"><i class="bi bi-clock-fill"></i> {{ $stats['total_tasks'] - $stats['completed_tasks'] }} Pending</span>
                    </div>
                </div>
                <div class="ring-status-badge task-status">
                    @if($taskRate >= 75)
                        <i class="bi bi-trophy-fill"></i> Excellent Progress
                    @elseif($taskRate >= 50)
                        <i class="bi bi-graph-up"></i> Good Progress
                    @elseif($taskRate >= 25)
                        <i class="bi bi-arrow-repeat"></i> Getting There
                    @else
                        <i class="bi bi-rocket-takeoff-fill"></i> Just Started
                    @endif
                </div>
            </div>
        </div>

        <!-- Projects Ring -->
        <div class="col-md-4">
            <div class="ring-card ring-card-projects animate-card" style="animation-delay: 0.2s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <defs>
                            <linearGradient id="projectGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#10b981" />
                                <stop offset="100%" stop-color="#047857" />
                            </linearGradient>
                        </defs>
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill projects-ring" cx="100" cy="100" r="80" 
                                stroke="url(#projectGradient)"
                                stroke-dasharray="502.65" stroke-dashoffset="502.65" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value projects-percent">{{ $projectRate }}%</div>
                        <div class="progress-label"><i class="bi bi-briefcase-fill"></i> Completion</div>
                    </div>
                </div>
                <div class="ring-details">
                    <div class="ring-detail-header"><i class="bi bi-briefcase"></i> Project Progress</div>
                    <div class="ring-stats">
                        <span class="completed">{{ $stats['completed_projects'] }}</span>
                        <span class="separator">/</span>
                        <span class="total">{{ $stats['total_projects'] }}</span>
                    </div>
                    <div class="ring-progress-bar">
                        <div class="ring-progress-fill projects-progress-bar" style="width: {{ $projectRate }}%"></div>
                    </div>
                    <div class="ring-stats-sub">
                        <span class="done"><i class="bi bi-check-circle-fill"></i> {{ $stats['completed_projects'] }} Completed</span>
                        <span class="pending"><i class="bi bi-hourglass-split"></i> {{ $stats['total_projects'] - $stats['completed_projects'] }} In Progress</span>
                    </div>
                </div>
                <div class="ring-status-badge project-status">
                    @if($projectRate >= 75)
                        <i class="bi bi-award-fill"></i> Master Builder
                    @elseif($projectRate >= 50)
                        <i class="bi bi-building"></i> Project Expert
                    @elseif($projectRate >= 25)
                        <i class="bi bi-hammer"></i> Building Skills
                    @else
                        <i class="bi bi-lightbulb"></i> Planning Phase
                    @endif
                </div>
            </div>
        </div>

        <!-- Internship Ring -->
        <div class="col-md-4">
            <div class="ring-card ring-card-internship animate-card" style="animation-delay: 0.3s;">
                <div class="progress-card">
                    <svg class="progress-ring" viewBox="0 0 200 200">
                        <defs>
                            <linearGradient id="internshipGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f59e0b" />
                                <stop offset="100%" stop-color="#b45309" />
                            </linearGradient>
                        </defs>
                        <circle class="progress-ring-bg" cx="100" cy="100" r="80" />
                        <circle class="progress-ring-fill internship-ring" cx="100" cy="100" r="80" 
                                stroke="url(#internshipGradient)"
                                stroke-dasharray="502.65" stroke-dashoffset="502.65" />
                    </svg>
                    <div class="progress-text">
                        <div class="progress-value internship-percent">{{ $internshipData['progress_percent'] }}%</div>
                        <div class="progress-label"><i class="bi bi-calendar-week-fill"></i> Journey</div>
                    </div>
                </div>
                <div class="ring-details">
                    <div class="ring-detail-header"><i class="bi bi-calendar-check"></i> Internship Journey</div>
                    <div class="ring-stats">
                        <span class="completed">{{ $internshipData['elapsed_days'] }}</span>
                        <span class="separator">/</span>
                        <span class="total">{{ $internshipData['total_days'] }}</span>
                        <span class="unit">days</span>
                    </div>
                    <div class="ring-progress-bar">
                        <div class="ring-progress-fill internship-progress-bar" style="width: {{ $internshipData['progress_percent'] }}%"></div>
                    </div>
                    <div class="ring-stats-sub">
                        <span class="done"><i class="bi bi-calendar-check"></i> {{ $internshipData['remaining_days'] }} Days Left</span>
                        <span class="pending"><i class="bi bi-calendar-heart"></i> Ends {{ $internshipData['end_date']->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="ring-status-badge internship-status">
                    @if($internshipData['remaining_days'] <= 30)
                        <i class="bi bi-rocket-takeoff-fill"></i> Final Stretch
                    @elseif($internshipData['remaining_days'] <= 60)
                        <i class="bi bi-graph-up"></i> Halfway There
                    @else
                        <i class="bi bi-compass"></i> On Track
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="stats-grid mb-4">
        @foreach($statItems as $item)
        <div class="stat-card-modern animate-card" style="--stat-color: {{ $item['color'] }}; animation-delay: {{ 0.4 + ($loop->index * 0.05) }}s;">
            <div class="stat-icon-wrapper">
                <i class="{{ $item['icon'] }} stat-icon-modern" style="color: {{ $item['color'] }}"></i>
            </div>
            <div class="stat-value-modern">{{ $item['value'] }}</div>
            <div class="stat-label-modern">{{ $item['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ABOUT SECTION --}}
    @if($intern->bio)
    <div class="about-section animate-card" style="animation-delay: 0.6s;">
        <div class="about-icon"><i class="bi bi-quote"></i></div>
        <h3 class="about-title">About Me</h3>
        <p class="about-quote">{{ $intern->bio }}</p>
    </div>
    @endif

    {{-- SKILLS SECTION --}}
    @if($skills->count() > 0)
    <div class="skills-section animate-card" style="animation-delay: 0.65s;">
        <div class="skills-header">
            <div class="skills-icon"><i class="bi bi-code-square"></i></div>
            <h3 class="skills-title">Technical Arsenal</h3>
        </div>
        <div>
            @foreach($skills as $skill)
                <span class="skill-tag-advanced"><i class="bi bi-check-circle-fill"></i> {{ $skill }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FEATURED PROJECTS SECTION --}}
    @if($projects->count() > 0)
    <div class="projects-section animate-card" style="animation-delay: 0.7s;">
        <div class="skills-header">
            <div class="skills-icon" style="background: linear-gradient(135deg, #3b82f6, #1e40af);"><i class="bi bi-folder-open"></i></div>
            <h3 class="skills-title">Featured Projects</h3>
        </div>
        <div class="row g-3">
            @foreach($projects as $project)
            <div class="col-md-6 col-lg-4">
                <div class="project-card-modern">
                    <div class="project-icon-modern"><i class="bi bi-folder-fill"></i></div>
                    <h6 class="project-title-modern">{{ $project->title ?? 'Project' }}</h6>
                    @if(!empty($project->end_date))
                    <p class="project-date-modern"><i class="bi bi-calendar-check"></i> {{ \Carbon\Carbon::parse($project->end_date)->format('M Y') }}</p>
                    @endif
                    <p class="project-desc-modern">{{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 100) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="projects-section animate-card" style="animation-delay: 0.7s;">
        <div class="skills-header">
            <div class="skills-icon" style="background: linear-gradient(135deg, #3b82f6, #1e40af);"><i class="bi bi-folder-open"></i></div>
            <h3 class="skills-title">Featured Projects</h3>
        </div>
        <div class="empty-state-modern"><i class="bi bi-inbox"></i><p class="mb-0">No projects to display yet.</p></div>
    </div>
    @endif

    {{-- CERTIFICATES SECTION --}}
    @if($certificates->count() > 0)
    <div class="certificates-section animate-card" style="animation-delay: 0.75s;">
        <div class="skills-header">
            <div class="skills-icon" style="background: linear-gradient(135deg, #f59e0b, #ea580c);"><i class="bi bi-award"></i></div>
            <h3 class="skills-title">Certificates & Achievements</h3>
        </div>
        <div class="row g-3">
            @foreach($certificates as $cert)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="cert-card-modern">
                    <div class="cert-icon-modern"><i class="bi bi-trophy-fill"></i></div>
                    <h6 class="cert-title-modern">{{ $cert->title ?? 'Certificate' }}</h6>
                    <p class="cert-date-modern">{{ \Carbon\Carbon::parse($cert->created_at)->format('M Y') }}</p>
                    @if(!empty($cert->file_path))
                    <a href="{{ asset($cert->file_path) }}" class="btn-download-modern" download><i class="bi bi-download"></i> Download</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="certificates-section animate-card" style="animation-delay: 0.75s;">
        <div class="skills-header">
            <div class="skills-icon" style="background: linear-gradient(135deg, #f59e0b, #ea580c);"><i class="bi bi-award"></i></div>
            <h3 class="skills-title">Certificates & Achievements</h3>
        </div>
        <div class="empty-state-modern"><i class="bi bi-trophy"></i><p class="mb-0">No certificates available yet.</p></div>
    </div>
    @endif

    {{-- BADGES SECTION --}}
    <div class="badges-section animate-card" style="animation-delay: 0.8s;">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="skills-icon" style="background: linear-gradient(135deg, #f59e0b, #ea580c);"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <h3 class="skills-title mb-0">Achievement Badges</h3>
                    <p class="text-muted small mb-0">Collect badges by completing milestones</p>
                </div>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $badgesData['earned_count'] }}/{{ $badgesData['total_count'] }} Earned</span>
        </div>
        <div class="row g-3">
            @foreach($badgesData['badges'] as $badge)
            <div class="col-4 col-md-3 col-lg-2">
                <div class="badge-card-modern {{ $badge['earned'] ? 'earned' : 'locked' }}">
                    <div class="badge-icon-modern text-{{ $badge['color'] }}"><i class="bi {{ $badge['icon'] }}"></i></div>
                    <div class="small fw-semibold">{{ $badge['name'] }}</div>
                    @if(!$badge['earned'])
                    <div class="small text-muted"><i class="bi bi-lock-fill"></i> Locked</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const circumference = 2 * Math.PI * 80;
    
    // Tasks Ring Animation
    const taskPercent = {{ $taskRate }};
    const taskRing = document.querySelector('.tasks-ring');
    if (taskRing) {
        taskRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            taskRing.style.strokeDashoffset = circumference - (taskPercent / 100) * circumference;
        }, 100);
    }
    
    // Projects Ring Animation
    const projectPercent = {{ $projectRate }};
    const projectRing = document.querySelector('.projects-ring');
    if (projectRing) {
        projectRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            projectRing.style.strokeDashoffset = circumference - (projectPercent / 100) * circumference;
        }, 200);
    }
    
    // Internship Ring Animation
    const internshipPercent = {{ $internshipData['progress_percent'] }};
    const internshipRing = document.querySelector('.internship-ring');
    if (internshipRing) {
        internshipRing.style.strokeDashoffset = circumference;
        setTimeout(() => {
            internshipRing.style.strokeDashoffset = circumference - (internshipPercent / 100) * circumference;
        }, 300);
    }
});
</script>
@endsection