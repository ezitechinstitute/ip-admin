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
        --blue-gradient: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        --amber-gradient: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);
        --rose-gradient: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        --purple-gradient: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 50%, #f8fafb 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .premium-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: var(--card-radius);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
    }

    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Profile Banner */
    .profile-banner {
        position: relative;
        height: 220px;
        background: linear-gradient(160deg, #0f1729 0%, #1a2744 30%, #2b9a82 70%, #3b82f6 100%);
        overflow: hidden;
    }

    .profile-banner::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -15%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    .profile-banner::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .banner-particle {
        position: absolute;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: floatUp 8s infinite ease-in;
    }

    @keyframes floatUp {
        0% { transform: translateY(100%) scale(0); opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { transform: translateY(-100%) scale(1.5); opacity: 0; }
    }

    /* Avatar Section */
    .avatar-section {
        margin-top: -65px;
        position: relative;
        z-index: 10;
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .avatar-image {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        transition: var(--transition-smooth);
        background: #f1f5f9;
    }

    .avatar-initials {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        color: white;
        font-size: 52px;
        font-weight: 700;
        border: 5px solid white;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        letter-spacing: 2px;
    }

    .avatar-wrapper:hover .avatar-image,
    .avatar-wrapper:hover .avatar-initials {
        transform: scale(1.05);
    }

    .status-dot {
        position: absolute;
        bottom: 12px;
        right: 8px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .status-active { background: #10b981; animation: pulse 2s infinite; }
    .status-interview { background: #3b82f6; }
    .status-contact { background: #8b5cf6; }
    .status-test { background: #f59e0b; }
    .status-completed { background: #6b7280; }
    .status-removed { background: #ef4444; }

    @keyframes pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    }

    /* Stat Mini Cards */
    .stat-mini-card {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 1rem;
        padding: 1rem 1.25rem;
        border: 1px solid rgba(0,0,0,0.04);
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .stat-mini-card:hover {
        background: rgba(255, 255, 255, 0.95);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    /* Contact Items */
    .contact-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 0.75rem;
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .contact-item:hover {
        background: rgba(43, 154, 130, 0.04);
        transform: translateX(6px);
    }

    .contact-icon-circle {
        width: 42px;
        height: 42px;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }

    /* Package Cards */
    .package-card {
        background: white;
        border: 2px solid rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        padding: 1.25rem;
        cursor: pointer;
        transition: var(--transition-smooth);
        position: relative;
        overflow: hidden;
    }

    .package-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .package-card:hover {
        border-color: rgba(43, 154, 130, 0.3);
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }

    .package-card:hover::before {
        transform: scaleX(1);
    }

    .package-card.selected {
        border-color: #2b9a82;
        background: rgba(43, 154, 130, 0.03);
        box-shadow: 0 4px 20px rgba(43, 154, 130, 0.1);
    }

    .package-card.selected::before {
        transform: scaleX(1);
    }

    .package-radio {
        width: 24px;
        height: 24px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-smooth);
        flex-shrink: 0;
    }

    .package-card.selected .package-radio {
        border-color: #2b9a82;
        background: #2b9a82;
    }

    .package-card.selected .package-radio::after {
        content: '✓';
        color: white;
        font-size: 13px;
        font-weight: bold;
    }

    .package-price {
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* Tabs */
    .custom-tabs {
        display: flex;
        gap: 0.25rem;
        padding: 0.5rem;
        background: rgba(0,0,0,0.03);
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .custom-tab {
        flex: 1;
        padding: 0.65rem 1rem;
        border: none;
        background: transparent;
        font-weight: 600;
        font-size: 0.8rem;
        color: #6c86a3;
        cursor: pointer;
        transition: var(--transition-smooth);
        border-radius: 0.6rem;
        white-space: nowrap;
        position: relative;
    }

    .custom-tab.active {
        background: white;
        color: #2b9a82;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .custom-tab:hover:not(.active) {
        color: #2b9a82;
        background: rgba(255,255,255,0.5);
    }

    /* Detail Grid */
    .detail-item {
        padding: 0.9rem 1rem;
        border-radius: 0.75rem;
        background: rgba(0,0,0,0.015);
        transition: var(--transition-smooth);
    }

    .detail-item:hover {
        background: rgba(43,154,130,0.03);
    }

    .detail-label {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 0.3rem;
    }

    .detail-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
    }

    /* Invoice Table */
    .invoice-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .invoice-table thead th {
        background: rgba(0,0,0,0.02);
        padding: 0.85rem 1rem;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        border-bottom: 2px solid rgba(0,0,0,0.05);
        white-space: nowrap;
    }

    .invoice-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }

    .invoice-table tbody tr {
        transition: var(--transition-smooth);
    }

    .invoice-table tbody tr:hover {
        background: rgba(43,154,130,0.02);
    }

    /* Status Badges */
    .status-badge {
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        white-space: nowrap;
    }

    .badge-paid { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .badge-pending { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
    .badge-overdue { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .badge-partial { background: rgba(139,92,246,0.1); color: #7c3aed; border: 1px solid rgba(139,92,246,0.2); }

    /* Action Buttons */
    .btn-icon-action {
        width: 34px;
        height: 34px;
        border-radius: 0.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-smooth);
        border: none;
        cursor: pointer;
    }

    .btn-edit { background: rgba(59,130,246,0.08); color: #3b82f6; }
    .btn-edit:hover { background: #3b82f6; color: white; transform: scale(1.1); }

    .btn-delete { background: rgba(239,68,68,0.08); color: #ef4444; }
    .btn-delete:hover { background: #ef4444; color: white; transform: scale(1.1); }

    .btn-primary-premium {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: var(--transition-smooth);
        box-shadow: 0 4px 15px rgba(43,154,130,0.3);
        cursor: pointer;
    }

    .btn-primary-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(43,154,130,0.4);
        color: white;
    }

    .btn-outline-danger-premium {
        background: transparent;
        color: #ef4444;
        border: 1.5px solid rgba(239,68,68,0.3);
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        transition: var(--transition-smooth);
        cursor: pointer;
    }

    .btn-outline-danger-premium:hover {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    .invoice-form-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(43,154,130,0.04));
        border-radius: 1rem;
        padding: 1.5rem;
        border: 1px solid rgba(43,154,130,0.15);
    }

    .amount-display {
        font-size: 1.6rem;
        font-weight: 800;
        color: #2b9a82;
        line-height: 1;
    }

    /* Toast */
    .toast-custom {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 99999;
        padding: 0.9rem 1.5rem;
        border-radius: 0.75rem;
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .toast-success { background: rgba(16,185,129,0.95); }
    .toast-error { background: rgba(239,68,68,0.95); }

    @keyframes slideInRight {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(120%); opacity: 0; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-in {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    /* Loading state */
    .btn-loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .spinner-border-xs { 
        width: 0.9rem; 
        height: 0.9rem; 
        border-width: 0.12em; 
        display: inline-block;
    }

    @media (max-width: 992px) {
        .profile-banner { height: 180px; }
        .avatar-section { margin-top: -50px; }
        .avatar-image, .avatar-initials { width: 110px; height: 110px; }
        .avatar-initials { font-size: 40px; }
    }

    @media (max-width: 768px) {
        .profile-banner { height: 150px; }
        .avatar-section { margin-top: -40px; flex-direction: column; align-items: center; text-align: center; }
        .avatar-image, .avatar-initials { width: 90px; height: 90px; }
        .avatar-initials { font-size: 32px; }
        .custom-tabs { flex-wrap: nowrap; overflow-x: auto; }
        .custom-tab { flex: none; }
    }


    /* ============ INVOICE FORM PREMIUM ============ */
.invoice-form-premium {
    background: white;
    border-radius: 1.25rem;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 8px 32px rgba(0,0,0,0.08);
    animation: slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-premium-header {
    background: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%);
    padding: 1.25rem 1.5rem;
}

.form-premium-header small { color: rgba(255,255,255,0.7) !important; }

.form-header-icon {
    width: 44px; height: 44px;
    border-radius: 0.75rem;
    background: rgba(255,255,255,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    backdrop-filter: blur(4px);
}

.btn-close-premium {
    width: 34px; height: 34px;
    border-radius: 50%; border: none;
    background: rgba(255,255,255,0.2);
    color: white; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease;
}

.btn-close-premium:hover {
    background: rgba(255,255,255,0.35);
    transform: rotate(90deg);
}

.form-premium-body { padding: 1.5rem; }

.form-premium-footer {
    padding: 1rem 1.5rem;
    background: rgba(0,0,0,0.02);
    border-top: 1px solid rgba(0,0,0,0.05);
    display: flex; justify-content: flex-end; gap: 0.75rem;
}

.info-cards-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.info-card-mini {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(0,0,0,0.015);
    border-radius: 0.75rem;
    border: 1px solid rgba(0,0,0,0.04);
    transition: all 0.3s ease;
}

.info-card-mini:hover {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    transform: translateY(-1px);
}

.info-card-icon {
    width: 38px; height: 38px;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; flex-shrink: 0;
}

.form-label-premium {
    font-size: 0.7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: #6c86a3; margin-bottom: 0.5rem;
    display: flex; align-items: center; gap: 0.4rem;
}

.highlight-box {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem; border-radius: 0.75rem;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.highlight-box-primary {
    background: rgba(43,154,130,0.04);
    border-color: rgba(43,154,130,0.2);
}

.highlight-box-warning {
    background: rgba(245,158,11,0.04);
    border-color: rgba(245,158,11,0.2);
}

.highlight-box-info {
    background: rgba(59,130,246,0.04);
    border-color: rgba(59,130,246,0.2);
}

.highlight-box-icon {
    width: 40px; height: 40px;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}

.highlight-box-primary .highlight-box-icon {
    background: rgba(43,154,130,0.1); color: #2b9a82;
}

.highlight-box-warning .highlight-box-icon {
    background: rgba(245,158,11,0.1); color: #f59e0b;
}

.highlight-box-info .highlight-box-icon {
    background: rgba(59,130,246,0.1); color: #3b82f6;
}

.btn-generate-premium {
    background: linear-gradient(135deg, #2b9a82 0%, #1e7b68 100%);
    color: white; border: none;
    padding: 0.7rem 1.75rem; border-radius: 50px;
    font-weight: 600; font-size: 0.82rem;
    cursor: pointer; transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(43,154,130,0.3);
    display: inline-flex; align-items: center; gap: 0.5rem;
}

.btn-generate-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(43,154,130,0.4);
}

.btn-generate-premium:disabled {
    opacity: 0.6; cursor: not-allowed; transform: none;
}

.btn-cancel-premium {
    background: transparent; color: #6c86a3;
    border: 1.5px solid rgba(0,0,0,0.1);
    padding: 0.7rem 1.75rem; border-radius: 50px;
    font-weight: 600; font-size: 0.82rem;
    cursor: pointer; transition: all 0.3s ease;
    display: inline-flex; align-items: center; gap: 0.5rem;
}

.btn-cancel-premium:hover {
    border-color: #ef4444; color: #ef4444;
    background: rgba(239,68,68,0.03);
}

@media (max-width: 768px) {
    .info-cards-row { grid-template-columns: 1fr 1fr; }
    .form-premium-footer { flex-direction: column; }
    .btn-generate-premium, .btn-cancel-premium {
        width: 100%; justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- ==================== PROFILE HEADER ==================== --}}
    <div class="premium-card mb-4 animate-in" style="padding: 0 !important; animation-delay: 0.05s;">
        
        <div class="profile-banner">
            @for($i = 1; $i <= 6; $i++)
            <div class="banner-particle" style="
                width: {{ rand(6, 12) }}px; 
                height: {{ rand(6, 12) }}px; 
                left: {{ rand(10, 85) }}%; 
                animation-delay: {{ $i * 0.5 }}s; 
                animation-duration: {{ rand(5, 8) }}s;
            "></div>
            @endfor
        </div>

        <div class="px-4 pb-4">
            <div class="avatar-section">
                <div class="avatar-wrapper">
                    @php
                        $profileImage = $interneeDetails->image ?? null;
                        $userName = $interneeDetails->name ?? 'User';
                        $initials = strtoupper(substr($userName, 0, 2));
                        
                        // Check for real image
                        $hasRealImage = false;
                        if ($profileImage && !str_starts_with($profileImage, 'data:image')) {
                            if (filter_var($profileImage, FILTER_VALIDATE_URL)) {
                                $hasRealImage = true;
                                $imageUrl = $profileImage;
                            } elseif (file_exists(public_path($profileImage))) {
                                $hasRealImage = true;
                                $imageUrl = asset($profileImage);
                            }
                        }
                    @endphp

                    @if($hasRealImage)
                        <img src="{{ $imageUrl }}?v={{ time() }}" 
                             alt="{{ $userName }}" 
                             class="avatar-image"
                             id="profileAvatarImage"
                             onerror="handleImageError()">
                        <div class="avatar-initials" id="profileAvatarInitials" style="display: none;">{{ $initials }}</div>
                    @else
                        <img src="" alt="" class="avatar-image" id="profileAvatarImage" style="display: none;">
                        <div class="avatar-initials" id="profileAvatarInitials">{{ $initials }}</div>
                    @endif

                    @php
                        $statusClass = match(strtolower($interneeDetails->status ?? '')) {
                            'active' => 'status-active',
                            'interview' => 'status-interview',
                            'contact' => 'status-contact',
                            'test' => 'status-test',
                            'completed' => 'status-completed',
                            'removed' => 'status-removed',
                            default => 'status-interview'
                        };
                    @endphp
                    <span class="status-dot {{ $statusClass }}" 
                          title="{{ ucfirst($interneeDetails->status ?? 'Unknown') }}"
                          data-bs-toggle="tooltip"></span>
                </div>

                <div class="flex-grow-1 pb-2">
                    <h3 class="fw-bold mb-1" style="color: #1e293b;">{{ $userName }}</h3>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-code-slash text-primary me-1"></i>
                            {{ $interneeDetails->technology ?? 'N/A' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-geo-alt text-success me-1"></i>
                            {{ $interneeDetails->city ?? 'N/A' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-calendar3 text-info me-1"></i>
                            Joined {{ $interneeDetails->join_date ?? 'N/A' }}
                        </span>
                        @php
                            $statusBadgeClass = match(strtolower($interneeDetails->status ?? '')) {
                                'active' => 'bg-success',
                                'removed' => 'bg-danger',
                                'completed' => 'bg-secondary',
                                default => 'bg-warning'
                            };
                        @endphp
                        <span class="badge {{ $statusBadgeClass }} bg-opacity-10 text-{{ str_replace('bg-', '', $statusBadgeClass) }} rounded-pill px-3 py-1">
                            {{ ucfirst($interneeDetails->status ?? 'Unknown') }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2 pb-2 flex-shrink-0">
                    <button class="btn-primary-premium" id="editInternBtn"
                            data-bs-toggle="modal" data-bs-target="#editInternModal"
                            data-id="{{ $interneeDetails->id }}"
                            data-name="{{ $interneeDetails->name }}"
                            data-email="{{ $interneeDetails->email }}"
                            data-technology="{{ $interneeDetails->technology }}"
                            data-status="{{ $interneeDetails->status }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                    <button class="btn-outline-danger-premium" id="removeInternBtn" data-id="{{ $interneeDetails->id }}">
                        <i class="bi bi-trash me-1"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== QUICK STATS ROW ==================== --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.1s;">
            <div class="stat-mini-card text-center">
                <div class="fw-bold text-muted small mb-1">Intern ID</div>
                <div class="fw-bold fs-5 text-primary">#{{ $interneeDetails->id ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.15s;">
            <div class="stat-mini-card text-center">
                <div class="fw-bold text-muted small mb-1">Email</div>
                <div class="fw-bold small text-truncate">{{ $interneeDetails->email ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.2s;">
            <div class="stat-mini-card text-center">
                <div class="fw-bold text-muted small mb-1">Phone</div>
                <div class="fw-bold small">{{ $interneeDetails->phone ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3 animate-in" style="animation-delay: 0.25s;">
            <div class="stat-mini-card text-center">
                <div class="fw-bold text-muted small mb-1">University</div>
                <div class="fw-bold small text-truncate">{{ $interneeDetails->university ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <div class="row g-4">
        
        {{-- LEFT COLUMN --}}
        <div class="col-xl-4 col-lg-5">
            
            {{-- Contact Information --}}
            <div class="premium-card p-4 mb-4 animate-in" style="animation-delay: 0.3s;">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-lines-fill me-2" style="color: #2b9a82;"></i>Contact Information
                </h5>
                <div class="d-flex flex-column gap-1">
                    <div class="contact-item">
                        <div class="contact-icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <small class="text-muted d-block" style="font-size: 0.68rem;">Email Address</small>
                            <span class="fw-medium small text-truncate d-block">{{ $interneeDetails->email ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-circle bg-success bg-opacity-10 text-success">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.68rem;">Phone Number</small>
                            <span class="fw-medium small">{{ $interneeDetails->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-circle bg-info bg-opacity-10 text-info">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.68rem;">Location</small>
                            <span class="fw-medium small">{{ $interneeDetails->city ?? 'N/A' }}, {{ $interneeDetails->country ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-circle bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <small class="text-muted d-block" style="font-size: 0.68rem;">University</small>
                            <span class="fw-medium small text-truncate d-block">{{ $interneeDetails->university ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-circle" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.68rem;">CNIC</small>
                            <span class="fw-medium small">{{ $interneeDetails->cnic ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Package Selection --}}
            <div class="premium-card p-4 animate-in" style="animation-delay: 0.35s;">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-gift-fill me-2" style="color: #2b9a82;"></i>Select Package
                </h5>
                <div class="d-flex flex-column gap-2" id="packageList">
                    @php
                        $packages = [
                            ['id' => 'basic', 'name' => 'Basic', 'amount' => 5000, 'duration' => '1 Month', 'due_days' => 30, 'color' => '#3b82f6'],
                            ['id' => 'standard', 'name' => 'Standard', 'amount' => 12000, 'duration' => '3 Months', 'due_days' => 60, 'color' => '#8b5cf6'],
                            ['id' => 'premium', 'name' => 'Premium', 'amount' => 20000, 'duration' => '6 Months', 'due_days' => 90, 'color' => '#2b9a82'],
                        ];
                    @endphp

                    @foreach($packages as $pkg)
                    <div class="package-card package-option" 
                         data-package-id="{{ $pkg['id'] }}"
                         data-package-name="{{ $pkg['name'] }} Package"
                         data-amount="{{ $pkg['amount'] }}"
                         data-due-days="{{ $pkg['due_days'] }}"
                         onclick="selectPackage(this)">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="package-radio"></div>
                                <div>
                                    <div class="fw-bold small">{{ $pkg['name'] }} Package</div>
                                    <small class="text-muted">{{ $pkg['duration'] }}</small>
                                </div>
                            </div>
                            <div class="package-price" style="color: {{ $pkg['color'] }};">
                                PKR {{ number_format($pkg['amount']) }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div id="selectedPackageInfo" class="mt-3"></div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-xl-8 col-lg-7">
            <div class="premium-card p-4 animate-in" style="animation-delay: 0.4s;">
                
                {{-- Custom Tabs --}}
                <div class="custom-tabs" id="tabNavigation">
                    <button class="custom-tab active" data-tab="details" onclick="switchTab('details')">
                        <i class="bi bi-info-circle me-1"></i> Details
                    </button>
                    <button class="custom-tab" data-tab="invoices" onclick="switchTab('invoices')">
                        <i class="bi bi-receipt me-1"></i> Invoices
                    </button>
                    <button class="custom-tab" data-tab="payments" onclick="switchTab('payments')">
                        <i class="bi bi-credit-card me-1"></i> Payments
                    </button>
                </div>

                {{-- Details Tab --}}
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

                {{-- Invoices Tab --}}
                <div class="tab-panel" id="tab-invoices" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-file-earmark-plus me-2" style="color: #2b9a82;"></i>Invoice Management
                        </h6>
                        <button class="btn-primary-premium btn-sm" id="showInvoiceFormBtn" onclick="showInvoiceForm()">
                            <i class="bi bi-plus-lg me-1"></i> Create Invoice
                        </button>
                    </div>

                   {{-- Invoice Form --}}
<div id="invoiceFormContainer" style="display: none;" class="invoice-form-premium mb-4">
    {{-- Form Header --}}
    <div class="form-premium-header">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="form-header-icon">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-white">Create New Invoice</h6>
                    <small class="text-white-50">Auto-filled from selected package</small>
                </div>
            </div>
            <button type="button" class="btn-close-premium" onclick="hideInvoiceForm()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- Form Body --}}
    <div class="form-premium-body">
        {{-- Intern Info Cards --}}
        <div class="info-cards-row">
            <div class="info-card-mini">
                <div class="info-card-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Intern</small>
                    <span class="fw-bold small">{{ $interneeDetails->name }}</span>
                </div>
            </div>
            <div class="info-card-mini">
                <div class="info-card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-envelope-fill"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Email</small>
                    <span class="fw-medium small text-truncate" style="max-width: 160px;">{{ $interneeDetails->email }}</span>
                </div>
            </div>
            <div class="info-card-mini">
                <div class="info-card-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Contact</small>
                    <span class="fw-medium small">{{ $interneeDetails->phone ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="info-card-mini">
                <div class="info-card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-code-slash"></i>
                </div>
                <div>
                    <small class="text-muted d-block">Technology</small>
                    <span class="fw-medium small">{{ $interneeDetails->technology ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Package & Amount --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label-premium">
                    <i class="bi bi-gift-fill"></i> Selected Package
                </label>
                <div class="highlight-box highlight-box-primary">
                    <div class="highlight-box-icon">
                        <i class="bi bi-box-seam-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold" id="displayPackageName" style="font-size: 0.9rem;">-</div>
                        <small class="text-muted">Package</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label-premium">
                    <i class="bi bi-cash-stack"></i> Total Amount
                </label>
                <div class="highlight-box highlight-box-warning">
                    <div class="highlight-box-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="displayAmount" style="color: #f59e0b;">PKR 0</div>
                        <small class="text-muted">Amount (PKR)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label-premium">
                    <i class="bi bi-calendar-event"></i> Due Date
                </label>
                <div class="highlight-box highlight-box-info">
                    <div class="highlight-box-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold" id="displayDueDate" style="font-size: 0.85rem;">-</div>
                        <small class="text-muted">Due Date</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Footer --}}
    <div class="form-premium-footer">
        <button type="button" class="btn-cancel-premium" onclick="hideInvoiceForm()">
            <i class="bi bi-x-circle me-1"></i> Cancel
        </button>
        <button type="button" class="btn-generate-premium" id="confirmCreateInvoiceBtn" onclick="createInvoice()">
            <i class="bi bi-check-circle me-1"></i> Generate Invoice
        </button>
    </div>
</div>

                    {{-- Invoices Table --}}
                    <div class="table-responsive">
                        <table class="invoice-table" id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Total</th>
                                    <th>Received</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="invoicesTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <span class="spinner-border spinner-border-xs text-primary" role="status"></span>
                                        <span class="ms-2 text-muted small">Loading invoices...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Payments Tab --}}
                <div class="tab-panel" id="tab-payments" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-wallet2 me-2" style="color: #2b9a82;"></i>Payment History
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice #</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Received By</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <span class="spinner-border spinner-border-xs text-primary" role="status"></span>
                                        <span class="ms-2 text-muted small">Loading payments...</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT INTERN MODAL ==================== --}}
<div class="modal fade" id="editInternModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-card border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2" style="color: #2b9a82;"></i>Edit Intern
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('update.intern.admin') }}" method="POST" id="editInternForm">
                    @csrf
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Full Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <input type="email" id="edit_email" name="email" class="form-control rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Technology</label>
                            <input type="text" id="edit_technology" name="technology" class="form-control rounded-pill">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Status</label>
                            <select id="edit_status" name="status" class="form-select rounded-pill">
                                <option value="Interview">Interview</option>
                                <option value="Contact">Contact</option>
                                <option value="Test">Test</option>
                                <option value="Completed">Completed</option>
                                <option value="Active">Active</option>
                                <option value="Removed">Removed</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-premium">
                            <i class="bi bi-check-lg me-1"></i> Update Intern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ==================== EDIT INVOICE MODAL ==================== --}}
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content premium-card border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2" style="color: #2b9a82;"></i>Edit Invoice
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editInvoiceForm" onsubmit="updateInvoice(event)">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_invoice_id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Total Amount (PKR)</label>
                        <input type="number" id="edit_total_amount" name="total_amount" class="form-control rounded-pill" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Received Amount (PKR)</label>
                        <input type="number" id="edit_received_amount" name="received_amount" class="form-control rounded-pill" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Due Date</label>
                        <input type="date" id="edit_due_date" name="due_date" class="form-control rounded-pill">
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-premium" id="updateInvoiceBtn">
                            <i class="bi bi-check-lg me-1"></i> Update Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ============ GLOBAL STATE ============
let selectedPackageData = null;

// ============ IMAGE ERROR HANDLER ============
function handleImageError() {
    const img = document.getElementById('profileAvatarImage');
    const initials = document.getElementById('profileAvatarInitials');
    if (img && initials) {
        img.style.display = 'none';
        initials.style.display = 'flex';
    }
}

// ============ TOOLTIPS ============
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) {
        return new bootstrap.Tooltip(el, { placement: 'top' });
    });
});

// ============ TAB SWITCHING ============
function switchTab(tabName) {
    // Update active tab button
    document.querySelectorAll('.custom-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.tab === tabName) {
            tab.classList.add('active');
        }
    });
    
    // Show corresponding panel
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.style.display = 'none';
    });
    
    const targetPanel = document.getElementById('tab-' + tabName);
    if (targetPanel) {
        targetPanel.style.display = 'block';
    }
    
    // Load data when switching to invoices or payments tab
    if (tabName === 'invoices') {
        loadInvoices();
    } else if (tabName === 'payments') {
        loadPayments();
    }
}

// ============ PACKAGE SELECTION ============
function selectPackage(element) {
    // Remove selected class from all packages
    document.querySelectorAll('.package-option').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked package
    element.classList.add('selected');
    
    // Store selected package data
    selectedPackageData = {
        id: element.dataset.packageId,
        name: element.dataset.packageName,
        amount: parseInt(element.dataset.amount),
        dueDays: parseInt(element.dataset.dueDays)
    };
    
    // Calculate due date
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
    const formattedDate = dueDate.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
    
    // Show selected package info
    document.getElementById('selectedPackageInfo').innerHTML = `
        <div class="p-3 rounded-3" style="background: rgba(43,154,130,0.06); border: 1px solid rgba(43,154,130,0.15);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-success"></i>
                <div>
                    <small class="fw-bold">${selectedPackageData.name}</small>
                    <small class="text-muted d-block">
                        PKR ${selectedPackageData.amount.toLocaleString()} | Due: ${formattedDate}
                    </small>
                </div>
            </div>
        </div>`;
    
    // Update invoice form if visible
    if (document.getElementById('invoiceFormContainer').style.display === 'block') {
        updateInvoiceFormDisplay();
    }
    
    console.log('Package selected:', selectedPackageData);
}

// ============ INVOICE FORM ============
function showInvoiceForm() {
    if (!selectedPackageData) {
        Swal.fire({
            title: 'Select Package',
            text: 'Please select a package from the left panel first.',
            icon: 'warning',
            confirmButtonColor: '#2b9a82',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    updateInvoiceFormDisplay();
    document.getElementById('invoiceFormContainer').style.display = 'block';
    document.getElementById('invoiceFormContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideInvoiceForm() {
    document.getElementById('invoiceFormContainer').style.display = 'none';
}

function updateInvoiceFormDisplay() {
    if (!selectedPackageData) return;
    
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + selectedPackageData.dueDays);
    const formattedDate = dueDate.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
    
    document.getElementById('displayPackageName').textContent = selectedPackageData.name;
    document.getElementById('displayAmount').textContent = 'PKR ' + selectedPackageData.amount.toLocaleString();
    document.getElementById('displayDueDate').textContent = formattedDate;
}

// ============ CREATE INVOICE ============
function createInvoice() {
    if (!selectedPackageData) {
        Swal.fire({
            title: 'Select Package',
            text: 'Please select a package first.',
            icon: 'warning',
            confirmButtonColor: '#2b9a82'
        });
        return;
    }
    
    Swal.fire({
        title: 'Generate Invoice?',
        html: `
            <div style="text-align: left; font-size: 0.9rem;">
                <p class="mb-2"><strong>Intern:</strong> {{ $interneeDetails->name }}</p>
                <p class="mb-2"><strong>Package:</strong> ${selectedPackageData.name}</p>
                <p class="mb-2"><strong>Amount:</strong> PKR ${selectedPackageData.amount.toLocaleString()}</p>
                <p class="mb-0"><strong>Due Date:</strong> ${document.getElementById('displayDueDate').textContent}</p>
            </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Generate',
        confirmButtonColor: '#2b9a82',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('confirmCreateInvoiceBtn');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('btn-loading');
            btn.innerHTML = '<span class="spinner-border spinner-border-xs me-2"></span>Generating...';
            
            const requestData = {
                intern_email: '{{ $interneeDetails->email }}',
                intern_name: '{{ $interneeDetails->name }}',
                intern_phone: '{{ $interneeDetails->phone ?? "" }}',
                intern_technology: '{{ $interneeDetails->technology ?? "" }}',
                package_name: selectedPackageData.name,
                amount: selectedPackageData.amount,
                due_days: selectedPackageData.dueDays
            };
            
            console.log('Creating invoice with data:', requestData);
            
            fetch('{{ route("admin.invoices.create-from-package") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.innerHTML = originalHTML;
                
                if (data.success) {
                    showToast('Invoice created successfully! 🎉', 'success');
                    hideInvoiceForm();
                    // Reset package selection
                    document.querySelectorAll('.package-option').forEach(c => c.classList.remove('selected'));
                    document.getElementById('selectedPackageInfo').innerHTML = '';
                    selectedPackageData = null;
                    loadInvoices();
                } else {
                    showToast(data.message || 'Failed to create invoice', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                btn.innerHTML = originalHTML;
                showToast('Network error. Please check console and try again.', 'error');
            });
        }
    });
}

// ============ LOAD INVOICES ============
function loadInvoices() {
    const tbody = document.getElementById('invoicesTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5">
        <span class="spinner-border spinner-border-xs text-primary" role="status"></span>
        <span class="ms-2 text-muted small">Loading invoices...</span>
    </td></tr>`;
    
    const url = '/admin/interns/{{ $interneeDetails->id }}/invoices';
    console.log('Loading invoices from:', url);
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Invoices data:', data);
        
        if (data.success && data.invoices && data.invoices.length > 0) {
            tbody.innerHTML = data.invoices.map(inv => {
                const remaining = parseFloat(inv.remaining_amount || (inv.total_amount - (inv.received_amount || 0)));
                const received = parseFloat(inv.received_amount || 0);
                const total = parseFloat(inv.total_amount || 0);
                
                let statusClass, statusText;
                if (remaining <= 0) {
                    statusClass = 'badge-paid';
                    statusText = 'Paid';
                } else if (inv.due_date && new Date(inv.due_date) < new Date()) {
                    statusClass = 'badge-overdue';
                    statusText = 'Overdue';
                } else if (received > 0) {
                    statusClass = 'badge-partial';
                    statusText = 'Partial';
                } else {
                    statusClass = 'badge-pending';
                    statusText = 'Pending';
                }
                
                const dueDateStr = inv.due_date 
                    ? new Date(inv.due_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})
                    : 'N/A';
                
                return `
                <tr>
                    <td><span class="fw-semibold small">${inv.inv_id || 'INV-' + inv.id}</span></td>
                    <td><span class="small">PKR ${total.toLocaleString()}</span></td>
                    <td><span class="small text-success">PKR ${received.toLocaleString()}</span></td>
                    <td><span class="small ${remaining > 0 ? 'text-danger fw-bold' : 'text-success'}">PKR ${remaining.toLocaleString()}</span></td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td><span class="small">${dueDateStr}</span></td>
                    <td>
                        <button class="btn-icon-action btn-edit me-1" 
                                onclick="editInvoice(${inv.id}, ${total}, ${received}, '${inv.due_date || ''}')"
                                title="Edit Invoice">
                            <i class="bi bi-pencil-fill small"></i>
                        </button>
                        <button class="btn-icon-action btn-delete" 
                                onclick="deleteInvoice(${inv.id}, '${inv.inv_id || 'INV-' + inv.id}')"
                                title="Delete Invoice">
                            <i class="bi bi-trash-fill small"></i>
                        </button>
                    </td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5">
                <i class="bi bi-receipt text-muted fs-3 d-block mb-2 opacity-50"></i>
                <span class="text-muted small">No invoices found</span>
            </td></tr>`;
        }
    })
    .catch(error => {
        console.error('Error loading invoices:', error);
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger small">
            Failed to load invoices. Check console for details.
        </td></tr>`;
    });
}

// ============ LOAD PAYMENTS ============
function loadPayments() {
    const tbody = document.getElementById('paymentsTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">
        <span class="spinner-border spinner-border-xs text-primary" role="status"></span>
        <span class="ms-2 text-muted small">Loading payments...</span>
    </td></tr>`;
    
    // Try to load payments - adjust URL as needed
    const url = '/admin/interns/{{ $interneeDetails->id }}/payments';
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Payments data:', data);
        
        if (data.success && data.payments && data.payments.length > 0) {
            tbody.innerHTML = data.payments.map(payment => `
                <tr>
                    <td><span class="small">${new Date(payment.created_at || payment.date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'})}</span></td>
                    <td><span class="small fw-semibold">${payment.invoice_id || payment.inv_id || 'N/A'}</span></td>
                    <td><span class="small fw-bold text-success">PKR ${parseFloat(payment.amount || 0).toLocaleString()}</span></td>
                    <td><span class="small">${payment.method || payment.payment_method || 'N/A'}</span></td>
                    <td><span class="small">${payment.received_by || 'Admin'}</span></td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">
                <i class="bi bi-credit-card-2-front text-muted fs-3 d-block mb-2 opacity-50"></i>
                <span class="text-muted small">No payment records found</span>
            </td></tr>`;
        }
    })
    .catch(error => {
        console.error('Error loading payments:', error);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5">
            <i class="bi bi-credit-card-2-front text-muted fs-3 d-block mb-2 opacity-50"></i>
            <span class="text-muted small">No payment records found</span>
        </td></tr>`;
    });
}

// ============ EDIT INVOICE ============
function editInvoice(id, total, received, dueDate) {
    document.getElementById('edit_invoice_id').value = id;
    document.getElementById('edit_total_amount').value = total;
    document.getElementById('edit_received_amount').value = received;
    document.getElementById('edit_due_date').value = dueDate;
    
    const modal = new bootstrap.Modal(document.getElementById('editInvoiceModal'));
    modal.show();
}

// ============ UPDATE INVOICE ============
function updateInvoice(event) {
    event.preventDefault();
    
    const id = document.getElementById('edit_invoice_id').value;
    const btn = document.getElementById('updateInvoiceBtn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.classList.add('btn-loading');
    btn.innerHTML = '<span class="spinner-border spinner-border-xs me-2"></span>Saving...';
    
    const updateData = {
        total_amount: document.getElementById('edit_total_amount').value,
        received_amount: document.getElementById('edit_received_amount').value,
        due_date: document.getElementById('edit_due_date').value
    };
    
    console.log('Updating invoice:', id, updateData);
    
    fetch(`/admin/invoices/${id}/update`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(updateData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Update response:', data);
        btn.disabled = false;
        btn.classList.remove('btn-loading');
        btn.innerHTML = originalHTML;
        
        if (data.success) {
            showToast('Invoice updated!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editInvoiceModal')).hide();
            loadInvoices();
        } else {
            showToast(data.message || 'Update failed', 'error');
        }
    })
    .catch(error => {
        console.error('Update error:', error);
        btn.disabled = false;
        btn.classList.remove('btn-loading');
        btn.innerHTML = originalHTML;
        showToast('Network error', 'error');
    });
}

// ============ DELETE INVOICE ============
function deleteInvoice(id, invId) {
    Swal.fire({
        title: 'Delete Invoice?',
        html: `<span>Are you sure you want to delete <strong>${invId}</strong>? This action cannot be undone.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Deleting invoice:', id);
            
            fetch(`/admin/invoices/${id}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Delete response:', data);
                if (data.success) {
                    showToast('Invoice deleted!', 'success');
                    loadInvoices();
                } else {
                    showToast(data.message || 'Failed to delete', 'error');
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                showToast('Network error', 'error');
            });
        }
    });
}

// ============ REMOVE INTERN ============
document.getElementById('removeInternBtn')?.addEventListener('click', function() {
    const internId = this.dataset.id;
    
    Swal.fire({
        title: 'Remove Intern?',
        html: '<span>This will <strong>freeze</strong> the intern\'s portal access. They will no longer be able to log in.</span>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/interns/${internId}/remove-ajax`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Intern removed successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("all-interns-admin") }}';
                    }, 1500);
                } else {
                    showToast(data.message || 'Failed to remove', 'error');
                }
            })
            .catch(() => showToast('Network error', 'error'));
        }
    });
});

// ============ EDIT INTERN MODAL ============
document.getElementById('editInternBtn')?.addEventListener('click', function() {
    document.getElementById('edit_id').value = this.dataset.id;
    document.getElementById('edit_name').value = this.dataset.name;
    document.getElementById('edit_email').value = this.dataset.email;
    document.getElementById('edit_technology').value = this.dataset.technology;
    document.getElementById('edit_status').value = this.dataset.status;
});

// ============ TOAST NOTIFICATION ============
function showToast(message, type = 'success') {
    // Remove existing toasts
    document.querySelectorAll('.toast-custom').forEach(t => {
        t.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(() => t.remove(), 300);
    });
    
    const toast = document.createElement('div');
    toast.className = `toast-custom toast-${type}`;
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ============ INITIALIZATION ============
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page initialized');
    console.log('Intern data:', {
        id: '{{ $interneeDetails->id }}',
        name: '{{ $interneeDetails->name }}',
        email: '{{ $interneeDetails->email }}'
    });
    
    // Load invoices by default (since invoice tab is not active by default, 
    // we preload but don't display until tab is clicked)
    
    // Add click handlers to tabs in addition to onclick attribute
    document.querySelectorAll('.custom-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
});
</script>
@endsection