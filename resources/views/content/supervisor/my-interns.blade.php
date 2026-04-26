@extends('layouts/layoutMaster')

@section('title', 'My Interns Directory')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header with Breadcrumbs --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">My Interns</h4>
            <p class="text-muted mb-0">
                <span class="text-muted fw-light">Directory /</span> {{ ucfirst($technology) }} Track
            </p>
        </div>
        <a href="{{ route('supervisor.tasks.kanban') }}" class="btn btn-label-info shadow-none">
            <i class="ti ti-layout-kanban me-1"></i> Kanban View
        </a>
    </div>

    {{-- Advanced Filter Card --}}
    <div class="card mb-4">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Advanced Filtering</h5>
        </div>
        <div class="card-body pt-4">
            <form action="{{ route('supervisor.myInterns') }}" method="GET">
                <div class="row g-3">
                    {{-- Tech Filter --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Technology</label>
                        <input type="text" name="tech" class="form-control" placeholder="Search tech..." value="{{ request('tech') }}">
                    </div>
                    {{-- Type Filter --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Internship Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Remote" {{ request('type') == 'Remote' ? 'selected' : '' }}>Remote</option>
                            <option value="Onsite" {{ request('type') == 'Onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="Hybrid" {{ request('type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    {{-- Status Filter --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    {{-- Join Date Filter --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Join Date</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                            <input type="date" name="join_date" class="form-control" value="{{ request('join_date') }}">
                        </div>
                    </div>
                    {{-- Actions --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary"><i class="ti ti-filter me-1"></i> Filter</button>
                            <a href="{{ route('supervisor.myInterns') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Intern Directory Table --}}
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Intern Identity</th>
                        <th>Contact Info</th>
                        <th>Type</th>
                        <th>Technology</th>
                        <th>Timeline</th>
                        <th style="width: 150px;">Roadmap</th>
                        <th>Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($interns as $intern)
                        <tr>
                            {{-- Intern Name & Avatar --}}
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-md me-3">
                                        <img src="{{ !empty($intern->image) ? asset('storage/' . $intern->image) : asset('assets/img/avatars/1.png') }}" 
                                             alt="Avatar" class="rounded-circle shadow-sm" style="object-fit: cover;">
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-heading">{{ $intern->name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $intern->eti_id }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact Info Column --}}
                            <td>
                                <div class="d-flex flex-column small">
                                    <span><i class="ti ti-mail ti-xs me-1 text-primary"></i>{{ $intern->email ?? 'N/A' }}</span>
                                    <span class="text-muted"><i class="ti ti-phone ti-xs me-1"></i>{{ $intern->phone ?? '--' }}</span>
                                </div>
                            </td>

                            {{-- Internship Type Badge --}}
                            <td>
                                @php 
                                    $typeColor = match($intern->internship_type) { 
                                        'Remote' => 'info', 
                                        'Onsite' => 'primary', 
                                        'Hybrid' => 'warning', 
                                        default => 'secondary' 
                                    }; 
                                @endphp
                                <span class="badge bg-label-{{ $typeColor }}">{{ $intern->internship_type ?? 'Remote' }}</span>
                            </td>

                            {{-- Technology --}}
                            <td><span class="fw-semibold">{{ $intern->int_technology ?? 'N/A' }}</span></td>

                            {{-- Dates --}}
                            <td>
                                <div class="small">
                                    <div class="text-success"><i class="ti ti-arrow-up-right ti-xs me-1"></i>{{ $intern->start_date ?? 'N/A' }}</div>
                                    <div class="text-danger"><i class="ti ti-arrow-down-left ti-xs me-1"></i>{{ $intern->end_date ?? 'N/A' }}</div>
                                </div>
                            </td>

                            {{-- Progress Bar --}}
                            <td>
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold text-primary">{{ $intern->progress }}%</small>
                                </div>
                                <div class="progress" style="height: 6px; background-color: rgba(115, 103, 240, 0.08);">
                                    <div class="progress-bar bg-primary shadow-none" style="width:{{ $intern->progress }}%" role="progressbar"></div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td>
                                @php
                                    $statusClass = match(strtolower($intern->int_status)) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'completed' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $statusClass }} rounded-pill">{{ ucfirst($intern->int_status ?? 'N/A') }}</span>
                            </td>

                            {{-- Single Action --}}
                            <td class="text-center">
                                <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-sm btn-icon btn-label-primary shadow-none" title="View Profile">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="avatar avatar-lg bg-label-secondary mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle"><i class="ti ti-users-minus"></i></span>
                                </div>
                                <h6 class="text-muted">No interns found in this track.</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Footer (Handles 1000+ interns perfectly) --}}
        <div class="card-footer border-top py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">Showing {{ $interns->firstItem() ?? 0 }} to {{ $interns->lastItem() ?? 0 }} of {{ $interns->total() }} interns</small>
            <div>
                {{ $interns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection