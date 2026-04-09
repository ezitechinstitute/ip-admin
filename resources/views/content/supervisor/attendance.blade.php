@extends('layouts/layoutMaster')

@section('title', 'Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="mb-4">Attendance</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Attendance List</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Intern</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($attendance as $index => $row)
                        <tr>

                            {{-- Index --}}
                            <td>{{ $index + 1 }}</td>

                            {{-- Intern Name + ETI --}}
                            <td>
                                <strong>{{ $row->intern_name ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $row->eti_id }}</small>
                            </td>

                            {{-- Date --}}
                            <td>
                                {{ \Carbon\Carbon::parse($row->start_shift)->format('Y-m-d') }}
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge bg-{{ $row->status ? 'success' : 'danger' }}">
                                    {{ $row->status ? 'Present' : 'Absent' }}
                                </span>
                            </td>

                            {{-- Start Time --}}
                            <td>
                                {{ $row->start_shift 
                                    ? \Carbon\Carbon::parse($row->start_shift)->format('h:i A') 
                                    : 'N/A' }}
                            </td>

                            {{-- End Time --}}
                            <td>
                                {{ $row->end_shift 
                                    ? \Carbon\Carbon::parse($row->end_shift)->format('h:i A') 
                                    : 'N/A' }}
                            </td>

                            {{-- Duration --}}
                            <td>
                                {{ $row->duration ? $row->duration . ' hrs' : '0 hrs' }}
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No attendance found</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>
@endsection