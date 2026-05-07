@extends('layouts/layoutMaster')

@section('title', 'All Interns')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 
'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
<style>
    .clickable-row { cursor: pointer; }
    .clickable-row:hover { background-color: rgba(0, 0, 0, 0.03); }
    .table-responsive-overflow { max-height: 600px; overflow-y: auto; overflow-x: auto; }
    #bulkChangeBtn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">All Interns</h4>
</div>

{{-- Alerts --}}
@if($errors->any())
    @foreach($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endforeach
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<script>
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

<div class="card">
    <div class="card-datatable">
        <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
            
            {{-- ========== TOP CONTROLS ========== --}}
            <div class="row m-3 my-0 justify-content-between">
                {{-- Per Page --}}
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-length mb-md-6 mb-0 d-flex">
                        <form id="perPageForm" method="GET">
                            <select name="per_page" id="dt-length-0" class="form-select ms-0"
                                onchange="document.getElementById('perPageForm').submit()">
                                <option value="15" {{ $perPage==15 ? 'selected' : '' }}>15</option>
                                <option value="25" {{ $perPage==25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
                            </select>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="package" value="{{ request('package') }}">
                        </form>
                    </div>
                </div>

                {{-- Right Side: Search + Package + Change Status + Export --}}
                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
                    
                    <form method="GET" action="{{ route('all-interns-admin') }}" id="filterForm" class="d-flex gap-2">
                        {{-- Search --}}
                        <input type="search" name="search" id="searchInput" class="form-control" 
                            placeholder="Search Internee" value="{{ request('search') }}">
                        <style>
                            input[type="search"]::-webkit-search-cancel-button,
                            input[type="search"]::-webkit-search-decoration {
                                -webkit-appearance: none; appearance: none;
                            }
                        </style>

                         {{-- ✅ Status Filter --}}
        <select id="statusFilter" class="form-select text-capitalize">
            <option value="">Select Status</option>
            @foreach (['Interview','Contact','Test','Completed','Active','Removed'] as $s)
            @php $slug = strtolower($s); @endphp
            <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
        </select>
        <input type="hidden" name="status" id="statusInput" value="{{ request('status') }}">

                        {{-- Package Filter --}}
                        <select name="package" id="packageFilter" class="form-select text-capitalize">
                            <option value="">All Packages</option>
                            <option value="training" {{ request('package')=='training' ? 'selected' : '' }}>Training Internship</option>
                            <option value="practice" {{ request('package')=='practice' ? 'selected' : '' }}>Project Practice</option>
                            <option value="industrial" {{ request('package')=='industrial' ? 'selected' : '' }}>Industrial Environment</option>
                        </select>
                    </form>

                 {{-- ====== CHANGE SELECTED STATUS ====== --}}
<div class="btn-group" role="group">
    <button id="bulkChangeBtn" type="button" class="btn btn-warning dropdown-toggle"
        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
        <i class="icon-base ti tabler-status-change icon-xs me-1"></i>
        Change Selected Status
        <span id="selectedCountBadge" class="badge bg-dark ms-1" style="display:none;">0</span>
    </button>
    <div class="dropdown-menu" style="z-index: 1021">
        <h6 class="dropdown-header">Move selected interns to:</h6>
        <a class="dropdown-item bulk-status-item" href="javascript:void(0);" data-status="interview">
            <i class="icon-base ti tabler-arrow-right me-2"></i> Interview
        </a>
        <a class="dropdown-item bulk-status-item" href="javascript:void(0);" data-status="contact">
            <i class="icon-base ti tabler-arrow-right me-2"></i> Contact
        </a>
        <a class="dropdown-item bulk-status-item" href="javascript:void(0);" data-status="test">
            <i class="icon-base ti tabler-arrow-right me-2"></i> Test
        </a>
        <a class="dropdown-item bulk-status-item" href="javascript:void(0);" data-status="completed">
            <i class="icon-base ti tabler-arrow-right me-2"></i> Completed
        </a>
        <a class="dropdown-item bulk-status-item" href="javascript:void(0);" data-status="active">
            <i class="icon-base ti tabler-arrow-right me-2"></i> Active
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item bulk-status-item text-danger" href="javascript:void(0);" data-status="removed">
            <i class="icon-base ti tabler-trash me-2"></i> Removed
        </a>
    </div>
</div>

                    {{-- ====== EXPORT ====== --}}
                    @php
                    $adminSettings = \App\Models\AdminSetting::first();
                    $isAdminAllowed = !$adminSettings || (isset($adminSettings->export_permissions['admin']) && $adminSettings->export_permissions['admin'] == 1);
                    @endphp
                    @if($isAdminAllowed)
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-base ti tabler-upload icon-xs me-1"></i>
                            Export
                        </button>
                        <div class="dropdown-menu" style="z-index: 1021">
                            <a class="dropdown-item" href="javascript:void(0);" onclick="downloadCSV()">
                                <i class="icon-base ti tabler-file-spreadsheet me-2"></i> Export All
                            </a>
                            <a class="dropdown-item" href="javascript:void(0);" onclick="exportSelectedCSV()">
                                <i class="icon-base ti tabler-file-export me-2"></i> Export Selected
                                <span id="exportBadge" class="badge bg-primary ms-1" style="display:none;">0</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ========== TABLE ========== --}}
            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive table-responsive-overflow">
                    <table class="datatables-users table dataTable dtr-column" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input" />
                                </th>
                                <th class="text-nowrap">Profile</th>
                                <th class="text-nowrap">Full Name</th>
                                <th class="text-nowrap">Phone</th>
                                <th class="text-nowrap">Join Date</th>
                                <th class="text-nowrap">Technology</th>
                                <th class="text-nowrap">Type</th>
                                <th class="text-nowrap">Package</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">City</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allInterns as $intern)
                            <tr class="clickable-row" data-href="{{ route('view.profile.internee.admin', $intern->id) }}">
                                {{-- Checkbox --}}
                                <td class="checkbox-cell">
                                    <input type="checkbox" class="form-check-input intern-checkbox" value="{{ $intern->id }}" />
                                </td>
                             {{-- Profile --}}
<td class="clickable-cell">
    <div class="d-flex justify-content-start align-items-center user-name">
        <div class="avatar-wrapper">
@if(!empty($intern->image) && $intern->image !== '')
            <div class="avatar avatar-md me-4">
                <img src="{{ str_starts_with($intern->image, 'data:image') ? $intern->image : asset($intern->image) }}" 
                     alt="{{ $intern->name }}" class="rounded-circle" />
            </div>
            @else
            <div class="avatar avatar-md me-4">
                <span class="avatar-initial rounded-circle bg-label-warning">
                    {{ strtoupper(substr($intern->name, 0, 2)) }}
                </span>
            </div>
            @endif
        </div>
    </div>
</td>
                                {{-- Full Name --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">{{$intern->name}}</span>
                                </td>
                                {{-- Phone --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">
                                        <i class="icon-base ti tabler-phone me-2 text-info icon-22px"></i>
                                        <small>{{$intern->phone ?? 'N/A'}}</small>
                                    </span>
                                </td>
                                {{-- Join Date --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">{{$intern->join_date ?? 'N/A'}}</span>
                                </td>
                                {{-- Technology --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">{{$intern->technology}}</span>
                                </td>
                                {{-- Type --}}
                                <td class="clickable-cell">
                                    @php $typeClass = strtolower($intern->interview_type ?? '') === 'remote' ? 'bg-label-primary' : 'bg-label-info'; @endphp
                                    <span class="badge {{ $typeClass }} text-capitalize">{{ $intern->interview_type ?? 'N/A' }}</span>
                                </td>
                                {{-- Package --}}
<td class="clickable-cell">
    @php
    $packageLabels = ['training' => 'Training','practice' => 'Practice','industrial' => 'Industrial'];
    $packageColors = ['training' => 'bg-label-info','practice' => 'bg-label-primary','industrial' => 'bg-label-success'];
    $pkg = strtolower($intern->package ?? '');
    $badgeClass = $packageColors[$pkg] ?? 'bg-label-secondary';
    $label = $packageLabels[$pkg] ?? ($intern->package ?: 'Training');
    @endphp
    <span class="badge {{ $badgeClass }} text-capitalize">{{ $label }}</span>
</td>
                                {{-- Status --}}
                                <td class="clickable-cell">
                                    @php
                                    $statusClasses = ['interview' => 'bg-label-primary','contact' => 'bg-label-info','test' => 'bg-label-warning','completed' => 'bg-label-success','active' => 'bg-label-success','removed' => 'bg-label-danger'];
                                    $badgeClass = $statusClasses[strtolower($intern->status)] ?? 'bg-label-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-capitalize">{{ strtolower($intern->status) }}</span>
                                </td>
                                {{-- City --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">{{ $intern->city ?? 'N/A' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10"><p class="text-center mb-0">No data available!</p></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========== PAGINATION ========== --}}
            <div class="row mx-3 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-info">
                        Showing {{ $allInterns->firstItem() ?? 0 }} to {{ $allInterns->lastItem() ?? 0 }} of {{ $allInterns->total() ?? 0 }} entries
                    </div>
                </div>
                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
                    <div class="dt-paging">
                        <nav aria-label="pagination">
                            <ul class="pagination">
                                <li class="page-item {{ $allInterns->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $allInterns->url(1) }}" aria-label="First">
                                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $allInterns->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $allInterns->previousPageUrl() }}" aria-label="Previous">
                                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                @foreach ($allInterns->getUrlRange(max(1, $allInterns->currentPage() - 2), min($allInterns->lastPage(), $allInterns->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $page == $allInterns->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                                @endforeach
                                <li class="page-item {{ $allInterns->currentPage() == $allInterns->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $allInterns->nextPageUrl() }}" aria-label="Next">
                                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $allInterns->currentPage() == $allInterns->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $allInterns->url($allInterns->lastPage()) }}" aria-label="Last">
                                        <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Bulk Status Form --}}
<form id="bulkStatusForm" method="POST" action="{{ route('admin.interns.bulk.status') }}" style="display: none;">
    @csrf
    <input type="hidden" name="intern_ids" id="bulkInternIds" />
    <input type="hidden" name="new_status" id="bulkNewStatus" />
    <input type="hidden" name="search" value="{{ request('search') }}" />
    <input type="hidden" name="package" value="{{ request('package') }}" />
    <input type="hidden" name="per_page" value="{{ $perPage }}" />
</form>

<script>
    let timer;

    // ========== SEARCH ==========
    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
    });

    // ========== STATUS FILTER ==========
document.getElementById('statusFilter').addEventListener('change', function () {
    const statusInput = document.getElementById('statusInput');
    statusInput.value = this.value;
    document.getElementById('filterForm').submit();
});


    // ========== PACKAGE FILTER ==========
    document.getElementById('packageFilter').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });

    // ========== ROW CLICK ==========
    document.querySelectorAll('.clickable-cell').forEach(cell => {
        cell.addEventListener('click', function() {
            const row = this.closest('tr.clickable-row');
            if (row && row.dataset.href) {
                window.location.href = row.dataset.href;
            }
        });
    });

    // ========== CHECKBOXES ==========
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        document.querySelectorAll('.intern-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectionUI();
    });

    document.querySelectorAll('.intern-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectionUI();
            const allCb = document.querySelectorAll('.intern-checkbox');
            const checkedCb = document.querySelectorAll('.intern-checkbox:checked');
            document.getElementById('selectAllCheckbox').checked = 
                allCb.length === checkedCb.length && allCb.length > 0;
        });
    });

    document.querySelectorAll('.checkbox-cell').forEach(cell => {
        cell.addEventListener('click', e => e.stopPropagation());
    });

    function updateSelectionUI() {
        const checkedCount = document.querySelectorAll('.intern-checkbox:checked').length;
        const bulkBtn = document.getElementById('bulkChangeBtn');
        const countBadge = document.getElementById('selectedCountBadge');
        const exportBadge = document.getElementById('exportBadge');
        
        if (checkedCount > 0) {
            bulkBtn.disabled = false;
            countBadge.style.display = 'inline-block';
            countBadge.textContent = checkedCount;
            if (exportBadge) {
                exportBadge.style.display = 'inline-block';
                exportBadge.textContent = checkedCount;
            }
        } else {
            bulkBtn.disabled = true;
            countBadge.style.display = 'none';
            if (exportBadge) exportBadge.style.display = 'none';
        }
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.intern-checkbox:checked')).map(cb => cb.value);
    }

    // ========== BULK STATUS CHANGE ==========
    document.querySelectorAll('.bulk-status-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const status = this.dataset.status;
            const checkedIds = getSelectedIds();

            if (checkedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No interns selected',
                    text: 'Please select at least one intern.',
                    position: 'center',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }

            Swal.fire({
                title: 'Change Status?',
                html: `Move <b>${checkedIds.length}</b> selected intern(s) to <b class="text-capitalize">${status}</b>?`,
                icon: 'question',
                position: 'center',
                showCancelButton: true,
                confirmButtonText: 'Yes, change!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bulkInternIds').value = checkedIds.join(',');
                    document.getElementById('bulkNewStatus').value = status;
                    document.getElementById('bulkStatusForm').submit();
                }
            });
        });
    });

    // ========== EXPORT ==========
    function exportSelectedCSV() {
        const checkedIds = getSelectedIds();
        if (checkedIds.length === 0) {
            Swal.fire({
                icon: 'warning', title: 'No interns selected',
                text: 'Please select at least one intern to export.',
                position: 'center', confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            return;
        }
        Swal.fire({
            title: 'Export Selected?', text: `Export ${checkedIds.length} selected intern(s)?`,
            icon: 'question', position: 'center',
            showCancelButton: true, confirmButtonText: 'Yes, export!', cancelButtonText: 'Cancel',
            customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-secondary' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('admin.interns.export.selected') }}?ids=" + checkedIds.join(',');
            }
        });
    }

    function downloadCSV() {
        const params = new URLSearchParams({
            search: document.getElementById('searchInput').value,
            package: document.getElementById('packageFilter').value
        });
        window.location.href = "{{ route('all.interns.export.csv.admin') }}?" + params.toString();
    }

    // ========== CENTER TOAST ==========
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('toast_success'))
        Swal.fire({
            icon: 'success', title: 'Done!',
            text: '{{ session('toast_success') }}',
            position: 'center', showConfirmButton: false,
            timer: 3000, timerProgressBar: true
        });
        @endif
        @if(session('toast_error'))
        Swal.fire({
            icon: 'error', title: 'Error!',
            text: '{{ session('toast_error') }}',
            position: 'center', showConfirmButton: false,
            timer: 3000, timerProgressBar: true
        });
        @endif
    });
</script>

@endsection