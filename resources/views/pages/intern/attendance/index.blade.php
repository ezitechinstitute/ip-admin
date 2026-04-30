@extends('layouts/layoutMaster')

@section('title', 'Attendance Dashboard')

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

    .premium-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    .stat-card-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 1rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.6);
        height: 100%;
    }

    .stat-card-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--stat-gradient);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .stat-card-premium:hover::after {
        transform: scaleX(1);
    }

    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -12px rgba(0, 0, 0, 0.15);
    }

    .stat-icon-premium {
        width: 45px;
        height: 45px;
        background: var(--stat-bg);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }

    .stat-value-premium {
        font-size: 1.8rem;
        font-weight: 800;
        background: var(--stat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .stat-label-premium {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c86a3;
        margin-top: 0.25rem;
    }

    .badge-custom {
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-present { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-absent { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }
    .badge-onsite { background: rgba(59,130,246,0.15); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3); }
    .badge-remote { background: rgba(139,92,246,0.15); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.3); }

    .attendance-action-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(12px);
        border-radius: 1.5rem;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.5);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
    }

    .attendance-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px -12px rgba(0, 0, 0, 0.1);
    }

    .time-display {
        font-size: 2.2rem;
        font-weight: 800;
        font-family: monospace;
        color: #1e293b;
        letter-spacing: 2px;
    }

    .date-display {
        font-size: 0.9rem;
        color: #6c86a3;
    }

    .btn-punch {
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .warning-box {
        background: linear-gradient(135deg, rgba(245,158,11,0.1), rgba(245,158,11,0.05));
        border-left: 4px solid #f59e0b;
        border-radius: 0.75rem;
        padding: 1rem;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.25rem;
    }

    .calendar-weekday {
        text-align: center;
        padding: 0.5rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #6c86a3;
    }

    .calendar-day {
        text-align: center;
        padding: 0.5rem;
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        position: relative;
        min-height: 60px;
        background: rgba(255, 255, 255, 0.6);
        cursor: pointer;
    }

    .calendar-day:hover {
        background: rgba(59,130,246,0.1);
        transform: scale(1.02);
    }

    .calendar-day-number {
        font-size: 0.8rem;
        font-weight: 600;
        color: #1e293b;
    }

    .calendar-day-today {
        background: rgba(59,130,246,0.15);
        border: 1px solid rgba(59,130,246,0.3);
    }

    .calendar-status-icon {
        font-size: 0.55rem;
        margin-top: 0.2rem;
    }

    .calendar-empty {
        background: transparent;
        min-height: 60px;
    }

    .history-table {
        width: 100%;
        margin-bottom: 0;
    }

    .history-table thead th {
        background: rgba(255, 255, 255, 0.9);
        padding: 0.75rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .history-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .history-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .history-table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .quick-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-stat-item {
        flex: 1;
        min-width: 100px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    .timeline-view {
        position: relative;
    }

    .timeline-year-header {
        margin: 1rem 0;
    }

    .timeline-year-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
    }

    .timeline-year-badge {
        background: rgba(59,130,246,0.1);
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .timeline-month-header {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 2px solid rgba(59,130,246,0.2);
        margin-top: 1rem;
    }

    .timeline-month-header:first-child {
        margin-top: 0;
    }

    .timeline-item {
        position: relative;
        padding: 1rem 1rem 1rem 2rem;
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        margin-bottom: 1rem;
        margin-left: 80px;
        transition: all 0.3s ease;
        border-left: 4px solid;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .timeline-item:hover {
        transform: translateX(8px);
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.15);
    }

    .timeline-badge {
        position: absolute;
        left: -12px;
        top: 20px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 3px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .timeline-date-badge {
        position: absolute;
        left: -80px;
        top: 15px;
        min-width: 65px;
        text-align: center;
        background: white;
        border-radius: 10px;
        padding: 0.25rem 0.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .timeline-date-day {
        font-size: 1rem;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }

    .timeline-date-month {
        font-size: 0.6rem;
        font-weight: 600;
        color: #6c86a3;
        text-transform: uppercase;
    }

    .timeline-work-hours {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    #timelineContainer {
        scroll-behavior: smooth;
        max-height: 550px;
        overflow-y: auto;
        padding-right: 10px;
    }

    #timelineContainer::-webkit-scrollbar {
        width: 6px;
    }

    #timelineContainer::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }

    #timelineContainer::-webkit-scrollbar-thumb {
        background: rgba(59,130,246,0.3);
        border-radius: 10px;
    }

    #timelineContainer::-webkit-scrollbar-thumb:hover {
        background: rgba(59,130,246,0.5);
    }

    .scroll-top-btn {
        position: sticky;
        bottom: 20px;
        left: calc(100% - 60px);
        background: rgba(59,130,246,0.9);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        color: white;
        box-shadow: 0 4px 15px rgba(59,130,246,0.3);
        margin-top: 10px;
        float: right;
    }

    .scroll-top-btn:hover {
        opacity: 1;
        transform: translateY(-3px);
    }

    @media (max-width: 768px) {
        .stat-card-premium {
            padding: 0.75rem;
        }
        .stat-value-premium {
            font-size: 1.3rem;
        }
        .stat-icon-premium {
            width: 35px;
            height: 35px;
        }
        .quick-stats {
            flex-wrap: wrap;
        }
        .quick-stat-item {
            min-width: calc(50% - 0.5rem);
        }
        .time-display {
            font-size: 1.5rem;
        }
        .history-table thead th,
        .history-table tbody td {
            padding: 0.5rem;
            font-size: 0.7rem;
        }
        .calendar-day {
            min-height: 45px;
            padding: 0.25rem;
        }
        .calendar-day-number {
            font-size: 0.7rem;
        }
        .timeline-item {
            margin-left: 75px;
            padding: 0.75rem 0.75rem 0.75rem 1.5rem;
        }
        .timeline-date-badge {
            left: -75px;
            min-width: 60px;
        }
        .timeline-work-hours {
            margin-top: 0.5rem;
            width: 100%;
            justify-content: center;
        }
        .timeline-month-header {
            flex-wrap: wrap;
        }
    }


    /* Future Features Cards */
.future-feature-card {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(8px);
    border-radius: 1rem;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.5);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.future-feature-card:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.1);
    border-color: #8b5cf6;
}

