@extends('layouts/layoutMaster')

{{-- 
===========================================
BACKUP - STANDALONE INVOICE CREATION PAGE
===========================================
This is a SECONDARY/BACKUP page accessed directly via URL:
/admin/invoices/create-from-profile?email=...&name=...

testing http://127.0.0.1:8000/admin/invoices/create-from-profile?email=test@gmail.com&name=Test%20User&phone=03001234567&technology=Laravel

The PRIMARY invoice creation happens in:
viewProfile.blade.php (inline form with package auto-fill)

This page is kept as a fallback. Both pages submit to:
route('admin.invoices.store') → InvoiceController@store
===========================================
--}}

@section('title', 'Create Invoice')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root {
        --primary: #2b9a82;
        --primary-gradient: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 50%, #f8fafb 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .premium-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: var(--card-radius);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
    }

    .page-header-card {
        background: linear-gradient(135deg, #ffffff 0%, rgba(43, 154, 130, 0.03) 100%);
        border-radius: var(--card-radius);
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .page-header-icon {
        width: 52px; height: 52px;
        border-radius: 1rem;
        background: var(--primary-gradient);
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1.4rem;
        box-shadow: 0 4px 15px rgba(43,154,130,0.3);
    }

    .intern-info-banner {
        background: linear-gradient(135deg, rgba(43,154,130,0.06), rgba(59,130,246,0.04));
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    }

    .intern-avatar-circle {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 700; font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(43,154,130,0.25);
    }

    .intern-info-tag {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.3rem 0.75rem; border-radius: 50px;
        font-size: 0.72rem; font-weight: 500;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(0,0,0,0.06);
        color: #4b5563;
    }

    .form-section { padding: 1.75rem 2rem; }

    .form-section-title {
        font-size: 0.9rem; font-weight: 700;
        color: #2b9a82; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 0.5rem;
        text-transform: uppercase; letter-spacing: 0.8px;
    }

    .form-section-title::after {
        content: ''; flex: 1; height: 1px;
        background: linear-gradient(90deg, rgba(43,154,130,0.2), transparent);
    }

    .form-label-premium {
        font-size: 0.75rem; font-weight: 600;
        color: #4b5563; margin-bottom: 0.4rem;
        display: flex; align-items: center; gap: 0.4rem;
    }

    .form-label-premium .required-dot { color: #ef4444; font-size: 0.5rem; }

    .form-control-premium {
        border: 1.5px solid rgba(0,0,0,0.08);
        border-radius: 0.75rem; padding: 0.65rem 1rem;
        font-size: 0.85rem; font-weight: 500;
        transition: var(--transition-smooth);
        background: rgba(255,255,255,0.7);
        color: #1e293b; width: 100%;
    }

    .form-control-premium:focus {
        border-color: #2b9a82;
        box-shadow: 0 0 0 3px rgba(43,154,130,0.1);
        outline: none; background: white;
    }

    .form-control-premium.readonly-input {
        background: rgba(0,0,0,0.02);
        cursor: not-allowed; color: #6c86a3;
        border-style: dashed;
    }

    .form-select-premium {
        border: 1.5px solid rgba(0,0,0,0.08);
        border-radius: 0.75rem; padding: 0.65rem 1rem;
        font-size: 0.85rem; font-weight: 500;
        transition: var(--transition-smooth);
        background: rgba(255,255,255,0.7);
        color: #1e293b; width: 100%; cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M6 8L1 3h10z' fill='%236c86a3'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 2.5rem;
    }

    .form-select-premium:focus {
        border-color: #2b9a82;
        box-shadow: 0 0 0 3px rgba(43,154,130,0.1);
        outline: none; background: white;
    }

    .amount-input-group { position: relative; }

    .amount-input-group .currency-symbol {
        position: absolute; left: 1rem;
        top: 50%; transform: translateY(-50%);
        font-weight: 700; color: #2b9a82;
        font-size: 0.85rem; z-index: 2;
    }

    .amount-input-group .form-control-premium {
        padding-left: 2.8rem;
        font-weight: 600; font-size: 1rem;
    }

    .form-helper-text {
        font-size: 0.7rem; color: #94a3b8;
        margin-top: 0.35rem;
        display: flex; align-items: center; gap: 0.3rem;
    }

    .invoice-preview-card {
        background: linear-gradient(135deg, #fff, rgba(43,154,130,0.03));
        border-radius: 1rem; padding: 1.25rem;
        border: 1px dashed rgba(43,154,130,0.3);
        margin-top: 1rem;
    }

    .preview-row {
        display: flex; justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        font-size: 0.8rem;
    }

    .preview-row:last-child { border-bottom: none; font-weight: 700; }
    .preview-label { color: #6c86a3; }
    .preview-value { font-weight: 600; color: #1e293b; }
    .preview-value.highlight { color: #2b9a82; }

    .form-actions {
        background: rgba(0,0,0,0.01);
        padding: 1.25rem 2rem;
        border-top: 1px solid rgba(0,0,0,0.05);
        display: flex; justify-content: flex-end; gap: 0.75rem;
    }

    .btn-primary-premium {
        background: var(--primary-gradient); color: white;
        border: none; padding: 0.7rem 1.75rem;
        border-radius: 50px; font-weight: 600;
        font-size: 0.82rem; cursor: pointer;
        transition: var(--transition-smooth);
        box-shadow: 0 4px 15px rgba(43,154,130,0.3);
        display: inline-flex; align-items: center; gap: 0.5rem;
    }

    .btn-primary-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(43,154,130,0.4);
    }

    .btn-outline-premium {
        background: transparent; color: #6c86a3;
        border: 1.5px solid rgba(0,0,0,0.1);
        padding: 0.7rem 1.75rem; border-radius: 50px;
        font-weight: 600; font-size: 0.82rem;
        cursor: pointer; text-decoration: none;
        transition: var(--transition-smooth);
        display: inline-flex; align-items: center; gap: 0.5rem;
    }

    .btn-outline-premium:hover {
        border-color: #2b9a82; color: #2b9a82;
        background: rgba(43,154,130,0.03);
    }

    .spinner-border-xs { width: 0.9rem; height: 0.9rem; border-width: 0.12em; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in { animation: fadeInUp 0.5s ease-out forwards; }

    @media (max-width: 768px) {
        .form-section { padding: 1.25rem 1rem; }
        .form-actions { flex-direction: column; }
        .btn-primary-premium, .btn-outline-premium { width: 100%; justify-content: center; }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Page Header --}}
    <div class="page-header-card animate-in">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="page-header-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: #1e293b;">Create Invoice</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('all-interns-admin') }}" class="text-decoration-none">Interns</a></li>
                            <li class="breadcrumb-item"><a href="{{ url()->previous() }}" class="text-decoration-none">Profile</a></li>
                            <li class="breadcrumb-item active text-primary fw-semibold">Create Invoice</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                <i class="bi bi-hash me-1"></i> {{ $newInvId }}
            </span>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="premium-card animate-in" style="animation-delay: 0.1s;">
        
        {{-- Intern Banner --}}
        <div class="intern-info-banner">
            <div class="intern-avatar-circle">
                {{ strtoupper(substr($internName ?? 'IN', 0, 2)) }}
            </div>
            <div class="flex-grow-1">
                <h5 class="fw-bold mb-1" style="color: #1e293b;">{{ $internName ?? 'N/A' }}</h5>
                <div class="d-flex flex-wrap gap-2">
                    <span class="intern-info-tag">
                        <i class="bi bi-envelope text-primary"></i> {{ $internEmail ?? 'N/A' }}
                    </span>
                    @if(!empty($internPhone))
                    <span class="intern-info-tag">
                        <i class="bi bi-telephone text-success"></i> {{ $internPhone }}
                    </span>
                    @endif
                    @if(!empty($internTechnology))
                    <span class="intern-info-tag">
                        <i class="bi bi-code-slash text-info"></i> {{ $internTechnology }}
                    </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('admin.invoices.store') }}" method="POST" id="createInvoiceForm">
            @csrf
            <input type="hidden" name="name" value="{{ $internName }}">
            <input type="hidden" name="intern_email" value="{{ $internEmail }}">
            <input type="hidden" name="inv_id" value="{{ $newInvId }}">
            <input type="hidden" name="contact" value="{{ $internPhone ?? '' }}">
            <input type="hidden" name="technology" value="{{ $internTechnology ?? '' }}">

            <div class="form-section">
                <h6 class="form-section-title">
                    <i class="bi bi-gear-fill"></i> Invoice Configuration
                </h6>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-telephone text-primary"></i> Contact Number
                        </label>
                        <input type="text" class="form-control-premium readonly-input" 
                               value="{{ $internPhone ?? 'Not provided' }}" readonly disabled>
                        <div class="form-helper-text">
                            <i class="bi bi-info-circle"></i> From intern profile
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-code-slash text-info"></i> Technology
                        </label>
                        <input type="text" class="form-control-premium readonly-input" 
                               value="{{ $internTechnology ?? 'Not specified' }}" readonly disabled>
                        <div class="form-helper-text">
                            <i class="bi bi-info-circle"></i> From intern profile
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-tag text-warning"></i> Invoice Type
                            <span class="required-dot">●</span>
                        </label>
                        <select name="invoice_type" class="form-select-premium" required>
                            <option value="Internship" selected>🎓 Internship Fee</option>
                            <option value="Course">📚 Course Fee</option>
                            <option value="Project">💼 Project Fee</option>
                            <option value="Other">📋 Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-calendar-event text-danger"></i> Due Date
                        </label>
                        <input type="date" name="due_date" class="form-control-premium" 
                               id="dueDate" min="{{ date('Y-m-d') }}">
                        <div class="form-helper-text">
                            <i class="bi bi-info-circle"></i> Leave empty if full payment
                        </div>
                    </div>
                </div>

                <h6 class="form-section-title mt-4">
                    <i class="bi bi-cash-stack"></i> Payment Details
                </h6>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-calculator text-primary"></i> Total Amount (PKR)
                            <span class="required-dot">●</span>
                        </label>
                        <div class="amount-input-group">
                            <span class="currency-symbol">Rs.</span>
                            <input type="number" name="total_amount" class="form-control-premium" 
                                   step="0.01" min="0" required id="totalAmount" placeholder="0.00"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-premium">
                            <i class="bi bi-check-circle text-success"></i> Received Amount (PKR)
                        </label>
                        <div class="amount-input-group">
                            <span class="currency-symbol">Rs.</span>
                            <input type="number" name="received_amount" class="form-control-premium" 
                                   step="0.01" min="0" value="0" id="receivedAmount" placeholder="0.00"
                                   oninput="updatePreview()">
                        </div>
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="invoice-preview-card" id="invoicePreview" style="display: none;">
                    <h6 class="fw-bold mb-3" style="color: #2b9a82; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.8px;">
                        <i class="bi bi-eye me-1"></i> Invoice Summary
                    </h6>
                    <div class="preview-row">
                        <span class="preview-label">Invoice ID</span>
                        <span class="preview-value">{{ $newInvId }}</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Total</span>
                        <span class="preview-value highlight" id="previewTotal">PKR 0</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Received</span>
                        <span class="preview-value text-success" id="previewReceived">PKR 0</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Remaining</span>
                        <span class="preview-value" id="previewRemaining" style="color: #ef4444;">PKR 0</span>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ url()->previous() }}" class="btn-outline-premium">
                    <i class="bi bi-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn-primary-premium" id="submitBtn">
                    <i class="bi bi-check-circle"></i> Create Invoice
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default due date (30 days)
    const dueDateInput = document.getElementById('dueDate');
    if (dueDateInput && !dueDateInput.value) {
        const d = new Date(); d.setDate(d.getDate() + 30);
        dueDateInput.value = d.toISOString().split('T')[0];
    }

    // Check for success message from session
    @if(session('success'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#10b981',
            color: '#fff',
            iconColor: '#fff',
            customClass: {
                popup: 'shadow-lg rounded-3'
            }
        });
    @endif

    // Check for error message from session
    @if(session('error'))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session('error') }}',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#ef4444',
            color: '#fff',
            iconColor: '#fff',
            customClass: {
                popup: 'shadow-lg rounded-3'
            }
        });
    @endif

    // Form validation with SweetAlert
    document.getElementById('createInvoiceForm').addEventListener('submit', function(e) {
        const total = parseFloat(document.getElementById('totalAmount').value) || 0;
        const received = parseFloat(document.getElementById('receivedAmount').value) || 0;
        const dueDate = document.getElementById('dueDate').value;
        
        // Validate total amount
        if (total <= 0) {
            e.preventDefault();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Please enter a valid Total Amount',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f59e0b',
                color: '#fff',
                iconColor: '#fff'
            });
            document.getElementById('totalAmount').focus();
            return;
        }
        
        // Validate received amount
        if (received > total) {
            e.preventDefault();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Received amount cannot exceed Total amount',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ef4444',
                color: '#fff',
                iconColor: '#fff'
            });
            document.getElementById('receivedAmount').focus();
            return;
        }
        
        // Validate due date
        if ((total - received) > 0 && !dueDate) {
            e.preventDefault();
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Due Date is required when there is a remaining balance',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#f59e0b',
                color: '#fff',
                iconColor: '#fff'
            });
            document.getElementById('dueDate').focus();
            return;
        }
        
        // Show loading state
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-xs me-2"></span>Creating...';
        
        // Show success toast after form submits (page will redirect, but toast shows on next page if session flash exists)
    });
});

function updatePreview() {
    const total = parseFloat(document.getElementById('totalAmount').value) || 0;
    const received = parseFloat(document.getElementById('receivedAmount').value) || 0;
    const remaining = total - received;
    const preview = document.getElementById('invoicePreview');
    
    if (total > 0) {
        preview.style.display = 'block';
        document.getElementById('previewTotal').textContent = 'PKR ' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('previewReceived').textContent = 'PKR ' + received.toLocaleString('en-US', {minimumFractionDigits: 2});
        const remEl = document.getElementById('previewRemaining');
        remEl.textContent = 'PKR ' + Math.max(0, remaining).toLocaleString('en-US', {minimumFractionDigits: 2});
        remEl.style.color = remaining <= 0 ? '#10b981' : (received > 0 ? '#f59e0b' : '#ef4444');
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endsection