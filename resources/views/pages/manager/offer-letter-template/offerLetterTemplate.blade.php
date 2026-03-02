@extends('layouts/layoutMaster')

@section('title', 'Offer Letters')

@section('vendor-style')
<link rel="stylesheet" href="path-to/datatables.bootstrap5.css">
<link rel="stylesheet" href="path-to/responsive.bootstrap5.css">
<link rel="stylesheet" href="path-to/buttons.bootstrap5.css">
<link rel="stylesheet" href="path-to/select2.css">
<link rel="stylesheet" href="path-to/form-validation.css">
<link rel="stylesheet" href="path-to/animate.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
@endsection

@section('vendor-script')
<script src="path-to/moment.js"></script>
<script src="path-to/datatables-bootstrap5.js"></script>
<script src="path-to/select2.js"></script>
<script src="path-to/form-validation.js"></script>
<script src="path-to/cleave-zen.js"></script>
<script src="path-to/sweetalert2.js"></script>
@endsection

@section('content')
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Offer Letter Template</h4>
</div>

{{-- Error Messages --}}
@if($errors->any())
@foreach($errors->all() as $error)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ $error }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endforeach
@endif

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Auto-hide script --}}
<script>
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000); // 5 seconds
</script>
@php
$manager = auth()->guard('manager')->user();
@endphp
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex justify-content-between align-items-center  w-full" style="width: 100%;">
            <div class="dt-length mb-0 d-flex">

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

                <label for="dt-length-0"></label>
            </div>

            <div
                class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('manager.offer.letter.template') }}" id="filterForm"
                    class="d-flex gap-2">

                    <input type="search" name="search" id="searchInput" class="form-control"
                        placeholder="Search offer letter template" value="{{ request('search') }}">
                    <style>
                        input[type="search"]::-webkit-search-cancel-button,
                        input[type="search"]::-webkit-search-decoration {
                            -webkit-appearance: none;
                            appearance: none;
                        }
                    </style>
                    <select name="status" id="statusFilter" class="form-select text-capitalize">
                        <option value="">Select Status</option>

                        <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </form>
                @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'add_new_manager_offer_letter_template'))
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">+ Create
                    Template</button>
                    @endif
            </div>
        </div>
    </div>

    <div class="card-datatable">
        <div class="table-responsive" style="max-height:500px;">
            <table class="datatables-users table dataTable" style="width:100%">
                <thead class="border-top sticky-top bg-card">
                    <tr>
                        <th data-dt-column="1" rowspan="1" colspan="1"
                            class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Full Name" tabindex="0">
                            <span class="dt-column-title" role="button">#</span><span class="dt-column-order"></span>
                        </th>
                        <th data-dt-column="2" rowspan="1" colspan="1"
                            class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Profile Picture"
                            tabindex="0"><span class="dt-column-title" role="button">Template Name
                            </span><span class="dt-column-order"></span></th>
                        <th data-dt-column="3" rowspan="1" colspan="1"
                            class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Full Name" tabindex="0">
                            <span class="dt-column-title" role="button">Status</span><span
                                class="dt-column-order"></span>
                        </th>
                        <th data-dt-column="5" rowspan="1" colspan="1"
                            class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="City" tabindex="0"><span
                                class="dt-column-title" role="button">Created AT</span><span
                                class="dt-column-order"></span></th>
                        <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                            aria-label="Join Date"><span class="dt-column-title">Action</span><span
                                class="dt-column-order"></span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                    <tr>
                        <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"></span>1</td>
                        <td><span class="text-heading text-nowrap">{{$template->title}}</span></td>
                        <td>
                            @php
                            // Status numeric hai toh directly '1' ya '0' check karein
                            $statusClasses = [
                            '1' => 'bg-label-success',
                            '0' => 'bg-label-danger',
                            ];

                            $status = $template->status; // strtolower hata diya
                            $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                            @endphp

                            <span class="badge {{ $badgeClass }} text-capitalize">
                                @if ($template->status == 1)
                                Active
                                @else
                                Inactive
                                @endif
                            </span>
                        </td>
                        <td><span class="text-heading text-nowrap">{{$template->created_at}}</span></td>
                        <td>
                            @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'edit_manager_offer_letter_template') || $manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'preview_manager_offer_letter_template') || $manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'delete_manager_offer_letter_template'))
                            <div class="d-flex align-items-center">
                                <div class="dropdown">
                                    <a href="javascript:;"
                                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end m-0">
                                        @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'preview_manager_offer_letter_template'))
                                        <a href="javascript:;" 
   class="dropdown-item preview-btn" 
   data-bs-toggle="modal"
   data-bs-target="#previewTemplateModal"
   data-title="{{ $template->title }}"
   data-content="{{ $template->content }}">
    Preview
