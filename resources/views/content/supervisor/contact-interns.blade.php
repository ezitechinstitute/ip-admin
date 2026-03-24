@extends('layouts/layoutMaster')

@section('title', 'Contact With')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Contact With</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Contact Intern List</h5>
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
                                        <img src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar" class="rounded-circle">
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
                                <td colspan="8" class="text-center">No interns to contact found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection