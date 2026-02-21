@extends('layouts/layoutMaster')

@section('title', 'New-Interns')

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
<!-- Users List Table -->
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Interview Test</h4>
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
 
  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 my-2 justify-content-between">
     


        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <form method="GET" action="{{ route('active-admin') }}" id="filterForm" class="d-flex gap-2">
 <select name="per_page" id="dt-length-0" class="form-select ms-0"
                onchange="document.getElementById('perPageForm').submit()">
                <option>15</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
              </select>
              <!-- Keep search & status in query -->
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
         
<style>
  input[type="search"]::-webkit-search-cancel-button,
  input[type="search"]::-webkit-search-decoration {
      -webkit-appearance: none;
      appearance: none;
  }
</style>
             <select name="status" id="statusFilter" class="form-select text-capitalize">
              <option value="">Select Status</option>

              @foreach (['Technology','Interest','Interview'] as $status)
              @php $slug = strtolower($status); @endphp

              <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' : '' }}>
                {{ $status }}
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadActiveCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
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
                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">#</span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Email</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Interview Date</span><span
                    class="dt-column-order"></span></th>
               
                
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Technology</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Interview</span><span class="dt-column-order"></span></th>
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>


              </tr>
            </thead>
            <tbody>
              {{--@forelse ($active as $intern)--}}
              <tr class="">
                <td class="control dtr-hidden" tabindex="0" style="display: none;"></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"></span>1</td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"></span>John Doe</td>
                <td><span class="text-heading text-nowrap"><small></small>johndoe@example.com</span></td>
                <td><span class="text-heading text-nowrap"></span>30 Feb</td>
                <td><span class="text-heading text-nowrap"></span>Front-end Developer</td>
                <td><span class="text-heading text-nowrap"></span>Technical</td>
                                <td><span class="text-heading text-nowrap"></span>                
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Selected</a></li>                          
                                            <li><a class="dropdown-item" href="#">Rejected</a></li>

                                        </ul>
                                    </div>
</td>

                                {{--<td><span class="text-heading text-nowrap"></span>Completed</td>--


                {{--<td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  'interview' => 'bg-label-primary',
                  'contact' => 'bg-label-info',
                  'test' => 'bg-label-warning',
                  'completed' => 'bg-label-success',
                  'active' => 'bg-label-success',
                  'removed' => 'bg-label-danger',
                  ];

                  $status = strtolower($intern->status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                </td>--}}

                {{-- <td><span class="text-heading text-nowrap">{{$intern->intern_type}}</span></td> --}}
                {{--<td>
                  <div class="d-flex align-items-center">
                    <div class="dropdown">
                      <a href="javascript:;"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                      </a>

                     <div class="dropdown-menu dropdown-menu-end m-0">

                        <a href="{{route('view.profile.internee.admin', $intern->id)}}"
                          class="dropdown-item permission-btn">
                          View Profile
                        </a>
                        <a href="javascript:;" class="dropdown-item edit-intern" data-bs-toggle="modal"
                          data-bs-target="#editInternModal" data-id="{{ $intern->id }}" data-name="{{ $intern->name }}"
                          data-email="{{ $intern->email }}" data-technology="{{ $intern->technology }}"
                          data-status="{{ $intern->status }}">
                          Edit
                        </a>

                        @if (strtolower($intern->status) != 'removed')
                        <a href="javascript:;" class="dropdown-item permission-btn delete-record"
                          data-id="{{ $intern->id }}">
                          Remove
                        </a>
                        <form id="delete-form-{{ $intern->id }}" action="{{ route('interns.destroy', $intern->id) }}"
                          method="POST" style="display: none;">
                          @csrf
                          @method('DELETE')
                        </form>
                        @endif



                      </div>
                    </div>
                  </div>

                </td>--}}


              </tr>



              {{--@empty--}}
              {{--<tr><td colspan="11">
                <p class="text-center mb-0">No data available!</p></td></tr>--}}
              {{--@endforelse--}}







            </tbody>
            <tfoot></tfoot>
          </table>

{{-- Edit Interne - Start --}}
          <div class="modal fade" id="editInternModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
              <div class="modal-content p-2">
                <div class="modal-body">

                  <button type="button" class="btn-close"
                    style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    data-bs-dismiss="modal" aria-label="Close"></button>

                  <div class="text-start mb-6">
                    <h4 class="role-title">Edit Intern</h4>
                  </div>

                  <form id="editInternForm" novalidate class="row g-3" action="{{ route('update.intern.admin') }}"
                    method="POST">
                    @csrf

                    <input type="hidden" id="id" name="id">

                    <div class="col-6 mb-3">
                      <label class="form-label" for="name">Name</label>
                      <input type="text" id="name" name="name" class="form-control" />
                      <small class="text-danger error-name"></small>
                    </div>

                    <div class="col-6 mb-3">
                      <label class="form-label" for="email">Email</label>
                      <input type="email" id="email" name="email" class="form-control" required />
                      <small class="text-danger error-email"></small>
                    </div>

                    <div class="col-6 mb-3">
                      <label class="form-label" for="technology">Technology</label>
                      <input type="text" id="technology" name="technology" class="form-control" required />
                      <small class="text-danger error-technology"></small>
                    </div>

                    <div class="col-6 mb-3">
                      <label class="form-label" for="status">Status</label>
                      <select name="status" id="status" required class="form-select text-capitalize">
                        <option value="Interview">Interview</option>
                        <option value="Contact">Contact</option>
                        <option value="Test">Test</option>
                        <option value="Completed">Completed</option>
                        <option value="Active">Active</option>
                        <option value="Removed">Removed</option>
                      </select>
                      <small class="text-danger error-status"></small>
                    </div>

                    <div class="col-12 text-end">
                      <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Update Account</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>
          </div>
          {{-- Edit Interne - End --}}


        </div>
      </div>
      <div class="row mx-3 justify-content-between">
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
         {{-- <div class="dt-info" aria-live="polite">
            Showing {{ $active->firstItem() ?? 0 }} to {{ $active->lastItem() ?? 0 }} of {{ $active->total() ??
            0 }} entries
          </div>--}}
        </div>

        {{-- Pagination --}}
       {{-- <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                 First Page 
                <li class="dt-paging-button page-item {{ $active->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $active->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                Previous Page 
                <li class="dt-paging-button page-item {{ $active->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $active->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                Page Numbers 
                @foreach ($active->getUrlRange(max(1, $active->currentPage() - 2), min($active->lastPage(),
                $active->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $active->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                Next Page 
                <li
                  class="dt-paging-button page-item {{ $active->currentPage() == $active->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $active->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                Last Page 
                <li
                  class="dt-paging-button page-item {{ $active->currentPage() == $active->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $active->url($active->lastPage()) }}" aria-label="Last">
                    <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>--}}

    </div>
  </div>

</div>



@endsection