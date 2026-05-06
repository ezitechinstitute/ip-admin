@extends('layouts/layoutMaster')

@section('title', 'Intern Accounts')

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
</style>

<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Intern Accounts</h4>
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
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </form>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
                    <form method="GET" action="{{ route('intern-accounts-admin') }}" id="filterForm" class="d-flex gap-2">
                        <input type="search" name="search" id="searchInput" class="form-control" 
                            placeholder="Search Internee" value="{{ request('search') }}">
                        <style>
                            input[type="search"]::-webkit-search-cancel-button,
                            input[type="search"]::-webkit-search-decoration {
                                -webkit-appearance: none; appearance: none;
                            }
                        </style>
                        <select name="status" id="statusFilter" class="form-select text-capitalize">
                            <option value="">All Status</option>
                            @foreach (['Test','Active'] as $status)
                            @php $slug = strtolower($status); @endphp
                            <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                    </form>

                    {{-- ====== EXPORT ====== --}}
                    @php
                    $adminSettings = \App\Models\AdminSetting::first();
                    $isAdminAllowed = !$adminSettings || (isset($adminSettings->export_permissions['admin']) && $adminSettings->export_permissions['admin'] == 1);
                    @endphp
                    @if($isAdminAllowed)
                    <button type="button" class="btn btn-outline-primary" onclick="downloadInternAccountsCSV()">
                        <i class="icon-base ti tabler-upload icon-xs me-1"></i> Export
                    </button>
                    @endif
                </div>
            </div>

            {{-- ========== TABLE ========== --}}
            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive table-responsive-overflow">
                    <table class="datatables-users table dataTable dtr-column" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="text-nowrap">Profile</th>
                                <th class="text-nowrap">ETI-ID</th>
                                <th class="text-nowrap">Full Name</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Tech</th>
                                <th class="text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($internAccounts as $intern)
                            <tr class="clickable-row" data-href="{{ route('view.profile.interne.account.admin', $intern->int_id) }}">
                             {{-- Profile Avatar --}}
<td class="clickable-cell">
    <div class="d-flex justify-content-start align-items-center user-name">
        <div class="avatar-wrapper">
            @if($intern->image)
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
                                {{-- ETI-ID --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap"><small>{{$intern->eti_id}}</small></span>
                                </td>
                                {{-- Full Name --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">{{$intern->name}}</span>
                                </td>
                                {{-- Email --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">
                                        <i class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i>
                                        <small>{{$intern->email}}</small>
                                    </span>
                                </td>
                                {{-- Tech --}}
                                <td class="clickable-cell">
                                    <span class="text-heading text-nowrap">
                                        <i class="icon-base ti tabler-cpu me-1 text-primary icon-22px"></i>
                                        {{$intern->int_technology}}
                                    </span>
                                </td>
                                {{-- Status --}}
                                <td class="clickable-cell">
                                    @php
                                    $statusClasses = [
                                        'test' => 'bg-label-warning',
                                        'active' => 'bg-label-success',
                                        'freeze' => 'bg-label-danger',
                                    ];
                                    $status = strtolower($intern->int_status);
                                    $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6"><p class="text-center mb-0">No data available!</p></td>
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
                        Showing {{ $internAccounts->firstItem() ?? 0 }} to {{ $internAccounts->lastItem() ?? 0 }} of {{ $internAccounts->total() ?? 0 }} entries
                    </div>
                </div>
                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
                    <div class="dt-paging">
                        <nav aria-label="pagination">
                            <ul class="pagination">
                                <li class="page-item {{ $internAccounts->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $internAccounts->url(1) }}" aria-label="First">
                                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $internAccounts->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $internAccounts->previousPageUrl() }}" aria-label="Previous">
                                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                @foreach ($internAccounts->getUrlRange(max(1, $internAccounts->currentPage() - 2), min($internAccounts->lastPage(), $internAccounts->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $page == $internAccounts->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                                @endforeach
                                <li class="page-item {{ $internAccounts->currentPage() == $internAccounts->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $internAccounts->nextPageUrl() }}" aria-label="Next">
                                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="page-item {{ $internAccounts->currentPage() == $internAccounts->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $internAccounts->url($internAccounts->lastPage()) }}" aria-label="Last">
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

<script>
    let timer;

    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
    });

    document.getElementById('statusFilter').addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });

    // ========== ROW CLICK NAVIGATION ==========
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            if (this.dataset.href) {
                window.location.href = this.dataset.href;
            }
        });
    });

    // ========== EXPORT ==========
    function downloadInternAccountsCSV() {
        const urlParams = new URLSearchParams(window.location.search);
        const search = urlParams.get('search') || '';
        const status = urlParams.get('status') || '';
        const exportUrl = "{{ route('export.intern.csv.admin') }}?" + 
                          "search=" + encodeURIComponent(search) + 
                          "&status=" + encodeURIComponent(status);
        window.location.href = exportUrl;
    }
</script>

@endsection