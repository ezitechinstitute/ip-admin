
@extends('layouts/layoutMaster')
@section('title', 'My Attendance')

@section('content')
<div class="container-xxl container-p-y">

    {{-- HEADER --}}
    <div class="mb-4 text-center">
        <h2 class="fw-bold text-primary">Attendance Portal</h2>
    </div>

    {{-- CHECK IN / OUT CARD --}}
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body text-center py-5">

            @if(!$todayAttendance)
                <form action="{{ route('intern.attendance.checkin') }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-lg px-5 rounded-pill">
                        <i class="ti ti-login me-2"></i> Punch In
                    </button>
                </form>

            @elseif($todayAttendance && !$todayAttendance->end_shift)

                <h5 class="mb-3 text-success">
                    Checked in at {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }}
                </h5>

                <form action="{{ route('intern.attendance.checkout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-lg px-5 rounded-pill">
                        <i class="ti ti-logout me-2"></i> Punch Out
                    </button>
                </form>

            @else
                <div class="alert alert-success">
                    <strong>In:</strong> {{ \Carbon\Carbon::parse($todayAttendance->start_shift)->format('h:i A') }} |
                    <strong>Out:</strong> {{ \Carbon\Carbon::parse($todayAttendance->end_shift)->format('h:i A') }}
                </div>
            @endif

        </div>
    </div>

    {{-- STATS (MODERN CARDS) --}}
    <div class="row g-4 mb-4">

        <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden">

        <!-- subtle background accent -->
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10"></div>

        <div class="card-body d-flex align-items-center justify-content-between position-relative">

            <!-- LEFT CONTENT -->
            <div>
                <small class="text-muted fw-semibold d-block mb-1">Total Days</small>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['total_days'] }}</h3>
            </div>

            <!-- RIGHT ICON -->
            <div class="bg-primary bg-opacity-10  rounded-3 d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px;">
<i class="bi bi-calendar-event fs-4"></i>
            </div>

        </div>
    </div>
</div>

      <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden">

        <!-- subtle green background -->
        <div class="position-absolute top-0 start-0 w-100 h-100  opacity-10"></div>

        <div class="card-body d-flex align-items-center justify-content-between position-relative">

            <!-- LEFT CONTENT -->
            <div>
                <small class="text-muted fw-semibold d-block mb-1">Present</small>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['present_days'] }}</h3>
            </div>

            <!-- RIGHT ICON -->
            <div class="bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px;">
<i class="bi bi-calendar-check fs-4"></i>            </div>

        </div>
    </div>
</div>

     <div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden">

        <!-- subtle red background -->
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10"></div>

        <div class="card-body d-flex align-items-center justify-content-between position-relative">

            <!-- LEFT CONTENT -->
            <div>
                <small class="text-muted fw-semibold d-block mb-1">Absent</small>
                <h3 class="fw-bold text-dark mb-0">{{ $stats['absent_days'] }}</h3>
            </div>

            <!-- RIGHT ICON -->
            <div class="bg-danger bg-opacity-10  rounded-3 d-flex align-items-center justify-content-center"
                 style="width: 50px; height: 50px;">
<i class="bi bi-person-dash fs-4"></i>            </div>

        </div>
    </div>
</div>

        <div class="col-md-3">
<div class="card border-0 shadow-sm rounded-4 position-relative overflow-hidden h-100">
        <!-- subtle info background -->
        <div class="position-absolute top-0 start-0 w-100 h-80 opacity-10"></div>

<div class="card-body position-relative py-4">
            <div class="d-flex align-items-center justify-content-between mb-2">

                <!-- LEFT TEXT -->
                <div>
                    <small class="text-muted fw-semibold d-block">Attendance</small>
                    <h3 class="fw-bold text-dark mb-0">
                        {{ $stats['attendance_percentage'] }}%
                    </h3>
                </div>

                <!-- ICON -->
                <div class="bg-info bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center"
                     style="width: 50px; height: 50px;">
<i class="bi bi-graph-up-arrow fs-4"></i>                </div>

            </div>

            {{-- <!-- PROGRESS BAR -->
            <div class="progress mt-3" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar bg-info" 
                     style="width: {{ $stats['attendance_percentage'] }}%">
                </div>
            </div> --}}

        </div>
    </div>
</div>

    </div>

    {{-- CALENDAR (CLEAN + MODERN) --}}
   <div class="row">

    <!-- LEFT SIDE: CALENDAR -->
    <div class="col-lg-5 col-md-6">
        <div class="card shadow-lg border-0 mb-4">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Attendance Calendar</h5>

                <div>
                    <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse(request()->get('month', date('Y-m')))->subMonth()->format('Y-m')]) }}" 
                       class="btn btn-sm btn-outline-primary">Prev</a>

                    <span class="mx-2 fw-bold">
                        {{ \Carbon\Carbon::parse(request()->get('month', date('Y-m')))->format('F Y') }}
                    </span>

                    <a href="{{ route('intern.attendance', ['month' => \Carbon\Carbon::parse(request()->get('month', date('Y-m')))->addMonth()->format('Y-m')]) }}" 
                       class="btn btn-sm btn-outline-primary">Next</a>
                </div>
            </div>
<div class="card-body p-0">

    <div class="table-responsive rounded-4 overflow-hidden">
<table class="table table-bordered text-center align-middle mb-0">
                      <thead>
                        <tr class="text-muted">
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
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

                            $attendanceDates = [];
                            foreach($attendance as $record) {
                                $recordDate = date('Y-m-d', strtotime($record->start_shift));
                                $attendanceDates[$recordDate] = $record->status;
                            }

                            $day = 1;
                        @endphp

                        @for ($row = 0; $row < 6; $row++)
                            <tr>
                                @for ($col = 0; $col < 7; $col++)
                                    
                                    @if ($row == 0 && $col < $firstDayOfWeek)
                                        <td></td>
                                    @elseif ($day <= $daysInMonth)

                                        @php
                                            $dateStr = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                                            $status = $attendanceDates[$dateStr] ?? null;
                                        @endphp

                                        <td>
                                            <div class="p-2 rounded-4 text-center
                                                {{ $status == 1 ? 'bg-success text-white' : ($status === 0 ? 'bg-danger text-white' : 'bg-light') }}">
                                                <strong>{{ $day }}</strong>
                                            </div>
                                        </td>

                                        @php $day++; @endphp

                                    @else
                                        <td></td>
                                    @endif

                                @endfor
                            </tr>
                        @endfor
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    </div>
    
    <!-- RIGHT SIDE: HISTORY -->
    <div class="col-lg-7 col-md-6">
        <div class="card shadow-lg border-0 mb-4">

            <div class="card-header">
                <h5 class="mb-0">Attendance History</h5>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($attendance as $index => $record)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}</td>
                                <td>{{ $record->end_shift ? \Carbon\Carbon::parse($record->end_shift)->format('h:i A') : '-' }}</td>
                                <td>{{ $record->duration ? $record->duration . ' hrs' : '-' }}</td>
                                <td>
                                    <span class="badge {{ $record->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $record->status ? 'Present' : 'Absent' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
</div>
@endsection