.future-feature-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(139,92,246,0.05));
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}

.future-feature-icon i {
    font-size: 1.5rem;
    color: #8b5cf6;
}

.coming-soon-badge {
    display: inline-block;
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: white;
    font-size: 0.6rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
    margin-top: 0.5rem;
}
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
            <div>
                <strong class="d-block text-success">Success!</strong>
                <span class="text-success">{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
            <div>
                <strong class="d-block text-danger">Error!</strong>
                <span class="text-danger">{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-calendar-check-fill text-primary me-2"></i>Attendance Dashboard
            </h4>
            <p class="text-muted small mb-0">Track your daily attendance and work hours</p>
        </div>
        <div>
            <span class="badge-custom {{ $stats['internship_type'] == 'Onsite' ? 'badge-onsite' : 'badge-remote' }}">
                <i class="bi bi-{{ $stats['internship_type'] == 'Onsite' ? 'building' : 'wifi' }} me-1"></i>
                {{ $stats['internship_type'] }} Intern
            </span>
        </div>
    </div>

    {{-- Check-in/out Card with GPS for Onsite --}}
    <div class="attendance-action-card animate-card" style="animation-delay: 0.1s;">
        <div class="row align-items-center">
            <div class="col-md-5 text-center text-md-start mb-3 mb-md-0">
                <div class="time-display" id="liveClock">--:--:--</div>
                <div class="date-display mt-1">{{ now()->format('l, F j, Y') }}</div>
            </div>
            <div class="col-md-7 text-center">
                @if($stats['internship_type'] == 'Remote')
                    {{-- Remote Intern - Time based --}}
                    @php
                        $now = \Carbon\Carbon::now();
                        $currentHour = (int) $now->format('H');
                        $canCheckin = ($currentHour >= 9 && $currentHour < 18);
                        $checkinMessage = '';
                        if ($currentHour < 9) {
                            $checkinMessage = '⏰ Working hours start at 9:00 AM';
                        } elseif ($currentHour >= 18) {
                            $checkinMessage = '⏰ Working hours ended at 6:00 PM';
                        } else {
                            $checkinMessage = '✅ You can check in now';
                        }
                    @endphp
                    
                    @if(!$todayAttendance)
                        @if($canCheckin)
                            <form action="{{ route('intern.attendance.checkin') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-punch">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Punch In
                                </button>
                            </form>
                            <div class="mt-2 small text-muted">
                                <i class="bi bi-info-circle"></i> Working hours: 9:00 AM - 6:00 PM
                            </div>
                        @else
                            <div class="warning-box">
                                <i class="bi bi-clock-history text-warning me-2"></i>
                                <span class="fw-semibold">{{ $checkinMessage }}</span>
                            </div>
                        @endif
                    @elseif($todayAttendance && !$todayAttendance->end_shift)
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <div class="badge bg-success bg-opacity-10 text-success px-4 py-2">
                                <i class="bi bi-clock-history me-1"></i> 
                                In: {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }}
                            </div>
                            <form action="{{ route('intern.attendance.checkout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-punch">
                                    <i class="bi bi-box-arrow-right me-2"></i> Punch Out
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-success rounded-4 border-0 mb-0 d-inline-block">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Completed | In: {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }} | 
                            Out: {{ \Carbon\Carbon::parse($todayAttendance->end_shift)->format('h:i A') }}
                        </div>
                    @endif
                    
                @else
                    {{-- Onsite Intern - GPS Location required --}}
                    @if(!$todayAttendance)
                        <div class="mb-3">
                            <div id="locationStatus" class="small mb-2"></div>
                            <div id="locationDebug" class="small text-muted"></div>
                        </div>
                        
                        <form action="{{ route('intern.attendance.checkin') }}" method="POST" id="checkinForm">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <button type="submit" class="btn btn-primary btn-punch w-100" id="punchInBtn" disabled>
                                <i class="bi bi-box-arrow-in-right me-2"></i> Getting Location...
                            </button>
                        </form>
                        
                        <div class="mt-2 small text-muted">
                            <i class="bi bi-geo-alt-fill me-1"></i> Office radius: 100m
                        </div>
                        
                    @elseif($todayAttendance && !$todayAttendance->end_shift)
                        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
                            <div class="badge bg-success bg-opacity-10 text-success px-4 py-2">
                                <i class="bi bi-clock-history me-1"></i> 
                                In: {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }}
                                @if($todayAttendance->checkin_method)
                                    <small class="d-block text-muted">via {{ strtoupper($todayAttendance->checkin_method) }}</small>
                                @endif
                            </div>
                            <form action="{{ route('intern.attendance.checkout') }}" method="POST">
                                @csrf
                                <input type="hidden" name="latitude" id="checkout_latitude">
                                <input type="hidden" name="longitude" id="checkout_longitude">
                                <button type="submit" class="btn btn-danger btn-punch">
                                    <i class="bi bi-box-arrow-right me-2"></i> Punch Out
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-success rounded-4 border-0 mb-0 d-inline-block">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Completed | In: {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }} | 
                            Out: {{ \Carbon\Carbon::parse($todayAttendance->end_shift)->format('h:i A') }}
                            @if($todayAttendance->checkin_method)
                                <small class="d-block">via {{ strtoupper($todayAttendance->checkin_method) }}</small>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    {{-- Future Features Section --}}
