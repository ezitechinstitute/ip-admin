@extends('layouts/layoutMaster')

@section('title', 'Offer Letter')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Offer Letter</h5>
        </div>
        <div class="card-body text-center py-5">
            @if(isset($offerLetter) && $offerLetter)
                @if($offerLetter->status == 'accept')
                    <i class="ti ti-file-text fs-1 text-success mb-3"></i>
                    <h5>Your Offer Letter is Ready</h5>
                    <p class="text-muted mb-3">
                        Offer Letter ID: {{ $offerLetter->offer_letter_id }}<br>
                        Status: <span class="badge bg-success">Accepted</span>
                    </p>
                    <p class="text-muted mb-4">Your offer letter has been accepted. Contact your manager for the official document.</p>
                    <button class="btn btn-primary" disabled>
                        <i class="ti ti-download me-1"></i> Coming Soon
                    </button>
                @elseif($offerLetter->status == 'pending')
                    <i class="ti ti-clock fs-1 text-warning mb-3"></i>
                    <h5>Offer Letter Pending</h5>
                    <p class="text-muted mb-3">
                        Offer Letter ID: {{ $offerLetter->offer_letter_id }}<br>
                        Status: <span class="badge bg-warning">Pending</span>
                    </p>
                    <p class="text-muted">Your offer letter request is being reviewed by the manager.</p>
                @elseif($offerLetter->status == 'reject')
                    <i class="ti ti-file-off fs-1 text-danger mb-3"></i>
                    <h5>Offer Letter Rejected</h5>
                    <p class="text-muted">Your offer letter request has been rejected. Please contact your manager for more information.</p>
                @endif
            @else
                <i class="ti ti-file-off fs-1 text-muted mb-3"></i>
                <h5>No Offer Letter Available</h5>
                <p class="text-muted">Your offer letter will be available once your internship is confirmed.</p>
                <p class="text-muted small">Contact your manager for more information.</p>
            @endif
        </div>
    </div>
</div>
@endsection