</a>
@endif

                    @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'edit_manager_offer_letter_template'))
                                        <a href="javascript:;" class="dropdown-item edit-btn" data-bs-toggle="modal"
                                            data-bs-target="#editTemplateModal" data-id="{{ $template->id }}"
                                            data-title="{{ $template->title }}" {{-- FIX HERE: Remove @json and use raw
                                            data --}} data-content="{{ $template->content }}"
                                            data-status="{{ $template->status }}"
                                            data-access="{{ $template->can_use_other_template }}">
                                            Edit
                                        </a>
                                        @endif
                                        @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'delete_manager_offer_letter_template'))
                                        <a href="javascript:;" class="dropdown-item delete-btn"
                                            data-id="{{ $template->id }}">
                                            Remove
                                        </a>
                                        @endif
                                        {{-- Hidden Form for Deletion --}}
                                        <form id="deleteForm" method="POST" action="" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <p class="text-center mb-0">No data available!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Structure Moved Here (Outside Modal) --}}
    <div class="card-footer py-4">
        <div class="row justify-content-between">
            {{-- Info --}}
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                <div class="dt-info" aria-live="polite">
                    Showing {{ $templates->firstItem() ?? 0 }} to {{ $templates->lastItem() ?? 0 }} of {{
                    $templates->total()
                    }} entries
                </div>
            </div>

            {{-- Pagination --}}
            <div
                class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                <div class="dt-paging">
                    <nav aria-label="pagination">
                        <ul class="pagination mb-0">
                            {{-- First Page --}}
                            <li class="dt-paging-button page-item {{ $templates->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" style="border-radius: 5px;" href="{{ $templates->url(1) }}"
                                    aria-label="First">
                                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                </a>
                            </li>

                            {{-- Previous Page --}}
                            <li class="dt-paging-button page-item {{ $templates->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" style="border-radius: 5px;"
                                    href="{{ $templates->previousPageUrl() }}" aria-label="Previous">
                                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                </a>
                            </li>

                            {{-- Page Numbers --}}
                            @foreach ($templates->getUrlRange(max(1, $templates->currentPage() - 2),
                            min($templates->lastPage(),
                            $templates->currentPage() + 2)) as $page => $url)
                            <li
                                class="dt-paging-button page-item {{ $page == $templates->currentPage() ? 'active' : '' }}">
                                <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach

                            {{-- Next Page --}}
                            <li
                                class="dt-paging-button page-item {{ $templates->currentPage() == $templates->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" style="border-radius: 5px;" href="{{ $templates->nextPageUrl() }}"
                                    aria-label="Next">
                                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                </a>
                            </li>

                            {{-- Last Page --}}
                            <li
                                class="dt-paging-button page-item {{ $templates->currentPage() == $templates->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" style="border-radius: 5px;"
                                    href="{{ $templates->url($templates->lastPage()) }}" aria-label="Last">
                                    <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div> {{-- End Card Footer --}}
</div> {{-- End Card --}}

{{-- Preview Template Modal --}}
<div class="modal fade" id="previewTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-2">
            <div class="modal-body">
                <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-start mb-4">
                    <h4 id="previewModalTitle">Template Name</h4>
                </div>
                
                {{-- Template Content Area --}}
                <div class="border p-3 rounded" style="min-height: 200px; background-color: #f8f9fa;">
                    <div id="previewModalContent"></div>
                </div>

                <div class="text-end mt-4">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Create Template Modal --}}
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-simple modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-2">
            <div class="modal-body">
                <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-start mb-6">
                    <h4 class="role-title">Add New Template</h4>
                </div>

                <form method="POST" action="{{ route('manager.offer.letter.template.create') }}" class="row g-3"
                    id="createTemplateForm">
                    @csrf
                    <div class="col-12 col-md-12 form-control-validation mb-3">
                        <label for="edit-amount" class="form-label">Template Name</label>
                        <input type="text" class="form-control" name="title" id="title"
                            placeholder="Enter Template Name..." required />
                    </div>

                    <div class="col-12 form-control-validation mb-3">
                        <label class="form-label" for="edit-description">Content <span
                                class="text-primary">(Placeholders: name, email, join_date, end_date, technology,
                                duration)</span></label>
                        <textarea name="content" rows="10" id="content" class="form-control" placeholder="Write body..."
                            required></textarea>
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                        <label for="can_use_other_template" class="form-label">Access</label>
                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                            <input class="form-check-input" type="checkbox" name='can_use_other_template'
                                id="can_use_other_template" />
                            <label class="form-check-label" for="manager"> Other can use this! </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                        <label class="form-label">Status</label>

                        <div class="d-flex gap-4">
                            <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                                <input class="form-check-input" type="radio" name="status" id="statusActive" value="1"
                                    checked />
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>

                            <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                                <input class="form-check-input" type="radio" name="status" id="statusInactive"
                                    value="0" />
                                <label class="form-check-label" for="statusFreeze">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- EDIT TEMPLATE MODAL --}}
