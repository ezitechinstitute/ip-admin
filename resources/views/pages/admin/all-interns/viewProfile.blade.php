@extends('layouts/layoutMaster')

{{-- 
===========================================
PRIMARY INVOICE CREATION WORKFLOW
===========================================
This is the MAIN profile page where admins:
1. Select a package (left panel)
2. Go to Invoices tab  
3. Click "Create Invoice" button
4. Invoice form auto-fills from selected package
5. Generates invoice via AJAX (createFromPackage route)

The inline form is inside #invoiceFormContainer div
JavaScript functions: showInvoiceForm(), createInvoice(), etc.
===========================================
--}}

@section('title', 'Intern Profile')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.5);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        --primary: #2b9a82;
        --primary-gradient: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%);
    }
    body { background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 50%, #f8fafb 100%); font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .premium-card { background: rgba(255,255,255,0.8); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.6); border-radius: var(--card-radius); box-shadow: 0 8px 32px rgba(0,0,0,0.05); transition: var(--transition-smooth); position: relative; overflow: hidden; }
    .premium-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent); }
    .premium-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
    .profile-banner { position: relative; height: 220px; background: linear-gradient(160deg, #0f1729 0%, #1a2744 30%, #2b9a82 70%, #3b82f6 100%); overflow: hidden; }
    .profile-banner::before { content: ''; position: absolute; top: -60%; right: -15%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%); border-radius: 50%; }
    .profile-banner::after { content: ''; position: absolute; bottom: -40%; left: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%); border-radius: 50%; }
    .banner-particle { position: absolute; background: rgba(255,255,255,0.1); border-radius: 50%; animation: floatUp 8s infinite ease-in; }
    @keyframes floatUp { 0% { transform: translateY(100%) scale(0); opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { transform: translateY(-100%) scale(1.5); opacity: 0; } }
    .avatar-section { margin-top: -65px; position: relative; z-index: 10; display: flex; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap; }
    .avatar-wrapper { position: relative; flex-shrink: 0; }
    .avatar-image { width: 140px; height: 140px; object-fit: cover; border-radius: 50%; border: 5px solid white; box-shadow: 0 8px 32px rgba(0,0,0,0.15); transition: var(--transition-smooth); background: #f1f5f9; }
    .avatar-initials { width: 140px; height: 140px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: var(--primary-gradient); color: white; font-size: 52px; font-weight: 700; border: 5px solid white; box-shadow: 0 8px 32px rgba(0,0,0,0.15); letter-spacing: 2px; }
    .avatar-wrapper:hover .avatar-image, .avatar-wrapper:hover .avatar-initials { transform: scale(1.05); }
    .status-dot { position: absolute; bottom: 12px; right: 8px; width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    .status-active { background: #10b981; animation: pulse 2s infinite; }
    .status-interview { background: #3b82f6; }
    .status-contact { background: #8b5cf6; }
    .status-test { background: #f59e0b; }
    .status-completed { background: #6b7280; }
    .status-removed { background: #ef4444; }
    @keyframes pulse { 0%,100% { box-shadow: 0 0 0 0 rgba(16,185,129,0.4); } 50% { box-shadow: 0 0 0 8px rgba(16,185,129,0); } }
    .stat-mini-card { background: rgba(255,255,255,0.7); border-radius: 1rem; padding: 1rem 1.25rem; border: 1px solid rgba(0,0,0,0.04); transition: var(--transition-smooth); cursor: pointer; }
    .stat-mini-card:hover { background: rgba(255,255,255,0.95); transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .contact-item { display: flex; align-items: center; gap: 1rem; padding: 0.85rem 1rem; border-radius: 0.75rem; transition: var(--transition-smooth); cursor: pointer; }
    .contact-item:hover { background: rgba(43,154,130,0.04); transform: translateX(6px); }
    .contact-icon-circle { width: 42px; height: 42px; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; }
    .package-card { background: white; border: 2px solid rgba(0,0,0,0.05); border-radius: 1rem; padding: 1.25rem; cursor: pointer; transition: var(--transition-smooth); position: relative; overflow: hidden; }
    .package-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--primary-gradient); transform: scaleX(0); transform-origin: left; transition: transform 0.4s cubic-bezier(0.175,0.885,0.32,1.275); }
    .package-card:hover { border-color: rgba(43,154,130,0.3); transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,0.08); }
    .package-card:hover::before { transform: scaleX(1); }
    .package-card.selected { border-color: #2b9a82; background: rgba(43,154,130,0.03); box-shadow: 0 4px 20px rgba(43,154,130,0.1); }
    .package-card.selected::before { transform: scaleX(1); }
    .package-radio { width: 24px; height: 24px; border: 2px solid #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: var(--transition-smooth); flex-shrink: 0; }
    .package-card.selected .package-radio { border-color: #2b9a82; background: #2b9a82; }
    .package-card.selected .package-radio::after { content: '✓'; color: white; font-size: 13px; font-weight: bold; }
    .package-price { font-size: 1.25rem; font-weight: 700; }
    .custom-tabs { display: flex; gap: 0.25rem; padding: 0.5rem; background: rgba(0,0,0,0.03); border-radius: 0.75rem; margin-bottom: 1.5rem; }
    .custom-tab { flex: 1; padding: 0.65rem 1rem; border: none; background: transparent; font-weight: 600; font-size: 0.8rem; color: #6c86a3; cursor: pointer; transition: var(--transition-smooth); border-radius: 0.6rem; white-space: nowrap; position: relative; }
    .custom-tab.active { background: white; color: #2b9a82; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .custom-tab:hover:not(.active) { color: #2b9a82; background: rgba(255,255,255,0.5); }
    .detail-item { padding: 0.9rem 1rem; border-radius: 0.75rem; background: rgba(0,0,0,0.015); transition: var(--transition-smooth); }
    .detail-item:hover { background: rgba(43,154,130,0.03); }
    .detail-label { font-size: 0.68rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin-bottom: 0.3rem; }
    .detail-value { font-weight: 600; color: #1e293b; font-size: 0.9rem; }
    .invoice-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .invoice-table thead th { background: rgba(0,0,0,0.02); padding: 0.85rem 1rem; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6c86a3; border-bottom: 2px solid rgba(0,0,0,0.05); white-space: nowrap; }
    .invoice-table tbody td { padding: 0.9rem 1rem; vertical-align: middle; border-bottom: 1px solid rgba(0,0,0,0.04); }
    .invoice-table tbody tr { transition: var(--transition-smooth); }
    .invoice-table tbody tr:hover { background: rgba(43,154,130,0.02); }
    .status-badge { padding: 0.35rem 0.8rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
    .badge-paid { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .badge-pending { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
    .badge-overdue { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .badge-partial { background: rgba(139,92,246,0.1); color: #7c3aed; border: 1px solid rgba(139,92,246,0.2); }
    .btn-icon-action { width: 34px; height: 34px; border-radius: 0.5rem; display: inline-flex; align-items: center; justify-content: center; transition: var(--transition-smooth); border: none; cursor: pointer; }
    .btn-edit { background: rgba(59,130,246,0.08); color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: white; transform: scale(1.1); }
    .btn-delete { background: rgba(239,68,68,0.08); color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; transform: scale(1.1); }
    .btn-primary-premium { background: var(--primary-gradient); color: white; border: none; padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 600; font-size: 0.8rem; transition: var(--transition-smooth); box-shadow: 0 4px 15px rgba(43,154,130,0.3); cursor: pointer; }
    .btn-primary-premium:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(43,154,130,0.4); color: white; }
    .btn-outline-danger-premium { background: transparent; color: #ef4444; border: 1.5px solid rgba(239,68,68,0.3); padding: 0.6rem 1.5rem; border-radius: 50px; font-weight: 600; font-size: 0.8rem; transition: var(--transition-smooth); cursor: pointer; }
    .btn-outline-danger-premium:hover { background: #ef4444; color: white; border-color: #ef4444; }
    .amount-display { font-size: 1.6rem; font-weight: 800; color: #2b9a82; line-height: 1; }
    .toast-custom { position: fixed; top: 24px; right: 24px; z-index: 99999; padding: 0.9rem 1.5rem; border-radius: 0.75rem; color: white; font-weight: 600; font-size: 0.85rem; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: slideInRight 0.4s cubic-bezier(0.175,0.885,0.32,1.275); display: flex; align-items: center; gap: 0.5rem; }
    .toast-success { background: rgba(16,185,129,0.95); }
    .toast-error { background: rgba(239,68,68,0.95); }
    @keyframes slideInRight { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(120%); opacity: 0; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.5s ease-out forwards; }
    .btn-loading { pointer-events: none; opacity: 0.7; }
    .spinner-border-xs { width: 0.9rem; height: 0.9rem; border-width: 0.12em; display: inline-block; }
    .invoice-form-premium { background: white; border-radius: 1.25rem; overflow: hidden; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 8px 32px rgba(0,0,0,0.08); animation: slideDown 0.4s cubic-bezier(0.175,0.885,0.32,1.275); }
    @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .form-premium-header { background: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%); padding: 1.25rem 1.5rem; }
    .form-premium-header small { color: rgba(255,255,255,0.7)!important; }
    .btn-close-premium { width: 34px; height: 34px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
    .btn-close-premium:hover { background: rgba(255,255,255,0.35); transform: rotate(90deg); }
    .form-premium-body { padding: 1.5rem; }
    .form-premium-footer { padding: 1rem 1.5rem; background: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: flex-end; gap: 0.75rem; }
    .info-cards-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem; margin-bottom: 1.5rem; }
    .info-card-mini { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: rgba(0,0,0,0.015); border-radius: 0.75rem; border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s ease; }
    .highlight-box { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: 0.75rem; border: 2px solid transparent; transition: all 0.3s ease; }
    .highlight-box-primary { background: rgba(43,154,130,0.04); border-color: rgba(43,154,130,0.2); }
    .highlight-box-warning { background: rgba(245,158,11,0.04); border-color: rgba(245,158,11,0.2); }
    .highlight-box-info { background: rgba(59,130,246,0.04); border-color: rgba(59,130,246,0.2); }
    .highlight-box-icon { width: 40px; height: 40px; border-radius: 0.6rem; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .highlight-box-primary .highlight-box-icon { background: rgba(43,154,130,0.1); color: #2b9a82; }
    .highlight-box-warning .highlight-box-icon { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .highlight-box-info .highlight-box-icon { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .btn-generate-premium { background: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%); color: white; border: none; padding: 0.7rem 1.75rem; border-radius: 50px; font-weight: 600; font-size: 0.82rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(43,154,130,0.3); display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-generate-premium:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(43,154,130,0.4); }
    .btn-generate-premium:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .btn-cancel-premium { background: transparent; color: #6c86a3; border: 1.5px solid rgba(0,0,0,0.1); padding: 0.7rem 1.75rem; border-radius: 50px; font-weight: 600; font-size: 0.82rem; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; }
    .btn-cancel-premium:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,0.03); }
    @media (max-width: 992px) { .profile-banner { height: 180px; } .avatar-section { margin-top: -50px; } .avatar-image, .avatar-initials { width: 110px; height: 110px; } .avatar-initials { font-size: 40px; } }
    @media (max-width: 768px) { .profile-banner { height: 150px; } .avatar-section { margin-top: -40px; flex-direction: column; align-items: center; text-align: center; } .avatar-image, .avatar-initials { width: 90px; height: 90px; } .avatar-initials { font-size: 32px; } .custom-tabs { flex-wrap: nowrap; overflow-x: auto; } .custom-tab { flex: none; } }
    .swal2-container { z-index: 99999 !important; }

    .form-label-premium{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6c86a3;margin-bottom:.5rem;display:flex;align-items:center;gap:.4rem;}
.form-header-icon{width:44px;height:44px;border-radius:.75rem;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
.info-card-icon{width:38px;height:38px;border-radius:.6rem;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;}
</style>
@endsection

@section('content')
<div class="container-xxl py-4">
    <div class="premium-card mb-4 animate-in" style="padding: 0 !important; animation-delay: 0.05s;">
        <div class="profile-banner">
            @for($i = 1; $i <= 6; $i++)
            <div class="banner-particle" style="width: {{ rand(6, 12) }}px; height: {{ rand(6, 12) }}px; left: {{ rand(10, 85) }}%; animation-delay: {{ $i * 0.5 }}s; animation-duration: {{ rand(5, 8) }}s;"></div>
            @endfor
        </div>
        <div class="px-4 pb-4">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    @php $userName = $interneeDetails->name ?? 'User'; $initials = strtoupper(substr($userName, 0, 2)); $imageUrl = $interneeDetails->profileImageUrl; @endphp
                    @if($imageUrl) <img src="{{ $imageUrl }}" alt="{{ $userName }}" class="avatar-image" id="profileAvatarImage" onerror="this.style.display='none'; document.getElementById('profileAvatarInitials').style.display='flex';"><div class="avatar-initials" id="profileAvatarInitials" style="display: none;">{{ $initials }}</div>
                    @else <div class="avatar-initials" id="profileAvatarInitials">{{ $initials }}</div> @endif
                    @php $statusClass = match(strtolower($interneeDetails->status ?? '')) { 'active' => 'status-active', 'interview' => 'status-interview', 'contact' => 'status-contact', 'test' => 'status-test', 'completed' => 'status-completed', 'removed' => 'status-removed', default => 'status-interview' }; @endphp
                    <span class="status-dot {{ $statusClass }}" title="{{ ucfirst($interneeDetails->status ?? 'Unknown') }}" data-bs-toggle="tooltip"></span>
                </div>
                <div class="flex-grow-1 pb-2">
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">{{ $userName }}</h3>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-code-slash text-primary me-1"></i>{{ $interneeDetails->technology ?? 'N/A' }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-geo-alt text-success me-1"></i>{{ $interneeDetails->city ?? 'N/A' }}</span>
                        <span class="badge bg-light text-dark border px-3 py-2"><i class="bi bi-calendar3 text-info me-1"></i>Joined {{ $interneeDetails->join_date ?? 'N/A' }}</span>
                        @php $statusBadgeClass = match(strtolower($interneeDetails->status ?? '')) { 'active' => 'bg-success', 'removed' => 'bg-danger', 'completed' => 'bg-secondary', default => 'bg-warning' }; @endphp
                        <span class="badge {{ $statusBadgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $statusBadgeClass) }} rounded-pill px-3 py-1">{{ ucfirst($interneeDetails->status ?? 'Unknown') }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2 pb-2 flex-shrink-0">
                    <button class="btn-primary-premium" id="editInternBtn" data-bs-toggle="modal" data-bs-target="#editInternModal" data-id="{{ $interneeDetails->id }}" data-name="{{ $interneeDetails->name }}" data-email="{{ $interneeDetails->email }}" data-phone="{{ $interneeDetails->phone }}" data-cnic="{{ $interneeDetails->cnic }}" data-gender="{{ $interneeDetails->gender }}" data-dob="{{ $interneeDetails->birth_date }}" data-country="{{ $interneeDetails->country }}" data-city="{{ $interneeDetails->city }}" data-university="{{ $interneeDetails->university }}" data-technology="{{ $interneeDetails->technology }}" data-duration="{{ $interneeDetails->duration }}" data-intern-type="{{ $interneeDetails->intern_type }}" data-status="{{ $interneeDetails->status }}" data-bio="{{ $interneeDetails->bio }}"><i class="bi bi-pencil-square me-1"></i> Edit</button>
                    <button class="btn-outline-danger-premium" id="removeInternBtn" data-id="{{ $interneeDetails->id }}"><i class="bi bi-trash me-1"></i> Remove</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.1s;"><div class="stat-mini-card text-center"><div class="fw-bold text-muted small mb-1">Intern ID</div><div class="fw-bold fs-5 text-primary">#{{ $interneeDetails->id ?? 'N/A' }}</div></div></div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.15s;"><div class="stat-mini-card text-center"><div class="fw-bold text-muted small mb-1">Email</div><div class="fw-bold small text-truncate">{{ $interneeDetails->email ?? 'N/A' }}</div></div></div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.2s;"><div class="stat-mini-card text-center"><div class="fw-bold text-muted small mb-1">Phone</div><div class="fw-bold small">{{ $interneeDetails->phone ?? 'N/A' }}</div></div></div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.25s;"><div class="stat-mini-card text-center"><div class="fw-bold text-muted small mb-1">University</div><div class="fw-bold small text-truncate">{{ $interneeDetails->university ?? 'N/A' }}</div></div></div>
    </div>

        <div class="row g-4">
        <div class="col-xl-4 col-lg-5">
            <div class="premium-card p-4 mb-4 animate-in" style="animation-delay: 0.3s;">
                <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2" style="color: #2b9a82;"></i>Contact Information</h5>
                <div class="d-flex flex-column gap-1">
                    <div class="contact-item"><div class="contact-icon-circle bg-primary bg-opacity-10 text-primary"><i class="bi bi-envelope-fill"></i></div><div class="flex-grow-1 min-width-0"><small class="text-muted d-block" style="font-size: 0.68rem;">Email Address</small><span class="fw-medium small text-truncate d-block">{{ $interneeDetails->email ?? 'N/A' }}</span></div></div>
                    <div class="contact-item"><div class="contact-icon-circle bg-success bg-opacity-10 text-success"><i class="bi bi-telephone-fill"></i></div><div><small class="text-muted d-block" style="font-size: 0.68rem;">Phone Number</small><span class="fw-medium small">{{ $interneeDetails->phone ?? 'N/A' }}</span></div></div>
                    <div class="contact-item"><div class="contact-icon-circle bg-info bg-opacity-10 text-info"><i class="bi bi-geo-alt-fill"></i></div><div><small class="text-muted d-block" style="font-size: 0.68rem;">Location</small><span class="fw-medium small">{{ $interneeDetails->city ?? 'N/A' }}, {{ $interneeDetails->country ?? 'N/A' }}</span></div></div>
                    <div class="contact-item"><div class="contact-icon-circle bg-warning bg-opacity-10 text-warning"><i class="bi bi-building"></i></div><div class="flex-grow-1 min-width-0"><small class="text-muted d-block" style="font-size: 0.68rem;">University</small><span class="fw-medium small text-truncate d-block">{{ $interneeDetails->university ?? 'N/A' }}</span></div></div>
                    <div class="contact-item"><div class="contact-icon-circle" style="background: rgba(139,92,246,0.1); color: #8b5cf6;"><i class="bi bi-credit-card"></i></div><div><small class="text-muted d-block" style="font-size: 0.68rem;">CNIC</small><span class="fw-medium small">{{ $interneeDetails->cnic ?? 'N/A' }}</span></div></div>
                </div>
            </div>
            <div class="premium-card p-4 animate-in" style="animation-delay: 0.35s;">
                <h5 class="fw-bold mb-3"><i class="bi bi-gift-fill me-2" style="color: #2b9a82;"></i>Select Package</h5>
                <div class="d-flex flex-column gap-2" id="packageList">
                    @foreach($packages as $pkg)
                    <div class="package-card package-option" data-package-id="{{ $pkg['slug'] }}" data-package-name="{{ $pkg['name'] }}" data-amount="{{ $pkg['amount'] }}" data-due-days="{{ $pkg['due_days'] }}" onclick="selectPackage(this)">
                        <div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center gap-3"><div class="package-radio"></div><div><div class="fw-bold small">{{ $pkg['name'] }}</div></div></div><div class="package-price" style="color: {{ $pkg['color'] }};">{{ \App\Services\PackageService::formatAmount($pkg['amount']) }}</div></div>
                    </div>
                    @endforeach
                </div>
                <div id="selectedPackageInfo" class="mt-3"></div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7">
            <div class="premium-card p-4 animate-in" style="animation-delay: 0.4s;">
                <div class="custom-tabs" id="tabNavigation">
                    <button class="custom-tab active" data-tab="details" onclick="switchTab('details')"><i class="bi bi-info-circle me-1"></i> Details</button>
                    <button class="custom-tab" data-tab="invoices" onclick="switchTab('invoices')"><i class="bi bi-receipt me-1"></i> Invoices</button>
                    <button class="custom-tab" data-tab="payments" onclick="switchTab('payments')"><i class="bi bi-credit-card me-1"></i> Payments</button>
                </div>
                <div class="tab-panel" id="tab-details">
                    <div class="row g-2">

                        @php
                            $details = [
                                ['label' => 'Full Name', 'value' => $interneeDetails->name, 'icon' => 'bi-person-fill', 'color' => 'primary'],
                                ['label' => 'Email', 'value' => $interneeDetails->email, 'icon' => 'bi-envelope-fill', 'color' => 'info'],
                                ['label' => 'Phone', 'value' => $interneeDetails->phone, 'icon' => 'bi-telephone-fill', 'color' => 'success'],
                                ['label' => 'CNIC', 'value' => $interneeDetails->cnic, 'icon' => 'bi-card-text', 'color' => 'warning'],
                                ['label' => 'Gender', 'value' => $interneeDetails->gender, 'icon' => 'bi-gender-ambiguous', 'color' => 'secondary'],
                                ['label' => 'Date of Birth', 'value' => $interneeDetails->birth_date, 'icon' => 'bi-cake2-fill', 'color' => 'danger'],
                                ['label' => 'Join Date', 'value' => $interneeDetails->join_date, 'icon' => 'bi-calendar-check-fill', 'color' => 'success'],
                                ['label' => 'University', 'value' => $interneeDetails->university, 'icon' => 'bi-building', 'color' => 'warning'],
                                ['label' => 'Country', 'value' => $interneeDetails->country, 'icon' => 'bi-globe2', 'color' => 'info'],
                                ['label' => 'City', 'value' => $interneeDetails->city, 'icon' => 'bi-geo-alt-fill', 'color' => 'danger'],
                                ['label' => 'Technology', 'value' => $interneeDetails->technology, 'icon' => 'bi-code-slash', 'color' => 'primary'],
                                ['label' => 'Duration', 'value' => $interneeDetails->duration, 'icon' => 'bi-clock-fill', 'color' => 'info'],
                                ['label' => 'Registered Package', 'value' => $interneeDetails->intern_type ?? 'N/A', 'icon' => 'bi-gift-fill', 'color' => 'warning'],
                                ['label' => 'Status', 'value' => $interneeDetails->status, 'icon' => 'bi-flag-fill', 'color' => 'success'],
                            ];
                        @endphp

                        @foreach($details as $detail)
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">
                                    <i class="bi {{ $detail['icon'] }} text-{{ $detail['color'] }} me-1"></i>
                                    {{ $detail['label'] }}
                                </div>
                                <div class="detail-value">{{ $detail['value'] ?? 'N/A' }}</div>
                            </div>
                        </div>
 @endforeach
                    </div>
                </div>
                <div class="tab-panel" id="tab-invoices" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4"><h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-plus me-2" style="color: #2b9a82;"></i>Invoice Management</h6><button class="btn-primary-premium btn-sm" id="showInvoiceFormBtn" onclick="showInvoiceForm()"><i class="bi bi-plus-lg me-1"></i> Create Invoice</button></div>
                    <div id="invoiceFormContainer" style="display: none;" class="invoice-form-premium mb-4">
                        <div class="form-premium-header"><div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center gap-3"><div class="form-header-icon"><i class="bi bi-receipt-cutoff"></i></div><div><h6 class="fw-bold mb-0 text-white">Create New Invoice</h6><small class="text-white-50">Auto-filled from selected package</small></div></div><button type="button" class="btn-close-premium" onclick="hideInvoiceForm()"><i class="bi bi-x-lg"></i></button></div></div>
                        <div class="form-premium-body">
                            <div class="info-cards-row"><div class="info-card-mini"><div class="info-card-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-fill"></i></div><div><small class="text-muted d-block">Intern</small><span class="fw-bold small">{{ $interneeDetails->name }}</span></div></div><div class="info-card-mini"><div class="info-card-icon bg-success bg-opacity-10 text-success"><i class="bi bi-envelope-fill"></i></div><div><small class="text-muted d-block">Email</small><span class="fw-medium small text-truncate" style="max-width: 160px;">{{ $interneeDetails->email }}</span></div></div><div class="info-card-mini"><div class="info-card-icon bg-info bg-opacity-10 text-info"><i class="bi bi-telephone-fill"></i></div><div><small class="text-muted d-block">Contact</small><span class="fw-medium small">{{ $interneeDetails->phone ?? 'N/A' }}</span></div></div><div class="info-card-mini"><div class="info-card-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-code-slash"></i></div><div><small class="text-muted d-block">Technology</small><span class="fw-medium small">{{ $interneeDetails->technology ?? 'N/A' }}</span></div></div></div>
                            <div class="row g-3 mb-3"><div class="col-md-4"><label class="form-label-premium"><i class="bi bi-gift-fill"></i> Selected Package</label><div class="highlight-box highlight-box-primary"><div class="highlight-box-icon"><i class="bi bi-box-seam-fill"></i></div><div><div class="fw-bold" id="displayPackageName" style="font-size: 0.9rem;">-</div><small class="text-muted">Package</small></div></div></div><div class="col-md-4"><label class="form-label-premium"><i class="bi bi-cash-stack"></i> Total Amount</label><div class="highlight-box highlight-box-warning"><div class="highlight-box-icon"><i class="bi bi-currency-dollar"></i></div><div><div class="fw-bold fs-5" id="displayAmount" style="color: #f59e0b;">PKR 0</div><small class="text-muted">Amount (PKR)</small></div></div></div><div class="col-md-4"><label class="form-label-premium"><i class="bi bi-calendar-event"></i> Due Date</label><div class="highlight-box highlight-box-info"><div class="highlight-box-icon"><i class="bi bi-calendar-check"></i></div><div><div class="fw-bold" id="displayDueDate" style="font-size: 0.85rem;">-</div><small class="text-muted">Due Date</small></div></div></div></div>
                        </div>
                        <div class="form-premium-footer"><button type="button" class="btn-cancel-premium" onclick="hideInvoiceForm()"><i class="bi bi-x-circle me-1"></i> Cancel</button><button type="button" class="btn-generate-premium" id="confirmCreateInvoiceBtn" onclick="createInvoice()"><i class="bi bi-check-circle me-1"></i> Generate Invoice</button></div>
                    </div>
                    <div class="table-responsive"><table class="invoice-table" id="invoicesTable"><thead><tr><th>Invoice #</th><th>Total</th><th>Received</th><th>Balance</th><th>Status</th><th>Due Date</th><th>Actions</th></tr></thead><tbody id="invoicesTableBody"><tr><td colspan="7" class="text-center py-5"><span class="spinner-border spinner-border-xs text-primary" role="status"></span><span class="ms-2 text-muted small">Loading invoices...</span></td></tr></tbody></table></div>
                </div>
                <div class="tab-panel" id="tab-payments" style="display: none;">
                    <div class="row g-2 mb-3" id="paymentStatsRow"><div class="col-4"><div class="p-2 rounded-3 text-center" style="background: rgba(16,185,129,0.06);"><div class="fw-bold text-success" id="statTotal" style="font-size: 1.1rem;">PKR 0</div><small class="text-muted">Total Paid</small></div></div><div class="col-4"><div class="p-2 rounded-3 text-center" style="background: rgba(59,130,246,0.06);"><div class="fw-bold text-primary" id="statMonth" style="font-size: 1.1rem;">PKR 0</div><small class="text-muted">This Month</small></div></div><div class="col-4"><div class="p-2 rounded-3 text-center" style="background: rgba(139,92,246,0.06);"><div class="fw-bold text-purple" id="statToday" style="font-size: 1.1rem; color: #8b5cf6;">PKR 0</div><small class="text-muted">Today</small></div></div></div>
                    <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2" style="color: #2b9a82;"></i>Payment History</h6><div class="d-flex gap-2"><input type="date" id="paymentFromDate" class="form-control form-control-sm" style="width: 130px;" onchange="loadPayments()" title="From Date"><input type="date" id="paymentToDate" class="form-control form-control-sm" style="width: 130px;" onchange="loadPayments()" title="To Date"><input type="search" id="paymentSearch" class="form-control form-control-sm" style="width: 150px;" placeholder="Search invoice..." onkeyup="paymentSearchDebounce()"></div></div>
                    <div class="table-responsive"><table class="invoice-table"><thead><tr><th>Date</th><th>Invoice #</th><th>Amount</th><th>Method</th><th>Received By</th></tr></thead><tbody id="paymentsTableBody"><tr><td colspan="5" class="text-center py-5"><span class="spinner-border spinner-border-xs text-primary" role="status"></span><span class="ms-2 text-muted small">Loading payments...</span></td></tr></tbody></table></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Intern Modal -->
<div class="modal fade" id="editInternModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content premium-card"><div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="ti ti-edit me-2 text-primary"></i>Edit Intern</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="editInternForm">@csrf<input type="hidden" id="edit_id" name="id">
    <div class="row g-3"><div class="col-12"><h6 class="fw-bold mb-2" style="font-size:0.75rem;color:#2b9a82;"><i class="ti ti-user-circle me-1"></i>Personal Information</h6></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Full Name</label><input type="text" id="edit_name" name="name" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Email Address</label><input type="email" id="edit_email" name="email" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Phone Number</label><input type="text" id="edit_phone" name="phone" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">CNIC</label><input type="text" id="edit_cnic" name="cnic" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Gender</label><select id="edit_gender" name="gender" class="form-select rounded-3"><option value="">Select Gender</option><option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option></select></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Date of Birth</label><input type="date" id="edit_dob" name="birth_date" class="form-control rounded-3"></div>
    <div class="col-12 mt-2"><h6 class="fw-bold mb-2" style="font-size:0.75rem;color:#2b9a82;"><i class="ti ti-map-pin me-1"></i>Location & Education</h6></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Country</label><input type="text" id="edit_country" name="country" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">City</label><input type="text" id="edit_city" name="city" class="form-control rounded-3"></div>
    <div class="col-md-12"><label class="form-label fw-semibold small">University</label><input type="text" id="edit_university" name="university" class="form-control rounded-3"></div>
    <div class="col-12 mt-2"><h6 class="fw-bold mb-2" style="font-size:0.75rem;color:#2b9a82;"><i class="ti ti-briefcase me-1"></i>Professional Information</h6></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Technology</label><input type="text" id="edit_technology" name="technology" class="form-control rounded-3"></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Duration</label><select id="edit_duration" name="duration" class="form-select rounded-3"><option value="">Select Duration</option><option value="1 Month">1 Month</option><option value="2 Months">2 Months</option><option value="3 Months">3 Months</option><option value="6 Months">6 Months</option></select></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Intern Type</label><select id="edit_intern_type" name="intern_type" class="form-select rounded-3"><option value="">Select Type</option><option value="Training Internship">Training Internship</option><option value="Paid Internship">Paid Internship</option><option value="Volunteer">Volunteer</option></select></div>
    <div class="col-md-6"><label class="form-label fw-semibold small">Status</label><select id="edit_status" name="status" class="form-select rounded-3"><option value="Active">Active</option><option value="Pending">Pending</option><option value="Interview">Interview</option><option value="Complete">Complete</option><option value="Reject">Reject</option><option value="Terminate">Terminate</option></select></div>
    <div class="col-12"><label class="form-label fw-semibold small">Bio / Notes</label><textarea id="edit_bio" name="bio" class="form-control rounded-3" rows="3" placeholder="Additional information..."></textarea></div></div>
    <div class="mt-4 text-end"><button type="button" class="btn btn-secondary me-2 rounded-3" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary-gradient">Update Intern</button></div></form></div></div></div>
</div>

<!-- Reason Modal -->
<div class="modal fade" id="reasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content premium-card"><div class="modal-header border-0"><h5 class="modal-title fw-bold" id="reasonModalTitle"><i class="ti ti-alert-triangle me-2 text-warning"></i>Provide Reason</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><form id="reasonForm" enctype="multipart/form-data">@csrf
    <input type="hidden" id="reason_intern_id" name="intern_id"><input type="hidden" id="reason_new_status" name="new_status"><input type="hidden" id="reason_intern_name" name="intern_name"><input type="hidden" id="reason_intern_email" name="intern_email"><input type="hidden" id="reason_intern_technology" name="intern_technology">
    <div class="mb-3"><label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label><textarea name="reason" id="reason_text" class="form-control rounded-3" rows="4" placeholder="Please provide reason for this action..." required></textarea></div>
    <div class="mb-3"><label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attach Screenshot</label><input type="file" name="screenshot" id="reason_screenshot" class="form-control rounded-3" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"><small class="text-muted">Max 5MB | Allowed: .pdf, .jpg, .jpeg, .png, .doc, .docx</small></div>
    <div class="alert alert-info py-2"><small><i class="ti ti-info-circle"></i>This reason will be saved for audit purposes.</small></div>
    <div class="text-end"><button type="button" class="btn btn-secondary me-2 rounded-3" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary-gradient">Confirm & Update</button></div></form></div></div></div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content premium-card border-0"><div class="modal-header border-0 pb-0"><h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2" style="color:#2b9a82;"></i>Edit Invoice</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
    <div class="modal-body"><form id="editInvoiceForm" onsubmit="updateInvoice(event)">@csrf @method('PUT')<input type="hidden" id="edit_invoice_id" name="id">
    <div class="mb-3"><label class="form-label fw-semibold small">Total Amount (PKR)</label><input type="number" id="edit_total_amount" name="total_amount" class="form-control rounded-pill" step="0.01" required></div>
    <div class="mb-3"><label class="form-label fw-semibold small">Received Amount (PKR)</label><input type="number" id="edit_received_amount" name="received_amount" class="form-control rounded-pill" step="0.01" required></div>
    <div class="mb-3"><label class="form-label fw-semibold small">Due Date</label><input type="date" id="edit_due_date" name="due_date" class="form-control rounded-pill"></div>
    <div class="d-flex justify-content-end gap-2 mt-4"><button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-primary-premium" id="updateInvoiceBtn"><i class="bi bi-check-lg me-1"></i>Update Invoice</button></div></form></div></div></div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@php
$safeInternData = ['id' => $interneeDetails->id, 'name' => $interneeDetails->name, 'email' => $interneeDetails->email, 'phone' => $interneeDetails->phone ?? '', 'technology' => $interneeDetails->technology ?? ''];
@endphp
<script>
const internData = @json($safeInternData);
const paymentSearchDebounce = (() => { let t; return () => { clearTimeout(t); t = setTimeout(loadPayments, 500); }; })();
let selectedPackageData = null;

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el, { placement: 'top' }); });
});

function switchTab(tabName) {
    document.querySelectorAll('.custom-tab').forEach(tab => { tab.classList.remove('active'); if (tab.dataset.tab === tabName) tab.classList.add('active'); });
    document.querySelectorAll('.tab-panel').forEach(panel => panel.style.display = 'none');
    const targetPanel = document.getElementById('tab-' + tabName);
    if (targetPanel) targetPanel.style.display = 'block';
    if (tabName === 'invoices') loadInvoices(); else if (tabName === 'payments') loadPayments();
}

function selectPackage(element) {
    document.querySelectorAll('.package-option').forEach(card => card.classList.remove('selected'));
    element.classList.add('selected');
    selectedPackageData = { id: element.dataset.packageId, name: element.dataset.packageName, amount: parseInt(element.dataset.amount), dueDays: parseInt(element.dataset.dueDays) };
    const dueDate = new Date(); dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
    const formattedDate = dueDate.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
    document.getElementById('selectedPackageInfo').innerHTML = `<div class="p-3 rounded-3" style="background:rgba(43,154,130,0.06);border:1px solid rgba(43,154,130,0.15);"><div class="d-flex align-items-center gap-2"><i class="bi bi-check-circle-fill text-success"></i><div><small class="fw-bold">${selectedPackageData.name}</small><small class="text-muted d-block">PKR ${selectedPackageData.amount.toLocaleString()} | Due: ${formattedDate}</small></div></div></div>`;
    if (document.getElementById('invoiceFormContainer').style.display === 'block') updateInvoiceFormDisplay();
}

function showInvoiceForm() {
    if (!selectedPackageData) { Swal.fire({ title: 'Select Package', text: 'Please select a package from the left panel first.', icon: 'warning', confirmButtonColor: '#2b9a82', confirmButtonText: 'OK' }); return; }
    updateInvoiceFormDisplay(); document.getElementById('invoiceFormContainer').style.display = 'block';
    document.getElementById('invoiceFormContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
}
function hideInvoiceForm() { document.getElementById('invoiceFormContainer').style.display = 'none'; }
function updateInvoiceFormDisplay() {
    if (!selectedPackageData) return;
    const dueDate = new Date(); dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
    document.getElementById('displayPackageName').textContent = selectedPackageData.name;
    document.getElementById('displayAmount').textContent = 'PKR ' + selectedPackageData.amount.toLocaleString();
    document.getElementById('displayDueDate').textContent = dueDate.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
}

function createInvoice() {
    if (!selectedPackageData) { Swal.fire({ title: 'Select Package', text: 'Please select a package first.', icon: 'warning', confirmButtonColor: '#2b9a82' }); return; }
    Swal.fire({ title: 'Generate Invoice?', text: `Intern: ${internData.name}\nPackage: ${selectedPackageData.name}\nAmount: PKR ${selectedPackageData.amount.toLocaleString()}\nDue Date: ${document.getElementById('displayDueDate').textContent}`, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Generate', confirmButtonColor: '#2b9a82', cancelButtonColor: '#6c757d' }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('confirmCreateInvoiceBtn'), orig = btn.innerHTML;
            btn.disabled = true; btn.classList.add('btn-loading'); btn.innerHTML = '<span class="spinner-border spinner-border-xs me-2"></span>Generating...';
            fetch('{{ route("admin.invoices.create-from-package") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: JSON.stringify({ intern_email: internData.email, intern_name: internData.name, intern_phone: internData.phone, intern_technology: internData.technology, package_name: selectedPackageData.name, amount: selectedPackageData.amount, due_days: selectedPackageData.dueDays }) })
            .then(r => r.json()).then(data => {
                btn.disabled = false; btn.classList.remove('btn-loading'); btn.innerHTML = orig;
                if (data.success) { showToast('Invoice created!', 'success'); hideInvoiceForm(); document.querySelectorAll('.package-option').forEach(c => c.classList.remove('selected')); document.getElementById('selectedPackageInfo').innerHTML = ''; selectedPackageData = null; loadInvoices(); }
                else showToast(data.message || 'Failed', 'error');
            }).catch(e => { console.error(e); btn.disabled = false; btn.classList.remove('btn-loading'); btn.innerHTML = orig; showToast('Network error', 'error'); });
        }
    });
}

function loadInvoices() {
    const tbody = document.getElementById('invoicesTableBody'); if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5"><span class="spinner-border spinner-border-xs text-primary"></span><span class="ms-2 text-muted small">Loading...</span></td></tr>`;
    fetch(`/admin/interns/${internData.id}/invoices`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(data => {
        if (data.success && data.invoices && data.invoices.length > 0) {
            tbody.innerHTML = data.invoices.map(inv => {
                const remaining = parseFloat(inv.remaining_amount || (inv.total_amount - (inv.received_amount || 0))), received = parseFloat(inv.received_amount || 0), total = parseFloat(inv.total_amount || 0);
                let sc, st; if (remaining <= 0) { sc = 'badge-paid'; st = 'Paid'; } else if (inv.due_date && new Date(inv.due_date) < new Date()) { sc = 'badge-overdue'; st = 'Overdue'; } else if (received > 0) { sc = 'badge-partial'; st = 'Partial'; } else { sc = 'badge-pending'; st = 'Pending'; }
                const ds = inv.due_date ? new Date(inv.due_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A';
                return `<tr><td><span class="fw-semibold small">${inv.inv_id || 'INV-' + inv.id}</span></td><td><span class="small">PKR ${total.toLocaleString()}</span></td><td><span class="small text-success">PKR ${received.toLocaleString()}</span></td><td><span class="small ${remaining > 0 ? 'text-danger fw-bold' : 'text-success'}">PKR ${remaining.toLocaleString()}</span></td><td><span class="status-badge ${sc}">${st}</span></td><td><span class="small">${ds}</span></td><td><button class="btn-icon-action btn-edit me-1" onclick="editInvoice(${inv.id},${total},${received},'${inv.due_date||''}')" title="Edit"><i class="bi bi-pencil-fill small"></i></button><button class="btn-icon-action btn-delete" onclick="deleteInvoice(${inv.id},'${inv.inv_id||'INV-'+inv.id}')" title="Delete"><i class="bi bi-trash-fill small"></i></button></td></tr>`;
            }).join('');
        } else tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5"><i class="bi bi-receipt text-muted fs-3 d-block mb-2 opacity-50"></i><span class="text-muted small">No invoices found</span></td></tr>`;
    }).catch(() => { tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger small">Failed to load invoices.</td></tr>`; });
}

function loadPayments() {
    const tbody = document.getElementById('paymentsTableBody'); if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5"><span class="spinner-border spinner-border-xs text-primary"></span><span class="ms-2 text-muted small">Loading...</span></td></tr>`;
    const fd = document.getElementById('paymentFromDate')?.value || '', td = document.getElementById('paymentToDate')?.value || '', s = document.getElementById('paymentSearch')?.value || '';
    let url = `/admin/interns/${internData.id}/payments?` + new URLSearchParams({ from_date: fd, to_date: td, search: s }).toString();
    fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => r.json()).then(data => {
        if (data.success && data.payments && data.payments.length > 0) {
            const total = data.payments.reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
            const tm = data.payments.filter(p => new Date(p.date).getMonth() === new Date().getMonth()).reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
            const td2 = data.payments.filter(p => new Date(p.date).toDateString() === new Date().toDateString()).reduce((sum, p) => sum + parseFloat(p.amount || 0), 0);
            document.getElementById('statTotal').textContent = 'PKR ' + total.toLocaleString();
            document.getElementById('statMonth').textContent = 'PKR ' + tm.toLocaleString();
            document.getElementById('statToday').textContent = 'PKR ' + td2.toLocaleString();
            tbody.innerHTML = data.payments.map(p => `<tr><td><span class="small">${new Date(p.date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})}</span></td><td><span class="small fw-semibold">${p.inv_id||'N/A'}</span></td><td><span class="small fw-bold text-success">PKR ${parseFloat(p.amount||0).toLocaleString()}</span></td><td><span class="badge bg-label-info">${p.method||'N/A'}</span></td><td><span class="small">${p.received_by||'Admin'}</span></td></tr>`).join('');
        } else { document.getElementById('statTotal').textContent = 'PKR 0'; document.getElementById('statMonth').textContent = 'PKR 0'; document.getElementById('statToday').textContent = 'PKR 0'; tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5"><i class="bi bi-credit-card-2-front text-muted fs-3 d-block mb-2 opacity-50"></i><span class="text-muted small">No payment records found</span></td></tr>`; }
    }).catch(() => { tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-danger small">Failed to load payments</td></tr>`; });
}

function editInvoice(id, total, received, dueDate) { document.getElementById('edit_invoice_id').value = id; document.getElementById('edit_total_amount').value = total; document.getElementById('edit_received_amount').value = received; document.getElementById('edit_due_date').value = dueDate; new bootstrap.Modal(document.getElementById('editInvoiceModal')).show(); }
function updateInvoice(event) {
    event.preventDefault(); const id = document.getElementById('edit_invoice_id').value, btn = document.getElementById('updateInvoiceBtn'), orig = btn.innerHTML;
    btn.disabled = true; btn.classList.add('btn-loading'); btn.innerHTML = '<span class="spinner-border spinner-border-xs me-2"></span>Saving...';
    fetch(`/admin/invoices/${id}/update`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ total_amount: document.getElementById('edit_total_amount').value, received_amount: document.getElementById('edit_received_amount').value, due_date: document.getElementById('edit_due_date').value }) }).then(r => r.json()).then(data => {
        btn.disabled = false; btn.classList.remove('btn-loading'); btn.innerHTML = orig;
        if (data.success) { showToast('Invoice updated!', 'success'); bootstrap.Modal.getInstance(document.getElementById('editInvoiceModal')).hide(); loadInvoices(); }
        else showToast(data.message || 'Update failed', 'error');
    }).catch(() => { btn.disabled = false; btn.classList.remove('btn-loading'); btn.innerHTML = orig; showToast('Network error', 'error'); });
}
function deleteInvoice(id, invId) {
    Swal.fire({ title: 'Delete Invoice?', text: `Delete ${invId}? This cannot be undone.`, icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, delete', confirmButtonColor: '#ef4444', cancelButtonColor: '#6c757d' }).then(r => {
        if (r.isConfirmed) fetch(`/admin/invoices/${id}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(r => r.json()).then(d => { if (d.success) { showToast('Invoice deleted!', 'success'); loadInvoices(); } else showToast(d.message || 'Failed', 'error'); }).catch(() => showToast('Network error', 'error'));
    });
}
document.getElementById('removeInternBtn')?.addEventListener('click', function() {
    Swal.fire({ title: 'Remove Intern?', text: 'This will freeze their portal access.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, remove', confirmButtonColor: '#ef4444', cancelButtonColor: '#6c757d' }).then(r => {
        if (r.isConfirmed) fetch(`/admin/interns/${this.dataset.id}/remove-ajax`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(r => r.json()).then(d => { if (d.success) { showToast('Removed!', 'success'); setTimeout(() => { window.location.href = '{{ route("all-interns-admin") }}'; }, 1500); } else showToast(d.message || 'Failed', 'error'); }).catch(() => showToast('Network error', 'error'));
    });
});
function showToast(message, type = 'success') {
    document.querySelectorAll('.toast-custom').forEach(t => { t.style.animation = 'slideOutRight 0.3s ease forwards'; setTimeout(() => t.remove(), 300); });
    const toast = document.createElement('div'); toast.className = `toast-custom toast-${type}`; toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${message}`; document.body.appendChild(toast);
    setTimeout(() => { toast.style.animation = 'slideOutRight 0.3s ease forwards'; setTimeout(() => toast.remove(), 300); }, 3500);
}
document.addEventListener('DOMContentLoaded', function() { document.querySelectorAll('.custom-tab').forEach(tab => { tab.addEventListener('click', function() { switchTab(this.dataset.tab); }); }); });

let pendingEditData = null;
document.getElementById('editInternForm')?.addEventListener('submit', function(e) {
    e.preventDefault(); const st = document.getElementById('edit_status').value, id = document.getElementById('edit_id').value, nm = document.getElementById('edit_name').value, em = document.getElementById('edit_email').value, tech = document.getElementById('edit_technology').value;
    if (st === 'Reject' || st === 'Terminate') { pendingEditData = { id, name: nm, email: em, technology: tech, status: st }; const rm = new bootstrap.Modal(document.getElementById('reasonModal')); document.getElementById('reasonModalTitle').innerHTML = `<i class="ti ti-alert-triangle me-2 text-warning"></i>${st} Reason Required`; document.getElementById('reason_intern_id').value = id; document.getElementById('reason_new_status').value = st; document.getElementById('reason_intern_name').value = nm; document.getElementById('reason_intern_email').value = em; document.getElementById('reason_intern_technology').value = tech; document.getElementById('reason_text').value = ''; rm.show(); return false; }
    submitEditForm(id, nm, em, tech, st, null);
});
function submitEditForm(id, name, email, technology, status, reason) {
    const fd = new FormData(); fd.append('id', id); fd.append('name', name); fd.append('email', email); fd.append('phone', document.getElementById('edit_phone')?.value || ''); fd.append('cnic', document.getElementById('edit_cnic')?.value || ''); fd.append('gender', document.getElementById('edit_gender')?.value || ''); fd.append('birth_date', document.getElementById('edit_dob')?.value || ''); fd.append('country', document.getElementById('edit_country')?.value || ''); fd.append('city', document.getElementById('edit_city')?.value || ''); fd.append('university', document.getElementById('edit_university')?.value || ''); fd.append('technology', technology); fd.append('duration', document.getElementById('edit_duration')?.value || ''); fd.append('intern_type', document.getElementById('edit_intern_type')?.value || ''); fd.append('status', status); fd.append('bio', document.getElementById('edit_bio')?.value || ''); if (reason) fd.append('status_reason', reason);
    const sf = document.getElementById('reason_screenshot')?.files[0]; if (sf) fd.append('screenshot', sf);
    Swal.fire({ title: 'Update Status?', text: `Change to ${status}?${reason ? '\nReason: ' + reason : ''}`, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Update', confirmButtonColor: '#2b9a82' }).then(r => {
        if (r.isConfirmed) { Swal.fire({ title: 'Updating...', text: 'Please wait', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            fetch('{{ route("update.intern.admin") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd }).then(r => r.json()).then(d => {
                if (d.success) { Swal.fire({ icon: 'success', title: 'Updated!', text: `Status: ${status}`, confirmButtonColor: '#2b9a82' }).then(() => { bootstrap.Modal.getInstance(document.getElementById('editInternModal')).hide(); setTimeout(() => location.reload(), 1000); }); }
                else Swal.fire({ icon: 'error', title: 'Failed!', text: d.message || 'Error', confirmButtonColor: '#2b9a82' });
            }).catch(() => { Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong.', confirmButtonColor: '#2b9a82' }); });
        }
    });
}
document.getElementById('reasonForm')?.addEventListener('submit', function(e) { e.preventDefault(); const r = document.getElementById('reason_text').value.trim(); if (!r) { Swal.fire({ title: 'Reason Required', text: 'Please provide a reason', icon: 'warning', confirmButtonColor: '#2b9a82' }); return; } if (pendingEditData) { submitEditForm(pendingEditData.id, pendingEditData.name, pendingEditData.email, pendingEditData.technology, pendingEditData.status, r); bootstrap.Modal.getInstance(document.getElementById('reasonModal')).hide(); pendingEditData = null; } });
document.getElementById('editInternBtn')?.addEventListener('click', function() { document.getElementById('edit_id').value = this.dataset.id; document.getElementById('edit_name').value = this.dataset.name; document.getElementById('edit_email').value = this.dataset.email; document.getElementById('edit_phone').value = this.dataset.phone || ''; document.getElementById('edit_cnic').value = this.dataset.cnic || ''; document.getElementById('edit_gender').value = this.dataset.gender || ''; document.getElementById('edit_dob').value = this.dataset.dob || ''; document.getElementById('edit_country').value = this.dataset.country || ''; document.getElementById('edit_city').value = this.dataset.city || ''; document.getElementById('edit_university').value = this.dataset.university || ''; document.getElementById('edit_technology').value = this.dataset.technology || ''; document.getElementById('edit_duration').value = this.dataset.duration || ''; document.getElementById('edit_intern_type').value = this.dataset.internType || ''; document.getElementById('edit_status').value = this.dataset.status || ''; document.getElementById('edit_bio').value = this.dataset.bio || ''; });
document.addEventListener('hidden.bs.modal', function() { document.querySelectorAll('.modal-backdrop').forEach(el => el.remove()); document.body.classList.remove('modal-open'); document.body.style.overflow = ''; document.body.style.paddingRight = ''; });
</script>
@endsection