<div class="premium-card animate-card" style="animation-delay: 0.6s;">
    <div class="p-3 border-bottom bg-transparent">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-stars text-primary fs-4"></i>
            <h5 class="fw-bold mb-0">Features Coming Soon</h5>
            <span class="badge bg-warning bg-opacity-10 text-warning ms-2">In Development</span>
        </div>
        <p class="text-muted small mb-0 mt-1">Exciting new features coming to enhance your attendance experience</p>
    </div>
    
    <div class="p-3">
        <div class="row g-3">
            @if($stats['internship_type'] == 'Onsite')
            {{-- Onsite Future Features --}}
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h6 class="fw-bold mb-1">QR Code Check-in</h6>
                    <p class="small text-muted mb-2">Scan office QR code for instant verification</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-camera-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Face Recognition</h6>
                    <p class="small text-muted mb-2">AI-powered face verification on check-in/out</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-wifi"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Office Wi-Fi Auto</h6>
                    <p class="small text-muted mb-2">Automatic check-in via office network</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            @else
            {{-- Remote Future Features --}}
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-camera-reels-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Screen Capture</h6>
                    <p class="small text-muted mb-2">Random screen capture during work hours</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-camera-video-fill"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Video Verification</h6>
                    <p class="small text-muted mb-2">Random video call verification</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="future-feature-card">
                    <div class="future-feature-icon">
                        <i class="bi bi-activity"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Activity Tracker</h6>
                    <p class="small text-muted mb-2">Keyboard/mouse activity monitoring</p>
                    <span class="coming-soon-badge">Coming Soon</span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4 animate-card" style="animation-delay: 0.2s;">
        <div class="col-md-3 col-6">
            <div class="stat-card-premium" style="--stat-gradient: linear-gradient(135deg, #3b82f6, #1e40af); --stat-bg: rgba(59,130,246,0.1)">
                <div class="stat-icon-premium" style="background: rgba(59,130,246,0.1)">
                    <i class="bi bi-calendar-range fs-3" style="color: #3b82f6"></i>
                </div>
                <div class="stat-value-premium">{{ $stats['total_days'] }}</div>
                <div class="stat-label-premium">Total Days</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-premium" style="--stat-gradient: linear-gradient(135deg, #10b981, #047857); --stat-bg: rgba(16,185,129,0.1)">
                <div class="stat-icon-premium" style="background: rgba(16,185,129,0.1)">
                    <i class="bi bi-check-circle fs-3" style="color: #10b981"></i>
                </div>
                <div class="stat-value-premium">{{ $stats['present_days'] }}</div>
                <div class="stat-label-premium">Present</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-premium" style="--stat-gradient: linear-gradient(135deg, #ef4444, #b91c1c); --stat-bg: rgba(239,68,68,0.1)">
                <div class="stat-icon-premium" style="background: rgba(239,68,68,0.1)">
                    <i class="bi bi-x-circle fs-3" style="color: #ef4444"></i>
                </div>
                <div class="stat-value-premium">{{ $stats['absent_days'] }}</div>
                <div class="stat-label-premium">Absent</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card-premium" style="--stat-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9); --stat-bg: rgba(139,92,246,0.1)">
                <div class="stat-icon-premium" style="background: rgba(139,92,246,0.1)">
                    <i class="bi bi-graph-up fs-3" style="color: #8b5cf6"></i>
                </div>
                <div class="stat-value-premium">{{ $stats['attendance_percentage'] }}%</div>
                <div class="stat-label-premium">Attendance Rate</div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Row --}}
    <div class="quick-stats mb-4 animate-card" style="animation-delay: 0.3s;">
        <div class="quick-stat-item">
            <i class="bi bi-calendar-heart text-primary fs-4"></i>
            <div class="fw-bold">{{ $stats['present_days'] }}/{{ $stats['total_days'] }}</div>
            <small class="text-muted">Present Ratio</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-star-fill text-warning fs-4"></i>
            <div class="fw-bold">{{ $stats['attendance_percentage'] }}%</div>
            <small class="text-muted">Completion</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-trophy-fill text-success fs-4"></i>
            <div class="fw-bold">{{ $stats['present_days'] }}</div>
            <small class="text-muted">Days Present</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-calendar-week text-info fs-4"></i>
            <div class="fw-bold">{{ $stats['total_days'] }}</div>
            <small class="text-muted">Total Days</small>
        </div>
    </div>

    {{-- Calendar Section --}}
    <div class="premium-card animate-card" style="animation-delay: 0.4s;">
        <div class="p-3 border-bottom bg-transparent">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Attendance Calendar</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse($currentMonthParam)->subMonth()->format('Y-m')]) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-chevron-left"></i> Prev
                    </a>
                    <span class="fw-semibold px-2">{{ \Carbon\Carbon::parse($currentMonthParam)->format('F Y') }}</span>
                    <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse($currentMonthParam)->addMonth()->format('Y-m')]) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="p-3">
            @php
                $selectedDate = \Carbon\Carbon::parse($currentMonthParam . '-01');
                $year = $selectedDate->year;
                $month = $selectedDate->month;
                $daysInMonth = $selectedDate->daysInMonth;
                $firstDayOfWeek = $selectedDate->dayOfWeek;
                
                $attendanceDates = [];
                foreach($allAttendance as $record) {
                    $recordDate = date('Y-m-d', strtotime($record->start_shift));
                    $attendanceDates[$recordDate] = $record->status;
                }
                
                $weeks = [];
                $week = [];
                $day = 1;
                
                for ($i = 0; $i < $firstDayOfWeek; $i++) {
                    $week[] = null;
                }
                
                while ($day <= $daysInMonth) {
                    $week[] = $day;
                    $day++;
                    if (count($week) == 7) {
                        $weeks[] = $week;
                        $week = [];
                    }
                }
                
                if (count($week) > 0) {
                    while (count($week) < 7) {
                        $week[] = null;
                    }
                    $weeks[] = $week;
                }
            @endphp

            <div class="calendar-grid">
                <div class="calendar-weekday text-danger">Sun</div>
                <div class="calendar-weekday">Mon</div>
                <div class="calendar-weekday">Tue</div>
                <div class="calendar-weekday">Wed</div>
                <div class="calendar-weekday">Thu</div>
                <div class="calendar-weekday">Fri</div>
                <div class="calendar-weekday text-success">Sat</div>
                
                @foreach($weeks as $week)
                    @foreach($week as $dayNum)
                        @php
                            $dateStr = $dayNum ? "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($dayNum, 2, '0', STR_PAD_LEFT) : null;
                            $status = $dateStr ? ($attendanceDates[$dateStr] ?? null) : null;
                            $isToday = $dateStr == date('Y-m-d');
                        @endphp
                        @if($dayNum)
                            <div class="calendar-day {{ $isToday ? 'calendar-day-today' : '' }}">
                                <div class="calendar-day-number">{{ $dayNum }}</div>
                                @if($status == 1)
                                    <div class="calendar-status-icon text-success">
                                        <i class="bi bi-check-circle-fill"></i> Present
                                    </div>
                                @elseif($status === 0)
                                    <div class="calendar-status-icon text-danger">
                                        <i class="bi bi-x-circle-fill"></i> Absent
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="calendar-day calendar-empty"></div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

   {{-- Attendance History - Timeline View --}}
