@extends('layouts/layoutMaster')

@section('title', 'Offer Letter')

@section('content')
<div class="container-xxl container-p-y">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-check-circle fs-3 me-3 text-success"></i>
            <div>
                <strong class="d-block text-success">Success!</strong>
                <span class="text-success">{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-3 me-3 text-danger"></i>
            <div>
                <strong class="d-block text-danger">Error!</strong>
                <span class="text-danger">{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-info-circle fs-3 me-3 text-info"></i>
            <div>
                <strong class="d-block text-info">Info</strong>
                <span class="text-info">{{ session('info') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-12">

            {{-- HEADER STRIP --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h5 class="mb-0">
                            <i class="ti ti-file-certificate text-primary me-2"></i>Offer Letter Center
                        </h5>
                        <small class="text-muted">Your official internship documentation system</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                        <i class="ti ti-shield-lock me-1"></i> Secure Document Portal
                    </span>
                </div>
            </div>

            <div class="row g-4">

                {{-- LEFT: DOCUMENT PANEL --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">

                            @if(isset($offerLetter) && $offerLetter)

                                {{-- ============================================ --}}
                                {{-- STATUS: ACCEPTED / APPROVED --}}
                                {{-- ============================================ --}}
                                @if($offerLetter->status == 'accept' || $offerLetter->status == 'approved')
                                    <div class="text-center mb-4">
                                        <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width:80px;height:80px;">
                                            <i class="ti ti-shield-check text-success fs-1"></i>
                                        </div>
                                        <h5 class="mb-1">Offer Letter Approved ✅</h5>
                                        <small class="text-muted">Your official document is ready</small>
                                    </div>

                                    {{-- DOCUMENT INFO CARD --}}
                                    <div class="border rounded-3 p-4 mb-4 bg-light">
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Offer ID</small>
                                                <span class="fw-semibold">#{{ $offerLetter->offer_letter_id ?? $offerLetter->id ?? 'N/A' }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Status</small>
                                                <span class="badge bg-success-subtle text-success rounded-pill">Approved</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Issued Date</small>
                                                <span class="fw-semibold">{{ isset($offerLetter->created_at) ? \Carbon\Carbon::parse($offerLetter->created_at)->format('M d, Y') : date('M d, Y') }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Security</small>
                                                <span class="text-success fw-semibold"><i class="ti ti-check me-1"></i>Verified</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ============================================ --}}
                                    {{-- OFFER LETTER CONTENT --}}
                                    {{-- ============================================ --}}
                                    <div id="offerLetterContent" class="border rounded-3 p-4 mb-4 bg-white">
                                        <div class="text-center mb-4 pb-3 border-bottom">
                                            <h4 class="fw-bold text-primary mb-0">EZILINE SOFTWARE HOUSE</h4>
                                            <p class="text-muted mb-0">Amna Plaza, Near Radio Pakistan, Rawalpindi</p>
                                            <p class="small text-muted">www.eziline.com | hr@eziline.com</p>
                                        </div>

                                        <h5 class="text-center mb-3 text-uppercase text-decoration-underline">Internship Offer Letter</h5>

                                        @if(isset($offerContent) && $offerContent)
                                            <div class="offer-body">
                                                {!! nl2br(e($offerContent)) !!}
                                            </div>
                                        @else
                                            <div class="offer-body">
                                                <p>Dear <strong>{{ $offerLetter->username ?? 'Intern' }}</strong>,</p>
                                                <p>Congratulations! We are pleased to offer you an internship position at <strong>Eziline Software House</strong>.</p>
                                                <p>This is to confirm your selection for the internship program. We believe your skills and enthusiasm will be a valuable addition to our team.</p>
                                                
                                                <div class="border rounded-3 p-3 bg-light my-3">
                                                    <table class="table table-sm mb-0">
                                                        <tr>
                                                            <td class="text-muted" width="40%">Name</td>
                                                            <td><strong>{{ $offerLetter->username ?? 'N/A' }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Email</td>
                                                            <td>{{ $offerLetter->email ?? 'N/A' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Technology</td>
                                                            <td><strong>{{ $offerLetter->tech ?? 'Not Assigned' }}</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Duration</td>
                                                            <td><strong>3 Months</strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">Start Date</td>
                                                            <td><strong>{{ isset($offerLetter->created_at) ? \Carbon\Carbon::parse($offerLetter->created_at)->format('F d, Y') : date('F d, Y') }}</strong></td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <p>We look forward to a productive and mutually beneficial association.</p>
                                            </div>
                                        @endif

                                        {{-- Signatures --}}
                                        <div class="row mt-5 pt-3 border-top">
                                            <div class="col-6">
                                                <p class="mb-0 small text-muted">Offer ID: {{ $offerLetter->offer_letter_id ?? 'N/A' }}</p>
                                                <p class="mb-0 small text-muted">Date: {{ date('F d, Y') }}</p>
                                            </div>
                                            <div class="col-6 text-end">
                                                <p class="mb-0 fw-bold">____________________</p>
                                                <p class="mb-0 small">Authorized Signature</p>
                                                <p class="mb-0 small text-muted">HR Department</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- ACTIONS --}}
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('intern.offer-letter.download') }}" class="btn btn-primary flex-fill">
                                            <i class="ti ti-download me-1"></i> Download PDF
                                        </a>
                                        <button class="btn btn-outline-secondary flex-fill" onclick="printOfferLetter()">
                                            <i class="ti ti-printer me-1"></i> Print
                                        </button>
                                    </div>

                                {{-- ============================================ --}}
                                {{-- STATUS: PENDING --}}
                                {{-- ============================================ --}}
                                @elseif($offerLetter->status == 'pending')
                                    <div class="text-center mb-4">
                                        <div class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width:80px;height:80px;">
                                            <i class="ti ti-clock text-warning fs-1"></i>
                                        </div>
                                        <h5 class="mb-1">Under Review ⏳</h5>
                                        <small class="text-muted">Waiting for manager approval</small>
                                    </div>

                                    <div class="alert bg-warning-subtle border border-warning-subtle rounded-3 mb-4">
                                        <div class="d-flex gap-2">
                                            <i class="ti ti-info-circle text-warning fs-4"></i>
                                            <div>
                                                <strong>Pending Approval</strong>
                                                <p class="mb-0 small">Your offer letter request is being reviewed by the management team. You will be notified once approved.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded-3 p-3 bg-light mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Request ID</span>
                                            <span class="fw-semibold">#{{ $offerLetter->offer_letter_id ?? $offerLetter->id ?? 'N/A' }}</span>
                                        </div>
                                        @if(isset($offerLetter->created_at))
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-muted">Requested On</span>
                                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($offerLetter->created_at)->format('M d, Y h:i A') }}</span>
                                        </div>
                                        @endif
                                    </div>

                                {{-- ============================================ --}}
                                {{-- STATUS: REJECTED --}}
                                {{-- ============================================ --}}
                                @elseif($offerLetter->status == 'reject' || $offerLetter->status == 'rejected')
                                    <div class="text-center mb-4">
                                        <div class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                             style="width:80px;height:80px;">
                                            <i class="ti ti-alert-triangle text-danger fs-1"></i>
                                        </div>
                                        <h5 class="mb-1">Not Approved ❌</h5>
                                        <small class="text-muted">Your request needs attention</small>
                                    </div>

                                    <div class="alert bg-danger-subtle border border-danger-subtle rounded-3 mb-4">
                                        <div class="d-flex gap-2">
                                            <i class="ti ti-alert-circle text-danger fs-4"></i>
                                            <div>
                                                <strong>Request Not Approved</strong>
                                                <p class="mb-0 small">Your offer letter request was not approved. Please contact your manager or HR for more information.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('intern.offer-letter.request') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary w-100">
                                            <i class="ti ti-refresh me-1"></i> Request Re-evaluation
                                        </button>
                                    </form>

                                @endif

                            @else
                                {{-- ============================================ --}}
                                {{-- NO OFFER LETTER - REQUEST FORM --}}
                                {{-- ============================================ --}}
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="ti ti-file-text text-muted" style="font-size: 5rem;"></i>
                                    </div>
                                    <h4>No Offer Letter Found</h4>
                                    <p class="text-muted mx-auto" style="max-width: 400px;">
                                        Your offer letter will appear here once your eligibility is confirmed and the document is generated.
                                    </p>

                                    <form action="{{ route('intern.offer-letter.request') }}" method="POST" class="mt-3">
                                        @csrf
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="ti ti-send me-1"></i> Request Offer Letter
                                        </button>
                                    </form>
                                </div>

                                <div class="border rounded-3 p-4 bg-light mt-4">
                                    <div class="fw-semibold mb-3">
                                        <i class="ti ti-file-info text-primary me-1"></i> What's Included:
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-check text-success"></i>
                                                <small>Intern Identity Verification</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-check text-success"></i>
                                                <small>Assigned Technology Stack</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-check text-success"></i>
                                                <small>Internship Duration</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ti ti-check text-success"></i>
                                                <small>Organization Details</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endif

                        </div>
                    </div>
                </div>

                {{-- RIGHT: INFO PANEL --}}
                <div class="col-lg-4">

                    {{-- Status Card --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body text-center">
                            @if(isset($offerLetter) && $offerLetter)
                                @if($offerLetter->status == 'accept' || $offerLetter->status == 'approved')
                                    <div class="text-success mb-2">
                                        <i class="ti ti-circle-check fs-1"></i>
                                    </div>
                                    <h6 class="text-success">Document Ready</h6>
                                    <small class="text-muted">Your offer letter is approved and available for download.</small>
                                @elseif($offerLetter->status == 'pending')
                                    <div class="text-warning mb-2">
                                        <i class="ti ti-clock fs-1"></i>
                                    </div>
                                    <h6 class="text-warning">Pending Review</h6>
                                    <small class="text-muted">Waiting for management approval.</small>
                                @else
                                    <div class="text-danger mb-2">
                                        <i class="ti ti-x-circle fs-1"></i>
                                    </div>
                                    <h6 class="text-danger">Not Approved</h6>
                                    <small class="text-muted">Contact your manager for details.</small>
                                @endif
                            @else
                                <div class="text-muted mb-2">
                                    <i class="ti ti-file-off fs-1"></i>
                                </div>
                                <h6>No Document</h6>
                                <small class="text-muted">Request your offer letter to get started.</small>
                            @endif
                        </div>
                    </div>

                    {{-- Document Security --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="ti ti-shield-check text-primary me-1"></i> Document Security
                            </h6>
                            <div class="small text-muted">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-check text-success"></i>
                                    <span>Digitally managed offer letter</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-check text-success"></i>
                                    <span>Controlled access system</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="ti ti-check text-success"></i>
                                    <span>HR-approved workflow</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ti ti-check text-success"></i>
                                    <span>Audit-ready record</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Help Center --}}
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <h6 class="mb-3">
                                <i class="ti ti-help-circle text-primary me-1"></i> Help Center
                            </h6>
                            <p class="small text-muted mb-3">Need assistance with your offer letter?</p>
                            <button class="btn btn-outline-primary w-100 mb-2" onclick="alert('HR Contact: hr@eziline.com')">
                                <i class="ti ti-mail me-1"></i> Contact HR
                            </button>
                            <button class="btn btn-outline-secondary w-100" onclick="alert('Guidelines page coming soon.')">
                                <i class="ti ti-book me-1"></i> View Guidelines
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@section('page-script')
<script>
function printOfferLetter() {
    const content = document.getElementById('offerLetterContent');
    if (!content) {
        alert('No offer letter content to print');
        return;
    }
    
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    printWindow.document.write(`
        <html>
        <head>
            <title>Offer Letter - Eziline Software House</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 40px;
                    line-height: 1.8;
                    color: #333;
                }
                h4 { color: #0d9394; margin-bottom: 5px; }
                h5 { margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; }
                table td { padding: 8px 10px; border-bottom: 1px solid #eee; }
                .border-top { border-top: 2px solid #0d9394; padding-top: 15px; }
                .text-center { text-align: center; }
                .text-end { text-align: right; }
                .text-muted { color: #666; }
                @media print {
                    body { padding: 20px; }
                }
            </style>
        </head>
        <body>
            ${content.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    
    setTimeout(() => {
        printWindow.print();
    }, 500);
}
</script>
@endsection