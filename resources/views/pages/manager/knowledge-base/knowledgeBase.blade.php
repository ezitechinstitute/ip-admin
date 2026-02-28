@extends('layouts/layoutMaster')

@section('title', 'Knowledgebase')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@endsection

@section('content')

<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Manager Knowledge Base</h4>
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

<div class="card">
  {{-- <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Knowledgebase
    </h5>

  </div> --}}
  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="d-flex m-3 mx-6 my-6 gap-3 justify-content-between" style="align-items: center">

        <form method="GET" action="{{ route('manager.knowledge.base') }}" id="filterForm"
          class="d-flex flex-wrap gap-2 justify-content-between w-100">

          {{-- LEFT SIDE --}}
          <div class="d-flex gap-2">

            {{-- Per Page --}}
            <select name="per_page" class="form-select" onchange="document.getElementById('filterForm').submit()">
              <option value="15" {{ (isset($perPage) ? $perPage : request('per_page'))==15 ? 'selected' : '' }}>15
              </option>
              <option value="25" {{ (isset($perPage) ? $perPage : request('per_page'))==25 ? 'selected' : '' }}>25
              </option>
              <option value="50" {{ (isset($perPage) ? $perPage : request('per_page'))==50 ? 'selected' : '' }}>50
              </option>
              <option value="100" {{ (isset($perPage) ? $perPage : request('per_page'))==100 ? 'selected' : '' }}>100
              </option>
            </select>

          </div>

          {{-- RIGHT SIDE --}}
          <div class="d-flex gap-2">

            {{-- Search --}}
            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search title..."
              value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>

            @php
            $adminSettings = \App\Models\AdminSetting::first();

            if (!$adminSettings) {
            $isAdminAllowed = true;
            } else {
            $permissions = $adminSettings->export_permissions;
            $isAdminAllowed = isset($permissions['manager']) && $permissions['manager'] == 1;
            }
            @endphp

            @if($isAdminAllowed)
            <div class="btn-group" role="group">
              <button id="btnGroupDrop1" type="button" class="btn add-new btn-outline-primary dropdown-toggle"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-base ti tabler-dots-vertical icon-md d-sm-none"></i>
                <i class="icon-base ti tabler-upload icon-xs me-2"></i>
                <span class="d-none d-sm-block">Export</span>
              </button>
              <div class="dropdown-menu" style="z-index: 1021" aria-labelledby="btnGroupDrop1">
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadKnowledgeBaseManagerCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
                </a>
              </div>
            </div>
            @endif
          </div>

        </form>








        <div class="modal fade" id="viewKnowledgeModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="viewKnowledgeTitle">Knowledge Base</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="viewKnowledgeContent" style="max-height: 400px; overflow-y: auto;">
                <!-- Content will be injected here -->
              </div>
            </div>
          </div>
        </div>




      </div>

      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
          style="max-height: 500px;">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($articles as $kb)
              @php
              $statusClasses = [
              'active' => 'bg-label-success',
              'inactive' => 'bg-label-danger',
              ];
              $status = strtolower($kb->status);
              $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';

              // Decode visibility JSON if it's a string, else leave as is
              $visibilityArray = is_string($kb->visibility) ? json_decode($kb->visibility, true) :
              (array)$kb->visibility;

              // Prepare data for JS Edit modal
              $kbData = [
              'id' => $kb->id,
              'title' => $kb->title,
              'category' => $kb->category,
              'content' => $kb->content,
              'status' => $kb->status,
              'visibility' => $visibilityArray,
              ];
              @endphp


              <tr>
                <td>{{ $loop->iteration + ($articles->currentPage()-1)*$articles->perPage() }}</td>
                <td>{{ $kb->title }}</td>
                <td>{{ $kb->category }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
                <td class="d-flex gap-3">
                  <a href="javascript:void(0);" class="viewBtn" data-bs-toggle="modal"
                    data-bs-target="#viewKnowledgeModal" data-title="{{ $kb->title }}"
                    data-content='@json($kb->content)'>
                    <i class="icon-base ti tabler-eye text-success icon-22px"></i>
                  </a>

                  {{-- <a href="javascript:void(0);" class="editBtn" data-bs-toggle="modal"
                    data-bs-target="#editKnowledgeBase" data-id="{{ $kb->id }}"
                    data-kb='@json($kbData, JSON_HEX_APOS|JSON_HEX_QUOT)'
                    data-update-url="{{ route('knowledge.update.admin', ['id' => $kb->id]) }}">
                    <i class="icon-base ti tabler-edit icon-22px"></i>
                  </a> --}}
                  <!-- Delete Button -->
                  {{-- <a href="javascript:void(0);" class="delete-record" data-id="{{ $kb->id }}">
                    <i class="icon-base ti tabler-trash text-danger icon-22px"></i>
                  </a> --}}

                  <!-- Hidden Delete Form -->
                  {{-- <form id="delete-form-{{ $kb->id }}" action="{{ route('knowledge.delete.admin', $kb->id) }}"
                    method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                  </form> --}}

                </td>

              </tr>

              @empty
              <tr>
                <td colspan="5" class="text-center">No data available!</td>
              </tr>
              @endforelse
            </tbody>

          </table>




        </div>
      </div>
      <div class="row mx-3 pt-3 justify-content-between">

        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $articles->firstItem() ?? 0 }} to {{ $articles->lastItem() ?? 0 }} of {{
            $articles->total() ??
            0 }} entries
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">

                <li class="dt-paging-button page-item {{ $articles->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $articles->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li class="dt-paging-button page-item {{ $articles->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $articles->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                @foreach ($articles->getUrlRange(max(1, $articles->currentPage() - 2),
                min($articles->lastPage(),
                $articles->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $articles->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach


                <li
                  class="dt-paging-button page-item {{ $articles->currentPage() == $articles->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $articles->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li
                  class="dt-paging-button page-item {{ $articles->currentPage() == $articles->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $articles->url($articles->lastPage()) }}" aria-label="Last">
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
@endpush


@push('scripts')
<script>
  const quill = new Quill('#editor', {
theme: 'snow',
placeholder: 'Write knowledge base content here...'
});

document.getElementById('addRoleForm')
.addEventListener('submit', function(e){

// visibility validation
const checked =
document.querySelectorAll('.visibility-checkbox:checked');

if(checked.length === 0){
e.preventDefault();
document.getElementById('visibilityError')
.classList.remove('d-none');
return;
}

// save editor content
document.getElementById('contentInput').value =
quill.root.innerHTML;

});

</script>

<script>
  document.getElementById('addRoleForm').addEventListener('submit', function(e) {

    const checked = document.querySelectorAll('.visibility-checkbox:checked');
    const errorBox = document.getElementById('visibilityError');

    // check at least one selected
    if (checked.length === 0) {
        e.preventDefault();
        errorBox.classList.remove('d-none');
        return false;
    } else {
        errorBox.classList.add('d-none');
    }

});
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const kb = JSON.parse(btn.dataset.kb);
        openEditModal(kb);
    });
});

</script>

@endpush



@push('scripts')
<script>
  const editQuill = new Quill('#editEditor', {
  theme: 'snow',
  placeholder: 'Write knowledge base content here...'
});

// Function to open Edit Modal
function openEditModal(kb, url) {
    // Remove any existing backdrop
    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    document.body.classList.remove('modal-open');

    // Fill form fields
    document.getElementById('editTitle').value = kb.title;
    document.getElementById('editCategory').value = kb.category;
    editQuill.root.innerHTML = kb.content;

    // Set status
    if(kb.status.toLowerCase() === 'active') {
        document.getElementById('editActive').checked = true;
    } else {
        document.getElementById('editInactive').checked = true;
    }

    // Set visibility checkboxes
    document.querySelectorAll('.edit-visibility-checkbox').forEach(cb => {
        cb.checked = kb.visibility.includes(cb.value);
    });

    // Set form action
    document.getElementById('editRoleForm').action = url;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editKnowledgeBase'));
    modal.show();

    // Clean up on modal hide
    document.getElementById('editKnowledgeBase').addEventListener('hidden.bs.modal', () => {
        editQuill.root.innerHTML = '';
        document.getElementById('editRoleForm').reset();
        document.getElementById('editContentInput').value = '';
        document.getElementById('editVisibilityError').classList.add('d-none');
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
    }, { once: true });
}

// Attach click event to edit buttons
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const kb = JSON.parse(btn.dataset.kb);
        const updateUrl = btn.dataset.updateUrl;
        openEditModal(kb, updateUrl);
    });
});

