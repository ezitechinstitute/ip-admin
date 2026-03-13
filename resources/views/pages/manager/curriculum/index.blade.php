@extends('layouts/layoutMaster')

@section('title', 'Technology Curriculum')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Technology Curriculum</h4>
        <a href="{{ route('manager.curriculum.create') }}" class="btn btn-primary">Add Curriculum</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Curriculum Name</th>
                            <th>Technology</th>
                            <th>Total Projects</th>
                            <th>Duration (weeks)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($curriculums as $curriculum)
                        <tr>
                            <td>{{ $curriculum->curriculum_id }}</td>
                            <td>{{ $curriculum->curriculum_name }}</td>
                            <td>{{ $curriculum->technology->technology ?? 'N/A' }}</td>
                            <td>{{ $curriculum->total_projects }}</td>
                            <td>{{ $curriculum->total_duration_weeks }}</td>
                            <td>
                                @if($curriculum->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                           <td>
                            <div class="d-flex align-items-center">
                                <div class="dropdown">
                                <a href="javascript:;" 
                                    class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                    <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end m-0">
                                    <a href="{{ route('manager.curriculum.show', $curriculum->curriculum_id) }}" 
                                    class="dropdown-item">View</a>

                                    <a href="{{ route('manager.curriculum.edit', $curriculum->curriculum_id) }}" 
                                    class="dropdown-item">Edit</a>

                                    <form action="{{ route('manager.curriculum.destroy', $curriculum->curriculum_id) }}" 
                                        method="POST" 
                                        class="m-0" 
                                        onsubmit="return confirm('Delete curriculum?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                    </form>
                                </div>

                                </div>
                            </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No curricula found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $curriculums->links() }}</div>
</div>
@endsection
