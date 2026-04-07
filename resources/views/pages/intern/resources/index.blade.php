@extends('layouts/layoutMaster')

@section('title', 'Learning Resources')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header with Progress --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="ti ti-book-2 me-2 text-primary"></i>
                                Learning Resources
                            </h4>
                            <p class="text-muted mb-0">Access internship rules, coding standards, guides & learning materials</p>
                        </div>
                        <div class="mt-2 mt-sm-0">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <div class="small text-muted">Your Progress</div>
                                    <h3 class="mb-0 text-primary">{{ $completionPercentage ?? 0 }}%</h3>
                                </div>
                                <div class="progress" style="width: 150px; height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $completionPercentage ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('intern.resources') }}" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search resources..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="all">All Categories</option>
                                @foreach($categories as $key => $cat)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                    {{ $cat['name'] }} ({{ $cat['count'] }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            @if(request('search') || request('category'))
                            <a href="{{ route('intern.resources') }}" class="btn btn-outline-secondary w-100">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Quick Access --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                @foreach($categories as $key => $cat)
                <a href="{{ route('intern.resources', ['category' => $key]) }}" 
                   class="btn btn-sm {{ request('category') == $key ? 'btn-primary' : 'btn-outline-secondary' }}">
                    <i class="{{ $cat['icon'] }} me-1"></i> {{ $cat['name'] }}
                    <span class="badge {{ request('category') == $key ? 'bg-white text-primary' : 'bg-secondary bg-opacity-25' }} ms-1">{{ $cat['count'] }}</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Featured Resources Section --}}
    @if(isset($featured) && $featured->count() > 0 && !request('search') && !request('category'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="ti ti-star me-2 text-warning"></i>
                    Featured Resources
                </h5>
            </div>
        </div>
        @foreach($featured as $resource)
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card h-100 shadow-sm border-primary border-opacity-25">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-{{ $resource->categoryColor ?? 'primary' }} bg-opacity-10 text-{{ $resource->categoryColor ?? 'primary' }}">
                            <i class="{{ $resource->categoryIcon ?? 'ti ti-folder' }} me-1"></i> {{ $resource->categoryName ?? ucfirst($resource->category) }}
                        </span>
                        @if(in_array($resource->id, $completedResourceIds ?? []))
                            <i class="ti ti-check-circle text-success fs-5" title="Completed"></i>
                        @endif
                    </div>
                    <h6 class="card-title mb-2">{{ \Illuminate\Support\Str::limit($resource->title, 50) }}</h6>
                    <p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($resource->content), 80) }}</p>
                    <a href="{{ route('intern.resources.show', $resource->id) }}" class="btn btn-sm btn-outline-primary w-100">
                        View Resource →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- All Resources Grid --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        @if(request('category') && isset($categories[request('category')]))
                            <i class="{{ $categories[request('category')]['icon'] }} me-2 text-primary"></i>
                            {{ $categories[request('category')]['name'] }}
                        @else
                            <i class="ti ti-library me-2 text-primary"></i>
                            All Resources
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($resources->count() > 0)
                        <div class="row g-4">
                            @foreach($resources as $resource)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-{{ $resource->categoryColor ?? 'primary' }} bg-opacity-10 p-2 me-3">
                                                    <i class="{{ $resource->categoryIcon ?? 'ti ti-folder' }} text-{{ $resource->categoryColor ?? 'primary' }} fs-5"></i>
                                                </div>
                                                <div>
                                                    <span class="badge bg-{{ $resource->categoryColor ?? 'primary' }} bg-opacity-10 text-{{ $resource->categoryColor ?? 'primary' }}">
                                                        {{ $resource->categoryName ?? ucfirst(str_replace('_', ' ', $resource->category)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(in_array($resource->id, $completedResourceIds ?? []))
                                                <i class="ti ti-check-circle text-success fs-4" title="Completed"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title mb-2">{{ $resource->title }}</h6>
                                        <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit(strip_tags($resource->content), 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('intern.resources.show', $resource->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-eye me-1"></i> View
                                                </a>
                                                @if($resource->file_path)
                                                <a href="{{ route('intern.resources.download', $resource->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="ti ti-download me-1"></i> Download
                                                </a>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                <i class="ti ti-eye me-1"></i> {{ $resource->views ?? 0 }}
                                            </small>
                                        </div>
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
                            <p class="text-muted mb-0">No learning resources found.</p>
                            @if(request('search') || request('category'))
                            <a href="{{ route('intern.resources') }}" class="btn btn-sm btn-primary mt-3">
                                Clear Filters
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelector('select[name="category"]')?.addEventListener('change', function() {
        this.closest('form').submit();
    });
</script>
@endpush