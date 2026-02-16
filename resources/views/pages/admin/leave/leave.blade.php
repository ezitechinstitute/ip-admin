  @extends('layouts/layoutMaster')

  @section('title', 'Leaves')

  @section('vendor-style')
  @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
  @endsection

  @section('vendor-script')
  @vite(['resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
  @endsection

  @section('page-script')
  {{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
  @endsection

  @section('content')

  <!-- Users List Table -->
  <div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Leaves</h4>
    {{-- <p class="mb-0">Find all of your company’s administrator accounts and their associate roles.</p> --}}
  </div>
  <div class="card">
    {{-- <div class="card-header border-bottom">
      <h5 class="card-title mb-0">Interview leavess
      </h5>

    </div> --}}
    
    <div class="card-datatable">
      <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
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
                <!-- Keep search & status in query -->
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
              </form>



              <label for="dt-length-0"></label>
            </div>
          </div>


          <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
    <form method="GET" action="{{ route('admin.leave') }}" id="filterForm" class="d-flex gap-2">
        <input type="hidden" name="per_page" value="{{ $perPage }}">

        <input type="search" name="search" id="searchInput" class="form-control" 
               placeholder="Search leave..." value="{{ request('search') }}">

        <input type="date" name="filter_date" id="filterDate" class="form-control" 
               value="{{ request('filter_date') }}">

        <select name="leave_type" id="leaveType" class="form-select">
            <option value="">All Leaves</option>
            <option value="intern" {{ request('leave_type')=='intern' ? 'selected' : '' }}>Intern</option>
            <option value="employee" {{ request('leave_type')=='employee' ? 'selected' : '' }}>Employee</option>
            <option value="supervisor" {{ request('leave_type')=='supervisor' ? 'selected' : '' }}>Supervisor</option>
        </select>

        @php
            $adminSettings = \App\Models\AdminSetting::first();
            $isAdminAllowed = !$adminSettings || (isset($adminSettings->export_permissions['admin']) && $adminSettings->export_permissions['admin'] == 1);
        @endphp

        @if($isAdminAllowed)
            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn add-new btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-upload icon-xs me-2"></i>
                    <span class="d-none d-sm-block">Export</span>
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    <a class="dropdown-item" href="javascript:void(0);" onclick="downloadLeavesCSV()">
                        <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>
        </div>
        <div class="justify-content-between dt-layout-table">
          <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto" style="max-height: 500px;">
            <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
              aria-describedby="DataTables_Table_0_info" style="width: 100%;">
              <colgroup>
                <col data-dt-column="1" style="width: 63.5375px;">
                <col data-dt-column="2" style="width: 326.863px;">
                <col data-dt-column="3" style="width: 170.125px;">
                <col data-dt-column="4" style="width: 131.825px;">
                <col data-dt-column="5" style="width: 212.475px;">
                <col data-dt-column="6" style="width: 126.662px;">
                <col data-dt-column="7" style="width: 181.312px;">
              </colgroup>




              <thead class="border-top sticky-top bg-card">
                <tr>
                  <th data-dt-column="0" class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1"
                    aria-label="" style="display: none;"><span class="dt-column-title"></span><span
                      class="dt-column-order"></span></th>

                  <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                    aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">ETI-ID
                      </span><span class="dt-column-order"></span></th>
                  
                      <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                    aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                      class="dt-column-order"></span></th>
                  <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                    aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Email</span><span
                      class="dt-column-order"></span></th>
                  <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                    aria-label="City" tabindex="0"><span class="dt-column-title" role="button">from_date</span><span
                      class="dt-column-order"></span></th>
                  {{-- <th data-dt-column="6" rowspan="1" colspan="1"
                    class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="leavesship Duration" tabindex="0">
                    <span class="dt-column-title" role="button">leavesship
                      Duration</span><span class="dt-column-order"></span></th> --}}
                  <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                    aria-label="Join Date"><span class="dt-column-title">to_date</span><span
                      class="dt-column-order"></span></th>
                  <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                    aria-label="Join Date"><span class="dt-column-title">Reason</span><span
                      class="dt-column-order"></span></th>

                    <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                    aria-label="Join Date"><span class="dt-column-title">Status</span><span
                      class="dt-column-order"></span></th>

                  <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                      class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



                </tr>
              </thead>
              <tbody>
                @forelse ($leave as $leaves) 
                <tr class="">
                  <td>
                      <span class="text-heading text-nowrap">{{$leaves->leave_id}}</span> 
                  
                  <td>
                      <span
                      class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$leaves->name}}</span> 

                  </td>

                  <td>
                      <span class="text-heading text-nowrap"><small>{{$leaves->email}}</small></span>
                  </td>

                  
                  
                  <td>
                    <span class="text-heading text-nowrap">{{$leaves->from_date}}</span>
                  </td>

                  <td>
                      <span class="text-heading text-nowrap">{{$leaves->to_date}}</span>
                  </td>
                

                  <td>
                    <span class="text-heading text-nowrap">{{$leaves->reason}}</span>
                  </td>

                  
                <td>
                    @php
                    // Map statuses to Bootstrap badge classes
                    $statusClasses = [
                    '1' => 'bg-label-success',
                    '0' => 'bg-label-danger',
                    ];

                    $status = strtolower($leaves->leave_status); // ensure lowercase
                    $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                    @endphp

                    <span class="badge {{ $badgeClass }} text-capitalize">@if ($leaves->leave_status == 1)
                      Approved
                      @else
                      Rejected
                      @endif</span>
                  </td>
  <td>
  <div class="d-flex align-items-center">
    <div class="dropdown">

      <!-- Three dots ALWAYS visible -->
      <a href="javascript:;"
        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
      </a>

      <ul class="dropdown-menu p-2">

        @if($leaves->leave_status == 1)
          <!-- If APPROVED → show ONLY Reject -->
          <li>
            <form 
              action="{{ 
                $leaves->source == 'employee' 
                  ? route('employee.leave.reject', $leaves->leave_id)
                  : ($leaves->source == 'supervisor'
                      ? route('supervisor.leave.reject', $leaves->leave_id)
                      : route('intern.leave.reject', $leaves->leave_id))
              }}" 
              method="POST">
              @csrf
              <button class="btn btn-danger w-100" type="submit">Reject</button>
            </form>
          </li>

        @else
          <!-- If REJECTED (or anything else) → show ONLY Approve -->
          <li>
            <form 
              action="{{ 
                $leaves->source == 'employee' 
                  ? route('employee.leave.approve', $leaves->leave_id)
                  : ($leaves->source == 'supervisor'
                      ? route('supervisor.leave.approve', $leaves->leave_id)
                      : route('intern.leave.approve', $leaves->leave_id))
              }}" 
              method="POST">
              @csrf
              <button class="btn btn-success w-100" type="submit">Approve</button>
            </form>
          </li>
        @endif

      </ul>
    </div>
  </div>
  </td>




                    
                    <div class="d-flex align-items-center">
                      {{-- <a href="javascript:;"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-record"
                        data-id="{{ $leaves->id }}">
                        <i class="icon-base ti tabler-trash icon-22px"></i>
                      </a>
                      <form id="delete-form-{{ $leaves->id }}" action="{{ route('leavess.destroy', $leaves->int_id) }}"
                        method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                      </form> --}}


                      {{-- <a href="javascript:void(0);"
    class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-leaves"
    data-bs-toggle="modal"
    data-bs-target="#editleavesModal"
    data-id="{{ $leaves->int_id }}"
    data-name="{{ $leaves->name }}"
    data-email="{{ $leaves->email }}"
    data-technology="{{ $leaves->int_technology }}"
    data-status="{{ strtolower($leaves->int_status) }}">
    <i class="icon-base ti tabler-edit icon-22px"></i>
  </a> --}}






  {{-- Edit leavese Accoutn - Start --}}
  {{-- <div class="modal fade" id="editleavesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
      <div class="modal-content p-2">
        <div class="modal-body">
          <button type="button" class="btn-close" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="text-start mb-6">
            <h4 class="role-title">Edit leaves Account</h4>
          </div>
          
          <form id="editleavesForm" class="row g-3" action="{{route('update-leaves-account')}}" method="POST">
            @csrf
            <input type="hidden" id="int_id" name="int_id">
            
            <div class="col-6 mb-3">
              <label class="form-label" for="name">Name</label>
              <input type="text" id="name" name="name" class="form-control" required/>
            </div>
            
            <div class="col-6 mb-3">
              <label class="form-label" for="email">Email</label>
              <input type="email" id="email" name="email" class="form-control" required/>
            </div>
            
            <div class="col-6 mb-3">
              <label class="form-label" for="int_technology">Technology</label>
              <input type="text" id="int_technology" name="int_technology" class="form-control" required/>
            </div>
            
            <div class="col-6 mb-3">
              <label class="form-label" for="int_status">Status</label>
              <select name="int_status" id="int_status" required class="form-select text-capitalize">
                <option value="active">Active</option>
                <option value="test">Test</option>
                <option value="freeze">Freeze</option>
              </select>
            </div>
          
            <div class="col-12 text-end">
              <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Account</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> --}}
  {{-- Edit leavese Accoutn - End --}}










                      {{-- <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/user/view/account"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon">
                        <i class="icon-base ti tabler-eye icon-22px"></i>
                      </a> --}}
                    </div>

                  </td>


                </tr>



                @empty
                <tr>
                  <td colspan="11">
                    <p class="text-center mb-0">No data available!</p>
                  </td>
                </tr>
                @endforelse 







              </tbody>
              <tfoot></tfoot>
            </table>




          </div>
        </div>
        <div class="row mx-3 justify-content-between">
          {{-- Info --}}
          {{-- <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
            <div class="dt-info" aria-live="polite">
              Showing {{ $leavesAccounts->firstItem() ?? 0 }} to {{ $leavesAccounts->lastItem() ?? 0 }} of {{ $leavesAccounts->total() ??
              0 }} entries
            </div>
          </div> --}}

          {{-- Pagination --}}
          <div
            class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
            <div class="dt-paging">
              <nav aria-label="pagination">
                <ul class="pagination">
                  {{-- First Page --}}
                  {{-- <li class="dt-paging-button page-item {{ $leavesAccounts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $leavesAccounts->url(1) }}" aria-label="First">
                      <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                    </a>
                  </li> --}}

                  {{-- Previous Page --}}
                  {{-- <li class="dt-paging-button page-item {{ $leavesAccounts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $leavesAccounts->previousPageUrl() }}" aria-label="Previous">
                      <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                    </a>
                  </li> --}}

                  {{-- Page Numbers --}}
                  {{-- @foreach ($leavesAccounts->getUrlRange(max(1, $leavesAccounts->currentPage() - 2), min($leavesAccounts->lastPage(),
                  $leavesAccounts->currentPage() + 2)) as $page => $url) --}}
                  {{-- <li class="dt-paging-button page-item {{ $page == $leavesAccounts->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                  </li> --}}
                  {{-- @endforeach --}}

                  {{-- Next Page --}}
                  {{-- <li
                    class="dt-paging-button page-item {{ $leavesAccounts->currentPage() == $leavesAccounts->lastPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $leavesAccounts->nextPageUrl() }}" aria-label="Next">
                      <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                    </a>
                  </li> --}}

                  {{-- Last Page --}}
                  {{-- <li
                    class="dt-paging-button page-item {{ $leavesAccounts->currentPage() == $leavesAccounts->lastPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $leavesAccounts->url($leavesAccounts->lastPage()) }}" aria-label="Last">
                      <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                    </a>
                  </li> --}}
                </ul>
              </nav>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
  <script>
  document.addEventListener("DOMContentLoaded", function () {

      // Auto-submit only for dropdowns (not for search or date typing)
      document.querySelectorAll("#filterForm select, #filterForm input[type='date']").forEach(el => {
          el.addEventListener("change", function () {
              document.getElementById("filterForm").submit();
          });
      });

  });
  </script>


  @push('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function () {
      const editModal = document.getElementById('editleavesModal');
      
      if (editModal) {
          editModal.addEventListener('show.bs.modal', function (event) {
              // Button that triggered the modal
              const button = event.relatedTarget;
              
              // Extract info from data-* attributes
              const id = button.getAttribute('data-id');
              const name = button.getAttribute('data-name');
              const email = button.getAttribute('data-email');
              const technology = button.getAttribute('data-technology');
              const status = button.getAttribute('data-status');

              // Update the modal's content
              const modalBody = editModal.querySelector('.modal-body');
              
              modalBody.querySelector('#int_id').value = id;
              modalBody.querySelector('#name').value = name;
              modalBody.querySelector('#email').value = email;
              modalBody.querySelector('#int_technology').value = technology;
              
              // Set the dropdown value
              const statusSelect = modalBody.querySelector('#int_status');
              if (statusSelect) {
                  statusSelect.value = status;
              }
          });
      }
  });
  </script>
  @endpush


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








  @endsection