// Edit form submit validation
document.getElementById('editRoleForm').addEventListener('submit', function(e){
    const checked = document.querySelectorAll('.edit-visibility-checkbox:checked');
    const errorBox = document.getElementById('editVisibilityError');

    if(checked.length === 0){
        e.preventDefault();
        errorBox.classList.remove('d-none');
        return false;
    } else {
        errorBox.classList.add('d-none');
    }

    // Save editor content to hidden input
    document.getElementById('editContentInput').value = editQuill.root.innerHTML;
});

// Hide visibility error when checkbox changes
document.querySelectorAll('.edit-visibility-checkbox').forEach(cb => {
    cb.addEventListener('change', () => {
        document.getElementById('editVisibilityError').classList.add('d-none');
    });
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.delete-record').forEach(button => {
    button.addEventListener('click', function () {

      const id = this.dataset.id;

      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('delete-form-' + id).submit();
        }
      });

    });
  });

});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.viewBtn').forEach(button => {
        button.addEventListener('click', function () {

            const title = this.dataset.title;
            const content = JSON.parse(this.dataset.content); // parse JSON content

            document.getElementById('viewKnowledgeTitle').innerText = title;
            document.getElementById('viewKnowledgeContent').innerHTML = content; // renders HTML

        });
    });

});


</script>

<script>
  function downloadKnowledgeBaseManagerCSV() {
    window.location.href = "{{ route('manager.knowledge-base.export') }}";
}
</script>
@endpush





@endsection