<div class="premium-card animate-card" style="animation-delay: 0.5s;">
    <div class="p-3 border-bottom bg-transparent">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Attendance Timeline</h5>
            <div class="d-flex gap-2">
                <select id="timelineYearFilter" class="form-select form-select-sm rounded-pill" style="width: auto;">
                    <option value="all">📅 All Years</option>
                    @php
                        $years = $allAttendance->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->start_shift)->format('Y');
                        })->keys();
                    @endphp
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
                <select id="timelineStatusFilter" class="form-select form-select-sm rounded-pill" style="width: auto;">
                    <option value="all">📋 All Status</option>
                    <option value="1">✅ Present Only</option>
                    <option value="0">❌ Absent Only</option>
                </select>
            </div>
        </div>
    </div>
    
    <div id="timelineContainer" style="max-height: 550px; overflow-y: auto; padding: 1.5rem;">
        <div class="timeline-view" id="timelineItems">
            @php
                $groupedAttendance = $allAttendance->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->start_shift)->format('F Y');
                });
                $lastYear = null;
            @endphp
            
            @foreach($groupedAttendance as $monthName => $monthRecords)
                @php
                    $currentYear = \Carbon\Carbon::parse($monthRecords->first()->start_shift)->format('Y');
                    $showYearHeader = ($lastYear !== $currentYear);
                @endphp
                
                @if($showYearHeader)
                    <div class="timeline-year-header mt-3 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="timeline-year-line flex-grow-1"></div>
                            <span class="timeline-year-badge px-3 py-1 rounded-pill">{{ $currentYear }}</span>
                            <div class="timeline-year-line flex-grow-1"></div>
                        </div>
                    </div>
                    @php $lastYear = $currentYear; @endphp
                @endif
                
                <div class="timeline-month-group mb-4">
                    <div class="timeline-month-header mb-3">
                        <i class="bi bi-calendar3 text-primary me-2"></i>
                        <span class="fw-bold">{{ $monthName }}</span>
                        <span class="badge bg-light text-dark ms-2">
                            {{ $monthRecords->count() }} days
                        </span>
                    </div>
                    
                    @foreach($monthRecords as $record)
                        @php
                            $date = \Carbon\Carbon::parse($record->start_shift);
                            $statusColor = $record->status ? '#10b981' : '#ef4444';
                            $statusIcon = $record->status ? 'check-circle' : 'x-circle';
                            $statusText = $record->status ? 'Present' : 'Absent';
                            $bgColor = $record->status ? 'rgba(16,185,129,0.08)' : 'rgba(239,68,68,0.08)';
                        @endphp
                        <div class="timeline-item attendance-timeline-item" 
                             data-year="{{ $date->format('Y') }}" 
                             data-status="{{ $record->status }}"
                             data-month="{{ $date->format('Y-m') }}"
                             style="border-left-color: {{ $statusColor }}; background: {{ $bgColor }}">
                            <div class="timeline-badge" style="border-color: {{ $statusColor }}; background: {{ $statusColor }}20"></div>
                            
                            <div class="timeline-date-badge">
                                <div class="timeline-date-day">{{ $date->format('d') }}</div>
                                <div class="timeline-date-month">{{ $date->format('M') }}</div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                        <i class="bi bi-calendar-check" style="color: {{ $statusColor }}"></i>
                                        <span class="fw-semibold">{{ $date->format('l, F j, Y') }}</span>
                                        <span class="badge-custom {{ $record->status ? 'badge-present' : 'badge-absent' }}">
                                            <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex gap-3 flex-wrap">
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-in-right text-primary small"></i>
                                            <small class="text-muted">Check In: <strong>{{ $date->format('h:i A') }}</strong></small>
                                        </div>
                                        @if($record->end_shift)
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-right text-danger small"></i>
                                            <small class="text-muted">Check Out: <strong>{{ \Carbon\Carbon::parse($record->end_shift)->format('h:i A') }}</strong></small>
                                        </div>
                                        @endif
                                        @if($record->duration)
                                        <div class="d-flex align-items-center gap-1">
                                            <i class="bi bi-hourglass-split text-warning small"></i>
                                            <small class="text-muted">Duration: <strong>{{ number_format($record->duration, 2) }} hours</strong></small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="timeline-work-hours" style="background: {{ $statusColor }}10; border: 1px solid {{ $statusColor }}20;">
                                    <i class="bi bi-clock" style="color: {{ $statusColor }}"></i>
                                    <span class="fw-bold" style="color: {{ $statusColor }}">
                                        @if($record->duration)
                                            {{ number_format($record->duration, 2) }}h
                                        @else
                                            {{ $record->status ? 'Full Day' : 'Absent' }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            
            @if($allAttendance->count() == 0)
            <div class="text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No attendance records found</p>
                @if($stats['internship_type'] == 'Remote')
                    <small class="text-muted">Remote interns do not require attendance tracking.</small>
                @else
                    <small class="text-muted">Start your first check-in to track attendance</small>
                @endif
            </div>
            @endif
        </div>
    </div>
    
    <div class="p-3 border-top bg-transparent" id="timelineNoResults" style="display: none;">
        <div class="text-center py-3">
            <i class="bi bi-inbox fs-2 text-muted"></i>
            <p class="mt-2 text-muted mb-0">No records found for selected filter</p>
        </div>
    </div>
    
    <button class="scroll-top-btn" id="scrollToTopBtn" style="display: none;">
        <i class="bi bi-arrow-up fs-5"></i>
    </button>
</div>

<script>
// GPS Location Detection for Onsite Interns
@if($stats['internship_type'] == 'Onsite')
let watchId = null;
let currentLocation = null;

// Eziline Software House Office Coordinates
const OFFICE_LAT = 33.6145;   // Amna Plaza, near Radio Pakistan, Rawalpindi
const OFFICE_LON = 73.0589;
const OFFICE_RADIUS = 100;     // 100 meters

function calculateOfficeDistance(lat, lon) {
    const R = 6371000; // Earth radius in meters
    
    const dLat = (lat - OFFICE_LAT) * Math.PI / 180;
    const dLon = (lon - OFFICE_LON) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(OFFICE_LAT * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function getLocation() {
    if ("geolocation" in navigator) {
        document.getElementById('locationStatus').innerHTML = '<i class="bi bi-hourglass-split text-warning"></i> Getting your location...';
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy
                };
                
                document.getElementById('latitude').value = currentLocation.latitude;
                document.getElementById('longitude').value = currentLocation.longitude;
                
                // Calculate distance from Eziline Software House
                var distance = calculateOfficeDistance(currentLocation.latitude, currentLocation.longitude);
                
                if (distance <= OFFICE_RADIUS) {
                    document.getElementById('locationStatus').innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> Location verified - ' + distance.toFixed(0) + 'm from Eziline Office';
                    document.getElementById('locationStatus').className = 'text-success small';
                    document.getElementById('punchInBtn').disabled = false;
                    document.getElementById('punchInBtn').innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Punch In';
                } else {
                    document.getElementById('locationStatus').innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> You are ' + distance.toFixed(0) + 'm away from Eziline Office (Required: within 100m)';
                    document.getElementById('locationStatus').className = 'text-danger small';
                    document.getElementById('punchInBtn').disabled = true;
                    document.getElementById('punchInBtn').innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Out of Range';
                }
                
                document.getElementById('locationDebug').innerHTML = 'Lat: ' + currentLocation.latitude.toFixed(6) + ', Lon: ' + currentLocation.longitude.toFixed(6) + ' | Distance: ' + distance.toFixed(0) + 'm';
            },
            function(error) {
                console.log('GPS Error:', error);
                let errorMsg = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg = 'Location permission denied. Please enable GPS.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg = 'Location unavailable. Please check GPS.';
                        break;
                    case error.TIMEOUT:
                        errorMsg = 'Location timeout. Please try again.';
                        break;
                }
                document.getElementById('locationStatus').innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger"></i> ' + errorMsg;
                document.getElementById('locationStatus').className = 'text-danger small';
                document.getElementById('punchInBtn').disabled = true;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        document.getElementById('locationStatus').innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger"></i> GPS not supported';
        document.getElementById('punchInBtn').disabled = true;
    }
}

// Start location detection when page loads
document.addEventListener('DOMContentLoaded', function() {
    getLocation();
});

// Also get location for checkout if needed
@if($todayAttendance && !$todayAttendance->end_shift)
if (document.getElementById('checkoutForm')) {
    navigator.geolocation.getCurrentPosition(function(position) {
        document.getElementById('checkout_latitude').value = position.coords.latitude;
        document.getElementById('checkout_longitude').value = position.coords.longitude;
    });
}
@endif

@endif

// ============================================
// ATTENDANCE TIMELINE FILTERS
// ============================================
function updateTimelineDisplay() {
    const yearFilter = document.getElementById('timelineYearFilter')?.value || 'all';
    const statusFilter = document.getElementById('timelineStatusFilter')?.value || 'all';
    
    const timelineItems = document.querySelectorAll('.attendance-timeline-item');
    const monthGroups = document.querySelectorAll('.timeline-month-group');
    let visibleCount = 0;
    
    timelineItems.forEach(item => {
        const itemYear = item.dataset.year;
        const itemStatus = item.dataset.status;
        
        let show = true;
        
        if (yearFilter !== 'all' && itemYear !== yearFilter) {
            show = false;
        }
        
        if (statusFilter !== 'all' && itemStatus != statusFilter) {
            show = false;
        }
        
        if (show) {
            item.style.display = 'block';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Hide empty month groups
    monthGroups.forEach(group => {
        const visibleItems = group.querySelectorAll('.attendance-timeline-item[style="display: block;"], .attendance-timeline-item:not([style*="display: none"])');
        if (visibleItems.length === 0) {
            group.style.display = 'none';
        } else {
            group.style.display = 'block';
        }
    });
    
    const noResults = document.getElementById('timelineNoResults');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// Timeline filters event listeners
document.getElementById('timelineYearFilter')?.addEventListener('change', updateTimelineDisplay);
document.getElementById('timelineStatusFilter')?.addEventListener('change', updateTimelineDisplay);

// Scroll to top button
function setupScrollToTop() {
    const container = document.getElementById('timelineContainer');
    const scrollBtn = document.getElementById('scrollToTopBtn');
    
    if (!container || !scrollBtn) return;
    
    container.addEventListener('scroll', function() {
        if (container.scrollTop > 200) {
            scrollBtn.style.display = 'flex';
        } else {
            scrollBtn.style.display = 'none';
        }
    });
    
    scrollBtn.addEventListener('click', function() {
        container.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// Initialize timeline filters
document.addEventListener('DOMContentLoaded', function() {
    setupScrollToTop();
    updateTimelineDisplay();
});

// Live Clock
function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const clockElement = document.getElementById('liveClock');
    if (clockElement) {
        clockElement.textContent = time;
    }
}
setInterval(updateClock, 1000);
updateClock();

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

@endsection