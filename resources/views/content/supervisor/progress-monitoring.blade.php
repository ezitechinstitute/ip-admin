@extends('layouts/layoutMaster')

@section('title', 'Progress Monitoring')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Intern Progress Monitoring</h4>

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Avg. Completion Rate</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ round($interns->avg('progress')) }}%</h4>
                            </div>
                            <small>Overall team efficiency</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti tabler-chart-pie ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Deadline Compliance</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ round($interns->avg('compliance')) }}%</h4>
                            </div>
                            <small>Tasks finished on time</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti tabler-calendar-check ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Overdue Tasks</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2 text-danger">{{ $interns->sum('overdue_tasks') }}</h4>
                            </div>
                            <small>Requires attention</small>
                        </div>
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="ti tabler-alert-triangle ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Interns</span>
                            <div class="d-flex align-items-center my-1">
                                <h4 class="mb-0 me-2">{{ $interns->count() }}</h4>
                            </div>
                            <small>Currently monitoring</small>
                        </div>
                        <span class="badge bg-label-info rounded p-2">
                            <i class="ti tabler-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-3">Intern Real-time Tracking</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover border-top">
                    <thead>
                        <tr>
                            <th>Intern</th>
                            <th>Task Progress</th>
                            <th>Project Completion</th>
                            <th>Deadline Compliance</th> {{-- 🔥 ADDED THIS --}}
                            <th>Code Quality</th>
                            <th>Pending/Overdue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        @foreach($interns as $intern)
            <tr>
                <td>
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="avatar-wrapper">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-{{ ['primary', 'success', 'info', 'warning', 'danger'][rand(0, 4)] }}">{{ strtoupper(substr($intern->name, 0, 1)) }}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="text-body text-truncate fw-bold">{{ $intern->name }}</a>
                            <small class="text-muted text-nowrap">{{ $intern->int_technology }}</small>
                        </div>
                    </div>
                </td>
                
                {{-- Task Completion Rate --}}
                <td>
                    <div class="d-flex align-items-center mb-1">
                        <div class="progress w-100 me-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width:{{ $intern->progress }}%" role="progressbar"></div>
                        </div>
                        <span>{{ $intern->progress }}%</span>
                    </div>
                    <small class="text-muted">{{ $intern->completed_tasks }}/{{ $intern->total_tasks }} Tasks</small>
                </td>

                {{-- Project Completion Percentage --}}
                <td>
                    <div class="d-flex align-items-center mb-1">
                        <div class="progress w-100 me-2" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width:{{ $intern->project_completion }}%" role="progressbar"></div>
                        </div>
                        <span>{{ $intern->project_completion }}%</span>
                    </div>
                    <small class="text-muted">{{ $intern->total_projects }} Assigned</small>
                </td>

                {{-- 🔥 NEW: Deadline Compliance --}}
                <td>
                    <div class="d-flex align-items-center mb-1">
                        <div class="progress w-100 me-2" style="height: 6px;">
                            {{-- Color logic: Green > 80, Yellow > 50, Red < 50 --}}
                            @php $compColor = $intern->compliance >= 80 ? 'success' : ($intern->compliance >= 50 ? 'warning' : 'danger'); @endphp
                            <div class="progress-bar bg-{{ $compColor }}" style="width:{{ $intern->compliance }}%" role="progressbar"></div>
                        </div>
                        <span>{{ $intern->compliance }}%</span>
                    </div>
                    <small class="text-muted">On-time rate</small>
                </td>

                {{-- Code Quality Score --}}
                <td>
                    <span class="badge bg-label-{{ $intern->code_quality >= 80 ? 'success' : ($intern->code_quality >= 50 ? 'warning' : 'danger') }}">
                        {{ $intern->code_quality }} / 100
                    </span>
                </td>

                {{-- Pending / Overdue --}}
                <td>
                    <span class="text-warning fw-bold">{{ $intern->total_tasks - $intern->completed_tasks }}</span> 
                    @if($intern->overdue_tasks > 0)
                        <span class="badge bg-label-danger ms-1">{{ $intern->overdue_tasks }} Overdue</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-sm btn-icon btn-label-primary">
                            <i class="ti tabler-eye"></i>
                        </a>
                        <a href="{{ route('supervisor.evaluations.create', $intern->eti_id) }}" class="btn btn-sm btn-icon btn-label-success" title="Evaluate">
                            <i class="ti tabler-star"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
