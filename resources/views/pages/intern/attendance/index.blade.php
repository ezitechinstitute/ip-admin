@extends('layouts/layoutMaster')

@section('title', 'My Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Check In/Out Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-4">
                    @if(!$todayAttendance)
                        <form action="{{ route('intern.attendance.checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="ti ti-login me-2"></i> Check In
                            </button>
                        </form>
                    @elseif($todayAttendance && !$todayAttendance->end_shift)
                        <div class="alert alert-info mb-3">
                            <i class="ti ti-clock me-2"></i> Checked in at <strong>{{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }}</strong>
                        </div>
                        <form action="{{ route('intern.attendance.checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg px-5">
                                <i class="ti ti-logout me-2"></i> Check Out
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <i class="ti ti-check-circle me-2"></i>
                            <strong>Check In:</strong> {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }} |
                            <strong>Check Out:</strong> {{ \Carbon\Carbon::parse($todayAttendance->end_shift)->format('h:i A') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-0 text-primary">{{ $stats['total_days'] }}</h2>
                    <small class="text-muted">Total Days</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-0 text-success">{{ $stats['present_days'] }}</h2>
                    <small class="text-muted">Present</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-0 text-danger">{{ $stats['absent_days'] }}</h2>
                    <small class="text-muted">Absent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="mb-0 text-info">{{ $stats['attendance_percentage'] }}%</h2>
                    <small class="text-muted">Attendance Rate</small>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-info" style="width: {{ $stats['attendance_percentage'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Professional Calendar View with Month Navigation --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">
                <i class="ti ti-calendar-month me-2 text-primary"></i>
                Attendance Calendar
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse(request()->get('month', '2026-04'))->subMonth()->format('Y-m')]) }}" 
                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="ti ti-chevron-left"></i> Previous
                </a>
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill">
                    <i class="ti ti-calendar me-1"></i> 
                    {{ \Carbon\Carbon::parse(request()->get('month', '2026-04'))->format('F Y') }}
                </span>
                <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse(request()->get('month', '2026-04'))->addMonth()->format('Y-m')]) }}" 
                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    Next <i class="ti ti-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-light">
                    <tr>
                        <th class="py-2">Sun</th>
                        <th class="py-2">Mon</th>
                        <th class="py-2">Tue</th>
                        <th class="py-2">Wed</th>
                        <th class="py-2">Thu</th>
                        <th class="py-2">Fri</th>
                        <th class="py-2">Sat</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $selectedMonth = request()->get('month', date('Y-m'));
                        $selectedDate = \Carbon\Carbon::parse($selectedMonth . '-01');
                        $year = $selectedDate->year;
                        $month = $selectedDate->month;
                        
                        $daysInMonth = $selectedDate->daysInMonth;
                        $firstDayOfWeek = $selectedDate->dayOfWeek;
                        $today = \Carbon\Carbon::today();
                        $isCurrentMonth = ($today->year == $year && $today->month == $month);
                        
                        // Build attendance lookup from your data
                        $attendanceDates = [];
                        foreach($attendance as $record) {
                            $recordDate = date('Y-m-d', strtotime($record->start_shift));
                            $attendanceDates[$recordDate] = $record->status;
                        }
                        
                        $day = 1;
                        $calendar = [];
                        
                        for ($row = 0; $row < 6; $row++) {
                            $week = [];
                            for ($col = 0; $col < 7; $col++) {
                                if ($row == 0 && $col < $firstDayOfWeek) {
                                    $week[] = null;
                                } elseif ($day <= $daysInMonth) {
                                    $week[] = $day;
                                    $day++;
                                } else {
                                    $week[] = null;
                                }
                            }
                            $calendar[] = $week;
                            if ($day > $daysInMonth) break;
                        }
                    @endphp
                    
                    @foreach($calendar as $week)
                        <tr>
                            @foreach($week as $colIndex => $dayNum)
                                @if($dayNum)
                                    @php
                                        $dateStr = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($dayNum, 2, '0', STR_PAD_LEFT);
                                        $status = isset($attendanceDates[$dateStr]) ? $attendanceDates[$dateStr] : null;
                                        $isToday = ($isCurrentMonth && $dayNum == $today->day);
                                        $isWeekend = ($colIndex == 0 || $colIndex == 6);
                                        
                                        if ($status == 1) {
                                            $bgClass = 'bg-success';
                                            $icon = '✓';
                                            $text = 'Present';
                                        } elseif ($status == 0) {
                                            $bgClass = 'bg-danger';
                                            $icon = '✗';
                                            $text = 'Absent';
                                        } elseif ($isWeekend) {
                                            $bgClass = 'bg-secondary bg-opacity-25';
                                            $icon = '●';
                                            $text = 'Weekend';
                                        } else {
                                            $bgClass = 'bg-secondary bg-opacity-25';
                                            $icon = '○';
                                            $text = 'No Record';
                                        }
                                    @endphp
                                    <td class="p-2 align-middle {{ $isToday ? 'bg-primary bg-opacity-10' : '' }}" style="height: 85px;">
                                        <div class="fw-bold {{ $isToday ? 'text-primary' : '' }}">{{ $dayNum }}</div>
                                        <div class="mt-2">
                                            <span class="badge {{ $bgClass }} rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                                {{ $icon }}
                                            </span>
                                        </div>
                                        <div><small class="text-muted" style="font-size: 9px;">{{ $text }}</small></div>
                                    </td>
                                @else
                                    <td class="bg-light" style="height: 85px;">—</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center gap-3 mt-3 pt-2 border-top">
            <div class="d-flex align-items-center gap-1"><span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span><small>Present</small></div>
            <div class="d-flex align-items-center gap-1"><span class="bg-danger rounded-circle" style="width: 8px; height: 8px;"></span><small>Absent</small></div>
            <div class="d-flex align-items-center gap-1"><span class="bg-secondary bg-opacity-25 rounded-circle" style="width: 8px; height: 8px;"></span><small>Weekend</small></div>
            <div class="d-flex align-items-center gap-1"><span class="bg-secondary bg-opacity-25 rounded-circle" style="width: 8px; height: 8px; opacity: 0.5;"></span><small>No Record</small></div>
            <div class="d-flex align-items-center gap-1"><span class="bg-primary rounded-circle" style="width: 8px; height: 8px;"></span><small>Today</small></div>
        </div>
    </div>
</div>
    
    {{-- Attendance History Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ti ti-history me-2 text-primary"></i>
                Attendance History
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($attendance->count() > 0)
                            @foreach($attendance as $index => $record)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('d M, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('l') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}</td>
                                <td>{{ $record->end_shift ? \Carbon\Carbon::parse($record->end_shift)->format('h:i A') : '—' }}</td>
                                <td>{{ ($record->duration && $record->duration > 0) ? number_format($record->duration, 1) . ' hrs' : '—' }}</td>
                                <td>
                                    @if($record->status == 1)
                                        <span class="badge bg-success">Present</span>
                                    @else
                                        <span class="badge bg-danger">Absent</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="ti ti-calendar-off ti-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No attendance records found</p>
                                    <small>Click "Check In" to start</small>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection