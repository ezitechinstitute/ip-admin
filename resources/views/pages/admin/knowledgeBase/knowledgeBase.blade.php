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
  <h4 class="mt-6 mb-1">Knowledge Base</h4>
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

        <form method="GET" action="{{ route('knowledge.base.admin') }}" id="filterForm"
          class="d-flex flex-wrap gap-2 justify-content-between w-100">

          {{-- LEFT SIDE --}}
          <div class="d-flex gap-2">

            {{-- Per Page --}}
            <select name="per_page" class="form-select" onchange="document.getElementById('filterForm').submit()">
    <option value="15" {{ (isset($perPage) ? $perPage : request('per_page')) == 15 ? 'selected' : '' }}>15</option>
    <option value="25" {{ (isset($perPage) ? $perPage : request('per_page')) == 25 ? 'selected' : '' }}>25</option>
    <option value="50" {{ (isset($perPage) ? $perPage : request('per_page')) == 50 ? 'selected' : '' }}>50</option>
    <option value="100" {{ (isset($perPage) ? $perPage : request('per_page')) == 100 ? 'selected' : '' }}>100</option>
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
            {{-- Status --}}
            <select name="status" id="statusFilter" class="form-select text-capitalize">

              <option value="">Select Status</option>

              @foreach(['Active','Inactive'] as $st)
              @php $slug = strtolower($st); @endphp
              <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' :'' }}>
                {{ $st }}
              </option>
              @endforeach

            </select>
          @php
            $adminSettings = \App\Models\AdminSetting::first();

            if (!$adminSettings) {
            $isAdminAllowed = true;
            } else {
            $permissions = $adminSettings->export_permissions;
            $isAdminAllowed = isset($permissions['admin']) && $permissions['admin'] == 1;
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadKnowledgeBaseCSV()">
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
        <button class="btn add-new btn-primary rounded-2 waves-effect px-7 waves-light text-nowrap" tabindex="0"
          aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
          data-bs-target="#addKnowledgeBase"><span><i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span
              class="d-none d-sm-inline-block">Add
              KB</span></span></button>
        <!-- Add Knowledge Base -->
        <div class="modal fade" id="addKnowledgeBase" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-2">
              <div class="modal-body">
                <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                  class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-start mb-6">
                  <h4 class="role-title">Add Knowledge Base</h4>
                </div>
                <form id="addRoleForm" class="row g-3" method="POST" action="{{route('knowledge.store.admin')}}">
                  @csrf

                  {{-- Title --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter Title..." required />
                  </div>

                  {{-- Category --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select" required>
                      <option value="">Select Category</option>
                      <option value="Rules">Rules</option>
                      <option value="Policy">Policy</option>
                      <option value="Agreement">Agreement</option>
                      <option value="Guidelines">Guidelines</option>
                    </select>
                  </div>

                  {{-- Visibility --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Visibility</label>

                    <div class="d-flex gap-4">

                      <div class="form-check">
                        <input class="form-check-input visibility-checkbox" type="checkbox" name="visibility[]"
                          value="manager">
                        <label class="form-check-label">Managers</label>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input visibility-checkbox" type="checkbox" name="visibility[]"
                          value="supervisor">
                        <label class="form-check-label">Supervisors</label>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input visibility-checkbox" type="checkbox" name="visibility[]"
                          value="intern">
                        <label class="form-check-label">Interns</label>
                      </div>

                    </div>

                    <div class="text-danger small d-none" id="visibilityError">
                      Please select at least one visibility option
                    </div>

                  </div>

                  {{-- Status --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Status</label>

                    <div class="d-flex gap-4">

                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="active" required checked>
                        <label class="form-check-label">Active</label>
                      </div>

                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" value="inactive" required>
                        <label class="form-check-label">Inactive</label>
                      </div>

                    </div>
                  </div>

                  {{-- Text Editor --}}
                  <div class="col-12 mb-3">
                    <label class="form-label">Content</label>

                    <div id="editor" style="height:200px;"></div>
                    <input type="hidden" name="content" id="contentInput">
                  </div>

                  {{-- Buttons --}}
                  <div class="col-12 text-end mt-3">
                    <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">
                      Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                      Create
                    </button>
                  </div>

                </form>

              </div>
            </div>
          </div>
        </div>
        <!-- Add Knowledge Base -->





        <!-- Edit Knowledge Base -->
        <div class="modal fade" id="editKnowledgeBase" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
            <div class="modal-content p-2">
              <div class="modal-body">
                <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                  class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-start mb-6">
                  <h4 class="role-title">Edit Knowledge Base</h4>
                </div>
                <form id="editRoleForm" class="row g-3" method="POST" action="">
                  @csrf
                  @method('PUT')

                  {{-- Title --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="editTitle" class="form-control" placeholder="Enter Title..."
                      required />
                  </div>

                  {{-- Category --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" id="editCategory" class="form-select" required>
                      <option value="">Select Category</option>
                      <option value="Rules">Rules</option>
                      <option value="Policy">Policy</option>
                      <option value="Agreement">Agreement</option>
                      <option value="Guidelines">Guidelines</option>
                    </select>
                  </div>

                  {{-- Visibility --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Visibility</label>
                    <div class="d-flex gap-4">
                      <div class="form-check">
                        <input class="form-check-input edit-visibility-checkbox" type="checkbox" name="visibility[]"
                          value="manager">
                        <label class="form-check-label">Managers</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input edit-visibility-checkbox" type="checkbox" name="visibility[]"
                          value="supervisor">
                        <label class="form-check-label">Supervisors</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input edit-visibility-checkbox" type="checkbox" name="visibility[]"
                          value="intern">
                        <label class="form-check-label">Interns</label>
                      </div>
                    </div>
                    <div class="text-danger small d-none" id="editVisibilityError">
                      Please select at least one visibility option
                    </div>
                  </div>

                  {{-- Status --}}
                  <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="d-flex gap-4">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editActive" value="active"
                          required>
                        <label class="form-check-label">Active</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" id="editInactive" value="inactive"
                          required>
                        <label class="form-check-label">Inactive</label>
                      </div>
                    </div>
                  </div>

                  {{-- Text Editor --}}
                  <div class="col-12 mb-3">
                    <label class="form-label">Content</label>
                    <div id="editEditor" style="height:200px;"></div>
                    <input type="hidden" name="content" id="editContentInput">
                  </div>

                  {{-- Buttons --}}
                  <div class="col-12 text-end mt-3">
                    <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">
                      Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                      Update
                    </button>
                  </div>

                </form>
              </div>
            </div>
          </div>
        </div>


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
@forelse ($knowledge as $kb)
    @php
$statusClasses = [
    'active' => 'bg-label-success',
    'inactive' => 'bg-label-danger',
];
$status = strtolower($kb->status);
$badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';

// Decode visibility JSON if it's a string, else leave as is
$visibilityArray = is_string($kb->visibility) ? json_decode($kb->visibility, true) : (array)$kb->visibility;

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
        <td>{{ $loop->iteration + ($knowledge->currentPage()-1)*$knowledge->perPage() }}</td>
        <td>{{ $kb->title }}</td>
        <td>{{ $kb->category }}</td>
        <td><span class="badge {{ $badgeClass }}">{{ $status }}</span></td>
       <td class="d-flex gap-3">
       <a href="javascript:void(0);"
   class="viewBtn"
   data-bs-toggle="modal"
   data-bs-target="#viewKnowledgeModal"
   data-title="{{ $kb->title }}"
   data-content='@json($kb->content)'>
   <i class="icon-base ti tabler-eye text-success icon-22px"></i>
</a>

   <a href="javascript:void(0);"
   class="editBtn"
   data-bs-toggle="modal"
   data-bs-target="#editKnowledgeBase"
   data-id="{{ $kb->id }}"
   data-kb='@json($kbData, JSON_HEX_APOS|JSON_HEX_QUOT)'
   data-update-url="{{ route('knowledge.update.admin', ['id' => $kb->id]) }}">
   <i class="icon-base ti tabler-edit icon-22px"></i>
</a>
  <!-- Delete Button -->
    <a href="javascript:void(0);"
       class="delete-record"
       data-id="{{ $kb->id }}">
       <i class="icon-base ti tabler-trash text-danger icon-22px"></i>
    </a>

    <!-- Hidden Delete Form -->
    <form id="delete-form-{{ $kb->id }}" action="{{ route('knowledge.delete.admin', $kb->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

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
            Showing {{ $knowledge->firstItem() ?? 0 }} to {{ $knowledge->lastItem() ?? 0 }} of {{
            $knowledge->total() ??
            0 }} entries
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">

                <li class="dt-paging-button page-item {{ $knowledge->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $knowledge->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li class="dt-paging-button page-item {{ $knowledge->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $knowledge->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                @foreach ($knowledge->getUrlRange(max(1, $knowledge->currentPage() - 2),
                min($knowledge->lastPage(),
                $knowledge->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $knowledge->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach


                <li
                  class="dt-paging-button page-item {{ $knowledge->currentPage() == $knowledge->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $knowledge->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li
                  class="dt-paging-button page-item {{ $knowledge->currentPage() == $knowledge->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $knowledge->url($knowledge->lastPage()) }}" aria-label="Last">
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
function downloadKnowledgeBaseCSV() {
    window.location.href = "{{ route('knowledge.base.export.admin') }}";
}
</script>
@endpush





@endsection