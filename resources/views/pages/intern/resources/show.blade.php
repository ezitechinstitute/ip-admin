@extends('layouts/layoutMaster')

@section('title', $resource->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            {{-- Back Button --}}
            <div class="mb-4">
                <a href="{{ route('intern.resources') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Back to Resources
                </a>
            </div>

            {{-- Resource Card --}}
            <div class="card">
                <div class="card-body">
                    {{-- Category Badge --}}
                    <div class="mb-3">
                        <span class="badge bg-{{ $resource->categoryColor ?? 'primary' }} bg-opacity-10 text-{{ $resource->categoryColor ?? 'primary' }} p-2">
                            <i class="{{ $resource->categoryIcon ?? 'ti ti-folder' }} me-1"></i> {{ $resource->categoryName ?? ucfirst(str_replace('_', ' ', $resource->category)) }}
                        </span>
                        @if($resource->is_featured)
                        <span class="badge bg-warning bg-opacity-10 text-warning ms-2">
                            <i class="ti ti-star me-1"></i> Featured
                        </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h3 class="mb-3">{{ $resource->title }}</h3>

                    {{-- Meta Info --}}
                    <div class="d-flex flex-wrap gap-3 mb-4 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-eye text-muted me-1"></i>
                            <small class="text-muted">{{ $resource->views ?? 0 }} views</small>
                        </div>
                        @if($resource->downloads)
                        <div class="d-flex align-items-center">
                            <i class="ti ti-download text-muted me-1"></i>
                            <small class="text-muted">{{ $resource->downloads }} downloads</small>
                        </div>
                        @endif
                        <div class="d-flex align-items-center">
                            <i class="ti ti-calendar text-muted me-1"></i>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($resource->created_at)->format('M d, Y') }}</small>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="resource-content mb-4">
                        {!! $resource->content !!}
                    </div>

                    {{-- Video Link (if exists) --}}
                    @if($resource->video_url)
                    <div class="mb-4">
                        <h6><i class="ti ti-video me-2 text-danger"></i> Video Tutorial</h6>
                        <a href="{{ $resource->video_url }}" target="_blank" class="btn btn-outline-danger">
                            <i class="ti ti-player-play me-1"></i> Watch Video
                        </a>
                    </div>
                    @endif

                    {{-- External Link --}}
                    @if($resource->external_link)
                    <div class="mb-4">
                        <a href="{{ $resource->external_link }}" target="_blank" class="btn btn-outline-primary">
                            <i class="ti ti-external-link me-1"></i> Open External Resource
                        </a>
                    </div>
                    @endif

                    {{-- File Download --}}
                    @if($resource->file_path)
                    <div class="mb-4">
                        <a href="{{ route('intern.resources.download', $resource->id) }}" class="btn btn-success">
                            <i class="ti ti-download me-1"></i> Download Resource
                        </a>
                    </div>
                    @endif

                    {{-- Mark as Complete Button --}}
                    <div class="mt-4 pt-3 border-top">
                        @if(isset($progress) && !$progress->is_completed)
                        <button type="button" class="btn btn-primary" id="markCompleteBtn">
                            <i class="ti ti-check me-1"></i> Mark as Completed
                        </button>
                        @elseif(isset($progress) && $progress->is_completed)
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="ti ti-check-circle fs-4 me-2"></i>
                            <div>
                                <strong>Completed!</strong> You marked this resource as completed on 
                                {{ \Carbon\Carbon::parse($progress->completed_at)->format('M d, Y') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Related Resources --}}
            @if(isset($relatedResources) && $relatedResources->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-related-services me-2 text-primary"></i>
                        Related Resources
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($relatedResources as $related)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="rounded-circle bg-{{ $related->categoryColor ?? 'primary' }} bg-opacity-10 p-2 me-3">
                                    <i class="{{ $related->categoryIcon ?? 'ti ti-folder' }} text-{{ $related->categoryColor ?? 'primary' }}"></i>
                                </div>
                                <div>
                                    <a href="{{ route('intern.resources.show', $related->id) }}" class="text-dark fw-semibold">
                                        {{ $related->title }}
                                    </a>
                                    <p class="small text-muted mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($related->content), 60) }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('markCompleteBtn')?.addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Processing...';

        fetch('{{ route("intern.resources.complete", $resource->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Something went wrong');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to mark as completed. Please try again.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
</script>
@endpush