<div class="modal fade" id="editTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-simple modal-dialog-centered">
        <div class="modal-content p-2">
            <div class="modal-body">
                <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-start mb-6">
                    <h4 class="role-title">Edit Template</h4>
                </div>

                <form method="POST" action="" class="row g-3" id="editTemplateForm">
                    @csrf
                    @method('PUT')

                    <div class="col-12 mb-3">
                        <label class="form-label">Template Name</label>
                        {{-- UNIQUE ID --}}
                        <input type="text" class="form-control" name="title" id="edit-title" required />
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Content <span class="text-primary">(Placeholders: name, email,
                                join_date, end_date, technology,
                                duration)</span></label>
                        {{-- UNIQUE ID --}}
                        <textarea name="content" rows="10" id="edit-content" class="form-control" required></textarea>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label">Access</label>
                        <div class="form-check mt-2">
                            {{-- UNIQUE ID --}}
                            <input class="form-check-input" type="checkbox" name='can_use_other_template'
                                id="edit-can_use_other_template" value="1" />
                            <label class="form-check-label" for="edit-can_use_other_template"> Other can use this!
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <div class="d-flex gap-4">
                            <div class="form-check mt-2">
                                {{-- UNIQUE ID --}}
                                <input class="form-check-input" type="radio" name="status" id="editStatusActive"
                                    value="1" />
                                <label class="form-check-label" for="editStatusActive">Active</label>
                            </div>
                            <div class="form-check mt-2">
                                {{-- UNIQUE ID --}}
                                <input class="form-check-input" type="radio" name="status" id="editStatusInactive"
                                    value="0" />
                                <label class="form-check-label" for="editStatusInactive">Inactive</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="button" class="btn btn-label-secondary me-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    let timer;

  document.getElementById('searchInput').addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500); // wait 500ms after typing
  });

  document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
  });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const previewModal = document.getElementById('previewTemplateModal');

    document.addEventListener('click', function (e) {
        const button = e.target.closest('.preview-btn');
        if (!button) return;

        // Get data from clicked button
        const title = button.getAttribute('data-title');
        const content = button.getAttribute('data-content');

        // Set data in Modal
        previewModal.querySelector('#previewModalTitle').textContent = title;
        // Using .innerHTML if content contains HTML tags, or .textContent if plain text
        previewModal.querySelector('#previewModalContent').innerHTML = content;
    });
});
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editTemplateModal');

    document.addEventListener('click', function (e) {
        const button = e.target.closest('.edit-btn');
        if (!button) return;

        // Get Data
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const content = button.getAttribute('data-content');
        const status = button.getAttribute('data-status');
        const access = button.getAttribute('data-access');

        // Set Title & Content
        editModal.querySelector('#edit-title').value = title;
        editModal.querySelector('#edit-content').value = content;

        // ✅ FIXED STATUS LOGIC (IMPORTANT)
        const numericStatus = Number(status);

        editModal.querySelector('#editStatusActive').checked = numericStatus === 1;
        editModal.querySelector('#editStatusInactive').checked = numericStatus === 0;

        // Checkbox
        editModal.querySelector('#edit-can_use_other_template').checked = Number(access) === 1;

        // Set Form Action
        let actionUrl = "{{ route('manager.offer.letter.template.update', ':id') }}";
        actionUrl = actionUrl.replace(':id', id);
        editModal.querySelector('#editTemplateForm').setAttribute('action', actionUrl);
    });
});
</script>

<style>
    .swal2-container {
        z-index: 10000 !important;
        /* Bootstrap modal usually has 1050-1060, so 10000 is safe */
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // The click event listener needs to be wrapped properly
document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (e) {
        const button = e.target.closest('.delete-btn');
        if (!button) return;

        const id = button.getAttribute('data-id');
        const deleteForm = document.getElementById('deleteForm');
        
        // Action URL
        let deleteUrl = "{{ route('manager.offer.letter.template.delete', ':id') }}";
        deleteUrl = deleteUrl.replace(':id', id);

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                deleteForm.action = deleteUrl;
                deleteForm.submit();
            }
        });
    });
}); // <-- Make sure there is only one closing brace here
</script>

@endpush

@endsection