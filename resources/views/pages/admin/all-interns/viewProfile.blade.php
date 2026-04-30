@extends('layouts/layoutMaster')

@section('title', 'Intern Profile')

@section('vendor-style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --primary-color: #2b9a82;
        --primary-light: #e8f5f2;
        --primary-dark: #1e7b68;
        --gray-bg: #f8f9fc;
    }

    .spinner-border-sm { width: 1rem; height: 1rem; border-width: 0.2em; }
    .invoice-action-btn { padding: 0.25rem 0.5rem; font-size: 0.75rem; margin: 0 2px; }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; }
    .status-paid { background: #d1fae5; color: #065f46; }
    .status-unpaid { background: #fee2e2; color: #991b1b; }
    .status-partial { background: #fef3c7; color: #92400e; }
    .status-overdue { background: #dc2626; color: white; }
    .empty-state { text-align: center; padding: 40px; }
    .empty-state i { font-size: 48px; color: #cbd5e1; margin-bottom: 16px; }
    .toast-success { background: #10b981; color: white; }
    .toast-error { background: #ef4444; color: white; }

    .package-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }
    .package-card:hover { border-color: var(--primary-color); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .package-card.selected { border-color: var(--primary-color); background: var(--primary-light); }
    .package-title { font-weight: bold; font-size: 16px; margin-bottom: 8px; }
    .package-amount { font-size: 22px; font-weight: bold; color: var(--primary-color); margin-bottom: 5px; }
    .package-duration { font-size: 12px; color: #666; }

    .invoice-form-card {
        background: linear-gradient(135deg, #fff, var(--primary-light));
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid rgba(43, 154, 130, 0.2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .invoice-form-header { border-bottom: 2px solid var(--primary-color); padding-bottom: 12px; margin-bottom: 20px; }
    .info-row { display: flex; margin-bottom: 12px; padding: 8px 0; border-bottom: 1px dashed #e0e0e0; }
    .info-label { width: 130px; font-weight: 600; color: #555; }
    .info-value { flex: 1; color: #333; font-weight: 500; }
    .amount-highlight { font-size: 24px; font-weight: bold; color: var(--primary-color); }

    .contact-list li { padding: 8px 0; border-bottom: 1px solid #eee; }
    .contact-list li:last-child { border-bottom: none; }
    .contact-list i { width: 24px; color: var(--primary-color); }

    .profile-avatar { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 3px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .profile-initials { width: 120px; height: 120px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); color: white; font-size: 48px; font-weight: bold; margin: 0 auto; }
</style>
@endsection

@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
@endsection

@section('content')
<div class="row">
    <!-- ========== LEFT COLUMN ========== -->
    <div class="col-xl-4 col-lg-5 order-1 order-md-0">
        
        <!-- Profile Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body text-center py-4">
                @if ($interneeDetails->image && !str_starts_with($interneeDetails->image, 'data:image'))
                    <img class="profile-avatar mb-3" src="{{ asset($interneeDetails->image) }}" alt="{{ $interneeDetails->name }}">
                @else
                    <div class="profile-initials mb-3">
                        {{ strtoupper(substr($interneeDetails->name, 0, 2)) }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $interneeDetails->name }}</h4>
                <span class="badge bg-label-{{ strtolower($interneeDetails->status) == 'active' ? 'success' : 'warning' }} px-3 py-2">
                    {{ ucfirst($interneeDetails->status) }}
                </span>
            </div>
        </div>

        <!-- Contact Information Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="ti ti-phone me-2 text-primary"></i> Contact Information</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 contact-list">
                    <li><i class="ti ti-mail"></i> {{ $interneeDetails->email ?? 'N/A' }}</li>
                    <li><i class="ti ti-phone"></i> {{ $interneeDetails->phone ?? 'N/A' }}</li>
                    <li><i class="ti ti-map-pin"></i> {{ $interneeDetails->city ?? 'N/A' }}</li>
                    <li><i class="ti ti-code"></i> {{ $interneeDetails->technology ?? 'N/A' }}</li>
                </ul>
            </div>
        </div>

        <!-- Packages Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="ti ti-gift me-2 text-primary"></i> Select Package</h6>
            </div>
            <div class="card-body">
                <div class="package-card" data-package="basic" data-package-name="Basic Package" data-amount="5000" data-due-days="30">
                    <div class="package-title">Basic Package</div>
                    <div class="package-amount">PKR 5,000</div>
                    <div class="package-duration">1 Month</div>
                </div>
                <div class="package-card" data-package="standard" data-package-name="Standard Package" data-amount="12000" data-due-days="60">
                    <div class="package-title">Standard Package</div>
                    <div class="package-amount">PKR 12,000</div>
                    <div class="package-duration">3 Months</div>
                </div>
                <div class="package-card" data-package="premium" data-package-name="Premium Package" data-amount="20000" data-due-days="90">
                    <div class="package-title">Premium Package</div>
                    <div class="package-amount">PKR 20,000</div>
                    <div class="package-duration">6 Months</div>
                </div>
                <div id="selectedPackageInfo" class="mt-3"></div>
            </div>
        </div>

        <!-- Remove Button -->
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <button type="button" class="btn btn-label-danger w-100" id="removeInternBtn" data-id="{{ $interneeDetails->id }}">
                    <i class="ti ti-trash me-2"></i> Remove Intern
                </button>
            </div>
        </div>
    </div>

    <!-- ========== RIGHT COLUMN ========== -->
    <div class="col-xl-8 col-lg-7 order-1 order-md-0">
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="ti ti-user me-1"></i> Details
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                    <i class="ti ti-file-invoice me-1"></i> Invoice
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                    <i class="ti ti-credit-card me-1"></i> Payment
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            <!-- Details Tab -->
            <div class="tab-pane fade show active" id="details" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ti ti-info-circle me-2 text-primary"></i> Personal Information</h6>
                        <button type="button" class="btn btn-sm btn-primary edit-intern" data-bs-toggle="modal" 
                            data-bs-target="#editInternModal" data-id="{{ $interneeDetails->id }}" 
                            data-name="{{ $interneeDetails->name }}" data-email="{{ $interneeDetails->email }}" 
                            data-technology="{{ $interneeDetails->technology }}" data-status="{{ $interneeDetails->status }}">
                            <i class="ti ti-edit me-1"></i> Edit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label class="text-muted small">Full Name</label><p class="mb-0 fw-medium">{{ $interneeDetails->name ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Email</label><p class="mb-0 fw-medium">{{ $interneeDetails->email ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Phone</label><p class="mb-0 fw-medium">{{ $interneeDetails->phone ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">CNIC</label><p class="mb-0 fw-medium">{{ $interneeDetails->cnic ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Gender</label><p class="mb-0 fw-medium">{{ $interneeDetails->gender ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Join Date</label><p class="mb-0 fw-medium">{{ $interneeDetails->join_date ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">DOB</label><p class="mb-0 fw-medium">{{ $interneeDetails->birth_date ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">University</label><p class="mb-0 fw-medium">{{ $interneeDetails->university ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Country</label><p class="mb-0 fw-medium">{{ $interneeDetails->country ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">City</label><p class="mb-0 fw-medium">{{ $interneeDetails->city ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Technology</label><p class="mb-0 fw-medium">{{ $interneeDetails->technology ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Duration</label><p class="mb-0 fw-medium">{{ $interneeDetails->duration ?? 'N/A' }}</p></div>
                            <div class="col-md-6 mb-3"><label class="text-muted small">Status</label><p class="mb-0 fw-medium">{{ $interneeDetails->status ?? 'N/A' }}</p></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Tab -->
            <div class="tab-pane fade" id="invoices" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="ti ti-file-invoice me-2 text-primary"></i> Invoice Management</h6>
                        <button type="button" class="btn btn-sm btn-success" id="showInvoiceFormBtn">
                            <i class="ti ti-plus me-1"></i> Create Invoice
                        </button>
                    </div>
                    <div class="card-body">
                        
                        <!-- Invoice Creation Form -->
                        <div id="invoiceFormContainer" style="display: none;" class="invoice-form-card mb-4">
                            <div class="invoice-form-header">
                                <h6 class="mb-0"><i class="ti ti-receipt me-2"></i> New Invoice Details</h6>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Intern Name:</div>
                                        <div class="info-value" id="displayInternName">{{ $interneeDetails->name }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Email Address:</div>
                                        <div class="info-value" id="displayInternEmail">{{ $interneeDetails->email }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Contact Number:</div>
                                        <div class="info-value" id="displayInternPhone">{{ $interneeDetails->phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Technology:</div>
                                        <div class="info-value" id="displayInternTechnology">{{ $interneeDetails->technology ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <div class="info-label">Selected Package:</div>
                                        <div class="info-value fw-bold text-primary" id="displayPackageName">-</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <div class="info-label">Total Amount:</div>
                                        <div class="info-value amount-highlight" id="displayAmount">PKR 0</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-row">
                                        <div class="info-label">Due Date:</div>
                                        <div class="info-value" id="displayDueDate">-</div>
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary w-100 py-2" id="confirmCreateInvoiceBtn">
                                <i class="ti ti-check me-2"></i> Generate Invoice
                            </button>
                        </div>
                        
                        <!-- Invoices Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="invoicesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th><th>Total Amount</th><th>Received</th><th>Remaining</th><th>Status</th><th>Due Date</th><th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody><td><td colspan="7" class="text-center py-4"><div class="spinner-border text-primary"></div><br>Loading...</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Tab -->
            <div class="tab-pane fade" id="payments" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="ti ti-credit-card me-2 text-primary"></i> Payment History</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr><th>Date</th><th>Invoice #</th><th>Amount Paid</th><th>Payment Method</th><th>Received By</th></tr>
                                </thead>
                                <tbody><tr><td colspan="5" class="text-center py-4"><div class="empty-state"><i class="ti ti-credit-card-off"></i><p>No payment records found</p></div></td></tr></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Intern Modal -->
<div class="modal fade" id="editInternModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Intern</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form action="{{ route('update.intern.admin') }}" method="POST">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label>Name</label><input type="text" id="edit_name" name="name" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Email</label><input type="email" id="edit_email" name="email" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Technology</label><input type="text" id="edit_technology" name="technology" class="form-control"></div>
                        <div class="col-md-6 mb-3"><label>Status</label><select id="edit_status" name="status" class="form-select">
                            <option value="Interview">Interview</option><option value="Contact">Contact</option><option value="Test">Test</option>
                            <option value="Completed">Completed</option><option value="Active">Active</option><option value="Removed">Removed</option>
                        </select></div>
                    </div>
                    <div class="text-end"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Invoice</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <form id="editInvoiceForm">
                    @csrf @method('PUT')
                    <input type="hidden" id="edit_invoice_id" name="id">
                    <div class="mb-3"><label>Total Amount</label><input type="number" id="edit_total_amount" name="total_amount" class="form-control" step="0.01" required></div>
                    <div class="mb-3"><label>Received Amount</label><input type="number" id="edit_received_amount" name="received_amount" class="form-control" step="0.01" required></div>
                    <div class="mb-3"><label>Due Date</label><input type="date" id="edit_due_date" name="due_date" class="form-control"></div>
                    <div class="text-end"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Update</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPackageData = null;

function showToast(message, type = 'success') {
    Toastify({ text: message, duration: 3000, gravity: "top", position: "right", backgroundColor: type === 'success' ? "#10b981" : "#ef4444", stopOnFocus: true }).showToast();
}

// Package Selection
document.querySelectorAll('.package-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        
        selectedPackageData = {
            name: this.dataset.packageName,
            amount: parseInt(this.dataset.amount),
            dueDays: parseInt(this.dataset.dueDays)
        };
        
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
        document.getElementById('selectedPackageInfo').innerHTML = `<div class="alert alert-success py-2 mb-0"><small>✓ <strong>${selectedPackageData.name}</strong> selected<br>Amount: PKR ${selectedPackageData.amount.toLocaleString()} | Due: ${dueDate.toLocaleDateString()}</small></div>`;
    });
});

// Show Invoice Form
document.getElementById('showInvoiceFormBtn').addEventListener('click', function() {
    if (!selectedPackageData) {
        Swal.fire({ title: 'No Package Selected', text: 'Please select a package from the left panel first', icon: 'warning', confirmButtonColor: '#2b9a82' });
        return;
    }
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
    document.getElementById('displayPackageName').innerHTML = `<strong>${selectedPackageData.name}</strong>`;
    document.getElementById('displayAmount').innerHTML = `PKR ${selectedPackageData.amount.toLocaleString()}`;
    document.getElementById('displayDueDate').innerHTML = dueDate.toLocaleDateString();
    document.getElementById('invoiceFormContainer').style.display = 'block';
    document.getElementById('invoiceFormContainer').scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// Create Invoice via Controller
document.getElementById('confirmCreateInvoiceBtn').addEventListener('click', function() {
    if (!selectedPackageData) return;
    
    Swal.fire({
        title: 'Generate Invoice?',
        html: `<div style="text-align:left;"><p><strong>Intern:</strong> {{ $interneeDetails->name }}</p><p><strong>Package:</strong> ${selectedPackageData.name}</p><p><strong>Amount:</strong> PKR ${selectedPackageData.amount.toLocaleString()}</p></div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Generate',
        confirmButtonColor: '#2b9a82'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.invoices.create-from-package") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    intern_email: '{{ $interneeDetails->email }}',
                    intern_name: '{{ $interneeDetails->name }}',
                    intern_phone: '{{ $interneeDetails->phone ?? "" }}',
                    intern_technology: '{{ $interneeDetails->technology ?? "" }}',
                    package_name: selectedPackageData.name,
                    amount: selectedPackageData.amount,
                    due_days: selectedPackageData.dueDays
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(`✅ Invoice created! Amount: PKR ${selectedPackageData.amount.toLocaleString()}`, 'success');
                    document.getElementById('invoiceFormContainer').style.display = 'none';
                    document.querySelectorAll('.package-card').forEach(c => c.classList.remove('selected'));
                    document.getElementById('selectedPackageInfo').innerHTML = '';
                    selectedPackageData = null;
                    loadInvoices();
                } else {
                    showToast(data.message || 'Failed to create invoice', 'error');
                }
            })
            .catch(() => showToast('Error creating invoice', 'error'));
        }
    });
});

// Load Invoices
function loadInvoices() {
    fetch(`/admin/interns/{{ $interneeDetails->id }}/invoices`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#invoicesTable tbody');
            if (data.success && data.invoices && data.invoices.length > 0) {
                tbody.innerHTML = data.invoices.map(inv => `
                    <tr>
                        <td><strong>${inv.inv_id}</strong></td>
                        <td>PKR ${parseFloat(inv.total_amount).toFixed(2)}</td>
                        <td class="text-success">PKR ${parseFloat(inv.received_amount).toFixed(2)}</td>
                        <td class="text-warning">PKR ${parseFloat(inv.remaining_amount).toFixed(2)}</td>
                        <td><span class="badge ${inv.status === 'paid' ? 'bg-success' : (inv.status === 'partial' ? 'bg-warning' : 'bg-danger')}">${inv.status}</span></td>
                        <td>${inv.due_date ? new Date(inv.due_date).toLocaleDateString() : 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-invoice-btn" data-id="${inv.id}" data-total="${inv.total_amount}" data-received="${inv.received_amount}" data-due="${inv.due_date || ''}"><i class="ti ti-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-invoice-btn" data-id="${inv.id}" data-inv-id="${inv.inv_id}"><i class="ti ti-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
                attachInvoiceEvents();
            } else {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><div class="empty-state"><i class="ti ti-file-invoice"></i><p>No invoices found</p></div></td></tr>';
            }
        });
}

function attachInvoiceEvents() {
    document.querySelectorAll('.edit-invoice-btn').forEach(btn => btn.addEventListener('click', function() {
        document.getElementById('edit_invoice_id').value = this.dataset.id;
        document.getElementById('edit_total_amount').value = this.dataset.total;
        document.getElementById('edit_received_amount').value = this.dataset.received;
        document.getElementById('edit_due_date').value = this.dataset.due;
        new bootstrap.Modal(document.getElementById('editInvoiceModal')).show();
    }));
    document.querySelectorAll('.delete-invoice-btn').forEach(btn => btn.addEventListener('click', function() {
        Swal.fire({ title: 'Delete Invoice?', text: `Delete invoice ${this.dataset.invId}?`, icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#d33' }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/invoices/${this.dataset.id}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(res => res.json())
                .then(data => { if (data.success) { showToast('Invoice deleted!'); loadInvoices(); } });
            }
        });
    }));
}

// Edit Invoice Submit
document.getElementById('editInvoiceForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    fetch(`/admin/invoices/${document.getElementById('edit_invoice_id').value}/update`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            total_amount: document.getElementById('edit_total_amount').value,
            received_amount: document.getElementById('edit_received_amount').value,
            due_date: document.getElementById('edit_due_date').value
        })
    })
    .then(res => res.json())
    .then(data => { if (data.success) { showToast('Invoice updated!'); bootstrap.Modal.getInstance(document.getElementById('editInvoiceModal')).hide(); loadInvoices(); } });
});

// Remove Intern
document.getElementById('removeInternBtn')?.addEventListener('click', function() {
    Swal.fire({ title: 'Remove Intern?', text: "This will freeze the intern's portal access!", icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#d33' }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/interns/${this.dataset.id}/remove-ajax`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(res => res.json())
            .then(data => { if (data.success) { showToast('Intern removed!'); setTimeout(() => window.location.href = '{{ route("all-interns-admin") }}', 1500); } });
        }
    });
});

// Edit Modal Population
document.querySelectorAll('.edit-intern').forEach(btn => btn.addEventListener('click', function() {
    document.getElementById('edit_id').value = this.dataset.id;
    document.getElementById('edit_name').value = this.dataset.name;
    document.getElementById('edit_email').value = this.dataset.email;
    document.getElementById('edit_technology').value = this.dataset.technology;
    document.getElementById('edit_status').value = this.dataset.status;
}));

// Load on Page Load
document.addEventListener('DOMContentLoaded', loadInvoices);
</script>
@endsection