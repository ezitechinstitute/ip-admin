@extends('layouts/layoutMaster')

@section('title', 'My Interns')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">My Interns</h4>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('supervisor.myInterns') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Technology</label>
                        <input type="text" name="tech" class="form-control" placeholder="Search tech..." value="{{ request('tech') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="Remote" {{ request('type') == 'Remote' ? 'selected' : '' }}>Remote</option>
                            <option value="Onsite" {{ request('type') == 'Onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="Hybrid" {{ request('type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="mb-0">Intern List</h5>
            <a href="{{ route('supervisor.tasks.kanban') }}" class="btn btn-outline-info">Kanban View</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Technology</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $index => $intern)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="avatar avatar-sm">
                                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                </td>
                                <td>{{ $intern->name ?? 'N/A' }}</td>
                                <td>{{ $intern->email ?? 'N/A' }}</td>
                                <td><span class="badge bg-label-info">{{ $intern->internship_type ?? 'Remote' }}</span></td>
                                <td>{{ $intern->int_technology ?? 'N/A' }}</td>
                                <td>{{ $intern->start_date ?? 'N/A' }}</td>
                                <td>{{ $intern->int_status ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-sm btn-label-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No interns found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection