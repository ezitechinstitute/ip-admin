@extends('layouts/layoutMaster')

@section('title', 'Offer Letter')

@section('content')
<div class="container-xxl container-p-y">

    <div class="row justify-content-center">
        <div class="col-lg-12">

            {{-- HEADER STRIP --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">

                    <div>
                        <h5 class="mb-0">Offer Letter Center</h5>
                        <small class="text-muted">Your official internship documentation system</small>
                    </div>

                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                        Secure Document Portal
                    </span>

                </div>
            </div>

            <div class="row g-4">

                {{-- LEFT: DOCUMENT PANEL --}}
                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body p-4">

                            @if(isset($offerLetter) && $offerLetter)

                                {{-- ACCEPTED --}}
                                @if($offerLetter->status == 'accept')

                                    <div class="d-flex align-items-center gap-3 mb-4">

                                        <div class="bg-success-subtle rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:60px;height:60px;">
                                            <i class="ti ti-shield-check text-success fs-3"></i>
                                        </div>

                                        <div>
                                            <h5 class="mb-0">Offer Approved</h5>
                                            <small class="text-muted">Document is ready for access</small>
                                        </div>

                                    </div>

                                    {{-- DOCUMENT INFO CARD --}}
                                    <div class="border rounded-3 p-3 mb-4 bg-light">

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Offer ID</span>
                                            <span class="fw-semibold">#{{ $offerLetter->offer_letter_id }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Status</span>
                                            <span class="badge bg-success-subtle text-success">Approved</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Security</span>
                                            <span class="text-success fw-semibold">Verified</span>
                                        </div>

                                    </div>

                                    {{-- ACTIONS --}}
                                    <div class="d-flex gap-2">

                                        <button class="btn btn-primary flex-fill" disabled>
                                            <i class="ti ti-download me-1"></i>
                                            Download PDF
                                        </button>

                                        <button class="btn btn-outline-secondary">
                                            <i class="ti ti-eye me-1"></i>
                                            Preview
                                        </button>

                                        <button class="btn btn-outline-dark">
                                            <i class="ti ti-mail me-1"></i>
                                            Email Copy
                                        </button>

                                    </div>

                                {{-- PENDING --}}
                                @elseif($offerLetter->status == 'pending')

                                    <div class="d-flex align-items-center gap-3 mb-4">

                                        <div class="bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:60px;height:60px;">
                                            <i class="ti ti-clock text-warning fs-3"></i>
                                        </div>

                                        <div>
                                            <h5 class="mb-0">Under Review</h5>
                                            <small class="text-muted">Waiting for manager approval</small>
                                        </div>

                                    </div>

                                    <div class="alert bg-warning-subtle border border-warning-subtle">
                                        Your offer letter request is currently in review stage.
                                    </div>

                                    <div class="border rounded-3 p-3 bg-light">

                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Offer ID</span>
                                            <span class="fw-semibold">#{{ $offerLetter->offer_letter_id }}</span>
                                        </div>

                                    </div>

                                    <button class="btn btn-outline-warning mt-3 w-100">
                                        Request Status Update
                                    </button>

                                {{-- REJECTED --}}
                                @elseif($offerLetter->status == 'reject')

                                    <div class="d-flex align-items-center gap-3 mb-4">

                                        <div class="bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center"
                                             style="width:60px;height:60px;">
                                            <i class="ti ti-alert-triangle text-danger fs-3"></i>
                                        </div>

                                        <div>
                                            <h5 class="mb-0">Not Approved</h5>
                                            <small class="text-muted">Review required</small>
                                        </div>

                                    </div>

                                    <div class="alert bg-danger-subtle border border-danger-subtle">
                                        Your request was not approved. You may contact your manager.
                                    </div>

                                    <button class="btn btn-outline-primary w-100">
                                        Request Re-evaluation
                                    </button>

                                @endif

                            @else

                                <div class="text-center py-11">


                                    <h4>No Offer Letter Found</h4>
                                    <p class="text-muted">
                                        Your document will appear once eligibility is confirmed.
                                    </p>

                                </div>

                                <div class="border rounded-3 p-3 bg-light">

                                    <div class="fw-semibold mb-2">This document includes:</div>

                                    <div class="small text-muted">
                                        • Intern Identity Verification<br>
                                        • Assigned Technology Stack<br>
                                        • Internship Duration<br>
                                        • Organization Details<br>
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

                {{-- RIGHT: INFO PANEL --}}
                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm rounded-4 mb-4">

                        <div class="card-body">

                            <h6 class="mb-3">Document Security</h6>

                            <div class="small text-muted">
                                • Digitally managed offer letter<br>
                                • Controlled access system<br>
                                • HR-approved workflow<br>
                                • Audit-ready record
                            </div>

                        </div>

                    </div>

                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body">

                            <h6 class="mb-3">Help Center</h6>

<button class="btn btn-outline-primary w-100 mb-2 hr-btn">
    Contact HR
</button>                            <button class="btn btn-outline-success w-100">View Guidelines</button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>
@endsection