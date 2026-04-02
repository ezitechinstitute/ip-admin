@extends('layouts/layoutMaster')

@section('title', 'Learning Resources')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Learning Resources</h5>
        </div>
        <div class="card-body">
            @if($resources->count() > 0)
            <div class="row g-4">
                @foreach($resources as $resource)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ti ti-book fs-1 text-primary me-3"></i>
                                <h6 class="mb-0">{{ $resource->title }}</h6>
                            </div>
                            <p class="text-muted small">{{ \Illuminate\Support\Str::limit($resource->content ?? 'No description', 100) }}</p>
                            <div class="mt-2">
                                <span class="badge bg-secondary">{{ $resource->category }}</span>
                            </div>
                            <a href="{{ route('intern.resources.show', $resource->id) }}" class="btn btn-sm btn-primary mt-3">
                                Read More
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $resources->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="ti ti-book-off ti-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No learning resources available yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection