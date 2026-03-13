@extends('layouts/layoutMaster')

@section('title', 'Offer-Letter-Request')

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
  <h4 class="mt-6 mb-1">Supervisor</h4>
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

  <div class="card-datatable">
    
      <div >
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          

            {{-- <form id="perPageForm" method="GET">
              <select name="perpage" class="form-select" onchange="this.form.submit()">
                @foreach([15, 25, 50, 100] as $val)
                <option value="{{ $val }}" {{ request('perpage', 15)==$val ? 'selected' : '' }}>
                  {{ $val }}
                </option>
                @endforeach
              </select>
              </select>
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
              <input type="hidden" name="intern_type" value="{{ request('intern_type') }}">
            </form> --}}



            <label for="dt-length-0"></label>
          </div>
        </div>


        <div class="d-md-flex align-items-center col-md-auto ms-auto d-flex gap-2 flex-wrap" class="form-control">

          <form method="GET"  id="filterForm"
            class="d-flex align-items-center gap-2">

            <!-- Search -->
            {{-- <input type="search" name="search" id="searchInput" class="form-control"
              style="width: 200px;" placeholder="Search..." value="{{ request('search') }}"> --}}

            <!-- Status Filter -->
            {{-- <select name="status" id="statusFilter" class="form-select" onchange="this.form.submit()">
              <option value="">Status</option>
              <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
              <option value="accept" {{ request('status')=='accept' ? 'selected' : '' }}>Accept</option>
            </select> --}}

            <!-- Keep Perpage -->
            <input type="hidden" name="perpage" value="{{ request('perpage', 15) }}">

          </form>
          {{-- @php
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
            @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'excel_manager_offer_letter_request'))
            <button id="btnGroupDrop1" type="button" class="btn add-new btn-outline-primary dropdown-toggle"
              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="icon-base ti tabler-dots-vertical icon-md d-sm-none"></i>
              <i class="icon-base ti tabler-upload icon-xs me-2"></i>
              <span class="d-none d-sm-block">Export</span>
            </button>
            @endif
            <div class="dropdown-menu" style="z-index: 1021" aria-labelledby="btnGroupDrop1">
              <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadOfferLetterCSV()">
                <span>
                  <span class="d-flex align-items-center">
                    <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                  </span>
                </span>
              </a>
            </div>
          </div>
          @endif --}}
        </div>









        </form>
      </div>
    </div>

    <div class="justify-content-between dt-layout-table">
      <div class="table-responsive overflow-auto" style="max-height: 700px;">
        <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
          aria-describedby="DataTables_Table_0_info" style="width: 100%;">

          <thead class="border-top sticky-top bg-card">
            <tr>
              <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">ETI‑ID</span><span
                  class="dt-column-order"></span></th>
              <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                  class="dt-column-order"></span></th>
              <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Join date</span><span
                  class="dt-column-order"></span></th>
              <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Commission</span><span
                  class="dt-column-order"></span></th>
              <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Status"><span class="dt-column-title" role="button">Status</span><span class="dt-column-order"></span></th>
              <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Action"><span class="dt-column-title" role="button">Action</span><span class="dt-column-order"></span></th>          
              {{-- <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="City" tabindex="0"><span class="dt-column-title" role="button">REASON</span><span
                  class="dt-column-order"></span></th> --}}
              
              {{-- <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                aria-label="City" tabindex="0"><span class="dt-column-title" role="button">CREATED AT</span><span
                  class="dt-column-order"></span></th> --}}



              

            </tr>
          </thead>
          <tbody>
            @forelse ($supervisors as $Sup)
            <tr class="">
              <td><span
                  class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->eti_id}}</span>
              </td>


              <td><span
                  class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->name}}</span>
              </td>

              <td><span
                  class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->join_date}}</span>
              </td>

              <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ $Sup->comission }}</span></td>
              <td>
                @php
                  $statusClasses = ['1' => 'bg-label-success', '0' => 'bg-label-danger'];
                  $badgeClass = $statusClasses[$Sup->status] ?? 'bg-label-secondary';
                @endphp
                <span class="badge {{ $badgeClass }} text-capitalize">{{ $Sup->status ? 'Active' : 'Frozen' }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="dropdown">
                    <a href="javascript:;" class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end m-0">
                      <a href="{{ route('manager.supervisor.show', $Sup->manager_id) }}" class="dropdown-item">Details</a>
                      <a href="{{ route('manager.supervisor.activityLog', $Sup->manager_id) }}" class="dropdown-item">Activity Log</a>
                      <form action="{{ route('manager.supervisor.toggleFreeze', $Sup->manager_id) }}" method="POST" class="m-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="dropdown-item">{{ $Sup->status ? 'Freeze' : 'Unfreeze' }}</button>
                      </form>
                    </div>
                  </div>
                </div>
              </td>
          
                {{-- @php
                $statusClasses = [
                'ongoing' => 'bg-label-primary',
                'contact' => 'bg-label-info',
                'expired' => 'bg-label-danger',
                'completed' => 'bg-label-success',
                'active' => 'bg-label-success',
                'removed' => 'bg-label-danger',
                'freeze' => 'bg-label-danger',
                'submitted' => 'bg-label-warning',
                'approved' => 'bg-label-success',
                'rejected' => 'bg-label-danger',
                ];

                $status = strtolower($offerletter->intern_status);
                $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                @endphp

                <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
              </td>
              <td><span class="text-heading text-nowrap"><small><i <td><span
                        class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$offerletter->tech}}</span>
              </td> --}}


              {{-- <td><span class="text-heading text-nowrap"><small><i></i>{{$offerletter->reason}}</small></span></td>
              --}}

              {{-- <td>
                @php
                $statusClasses = [
                'ongoing' => 'bg-label-primary',
                'contact' => 'bg-label-info',
                'pending' => 'bg-label-warning',
                'accept' => 'bg-label-success',
                'active' => 'bg-label-success',
                'removed' => 'bg-label-danger',
                'freeze' => 'bg-label-danger',
                'submitted' => 'bg-label-warning',
                'approved' => 'bg-label-success',
                'reject' => 'bg-label-danger',
                ];

                $status = strtolower($offerletter->status);
                $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                @endphp

                <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
              </td> --}}

              {{-- <td>
                @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'accept_manager_offer_letter_request') || $manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'reject_manager_offer_letter_request') || $manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'offer_letter_send_manager_offer_letter_request') || $manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'view_reason_manager_offer_letter_request'))

                <div class="dropdown">
                  <a href="javascript:;"
                    class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                  </a>

                  <div class="dropdown-menu dropdown-menu-end m-0">



                    @if ($offerletter->status != 'accept' && $offerletter->status != 'reject')
                    @if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'accept_manager_offer_letter_request'))
                       <a href="javascript:void(0);" 
   class="dropdown-item status-update-btn" 
   data-id="{{ $offerletter->offer_letter_id }}" 
   data-status="accept"
   data-name="{{ $offerletter->username }}">
   Accept
</a>
@endif

@if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'reject_manager_offer_letter_request'))
<a href="javascript:void(0);" 
   class="dropdown-item status-update-btn" 
   data-id="{{ $offerletter->offer_letter_id }}" 
   data-status="reject"
   data-name="{{ $offerletter->username }}">
   Reject
</a>
@endif
                    @endif
                  

<form id="statusUpdateForm" action="{{ route('manager.offer-letter.update-status') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="id" id="update_id">
    <input type="hidden" name="status" id="update_status">
</form>

@if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'offer_letter_send_manager_offer_letter_request'))
                    <a href="javascript:;" 
   class="dropdown-item offer-letter-send" 
   data-bs-toggle="modal"
   data-bs-target="#supervisorsendModal" 
   data-id="{{ $offerletter->id }}"> 
   Offer Letter Send
</a>
@endif

@if($manager && \Illuminate\Support\Facades\Gate::forUser($manager)->allows('check-privilege',
  'view_reason_manager_offer_letter_request'))

                    <a href="javascript:void(0);" class="dropdown-item view-reason-btn" data-bs-toggle="modal"
                      data-bs-target="#viewReasonModal" data-name="{{ $offerletter->username }}"
                      data-reason="{{ $offerletter->reason ?? 'No reason provided.' }}">
                      View Reason
                    </a>
                    @endif



                  



                  </div>
                </div>
@endif
              </td>



            </tr> --}}



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
  {{-- <div class="modal fade" id="viewReasonModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Request Reason</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label fw-bold">Intern Name:</label>
                              <p id="modalUserName" class="text-muted"></p>
                            </div>
                            <hr>
                            <div class="mb-3">
                              <label class="form-label fw-bold">Reason for Request:</label>
                              <div id="modalReasonText" class="p-3 bg-light border rounded"
                                style="white-space: pre-wrap;"></div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                        </div>
                      </div>
                    </div> --}}


        {{-- Offer Letter Send Modal --}}
        {{-- <div class="modal fade" id="supervisorsendModal" tabindex="-1" aria-hidden="true"
          style="z-index: 9999 !important;">
          <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
            <div class="modal-content p-2">
              <div class="modal-body">
                <button type="button" class="btn-close"
                  style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" data-bs-dismiss="modal"
                  aria-label="Close"></button>
                <div class="text-start mb-6">
                  <h4 class="role-title">Send Offer Letter</h4>
                </div> --}}

                {{-- <form id="offerLetterForm" action="{{ route('send.offer.letter') }}" method="POST">
    @csrf 
    <input type="hidden" id="offer_intern_id" name="intern_id">

    <div class="col-12 mb-3">
        <label class="form-label" for="template_select">Select Offer Letter Template</label>
        <select name="template_id" id="template_select" required class="form-select">
            <option value="">Select a Template</option>
            @foreach($templates as $template)
                <option value="{{ $template->id }}">{{ $template->title }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <h6>Preview Template</h6>
        <div id="template_preview_area" class="border p-3 text-center content-center rounded" style="min-height: 150px; display: flex; justify-content: center; align-items: center; border: 1px dotted blue !important; background-color: #f6f9fc; max-height: 1000px; overflow-y: auto;">
            <p class="text-center text-primary">Please select a template to see preview</p>
        </div>
    </div>

    <div class="col-12 text-end mt-4">
        <button type="button" id="downloadPdfBtn" class="btn btn-outline-success btn-sm me-2"><i class="menu-icon icon-base ti tabler-file-download"></i> Download PDF</button>
        <span>Or</span>
        <button type="submit" class="btn btn-sm btn-primary ms-2"><i class="menu-icon icon-base ti tabler-mail-forward"></i> Send Email</button>
    </div>
</form>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div> --}}
    <div class="row mx-3 justify-content-between"> 
      
    <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
        <div class="dt-info" aria-live="polite">
          Showing {{ $supervisors->firstItem() ?? 0 }} to {{ $supervisors->lastItem() ?? 0 }} of {{
          $supervisors->total() ??
          0 }} entries
        </div>
    </div> 

      {{-- Pagination --}}
       <div
        class="d-md-flex align-items-center dt-layout-end mt-4 col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
        <div class="dt-paging">
          <nav aria-label="pagination">
            <ul class="pagination">
              {{-- First Page --}}
               <li class="dt-paging-button page-item {{ $supervisors->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->url(1) }}" aria-label="First">
                  <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                </a>
              </li> 

              {{-- Previous Page --}}
              <li class="dt-paging-button page-item {{ $supervisors->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->previousPageUrl() }}"
                  aria-label="Previous">
                  <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                </a>
              </li>

              {{-- Page Numbers --}}
              @foreach ($supervisors->getUrlRange(max(1, $supervisors->currentPage() - 2),
              min($supervisors->lastPage(),
              $supervisors->currentPage() + 2)) as $page => $url)
              <li class="dt-paging-button page-item {{ $page == $supervisors->currentPage() ? 'active' : '' }}">
                <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
              </li>
              @endforeach

              {{-- Next Page --}}
              <li
                class="dt-paging-button page-item {{ $supervisors->currentPage() == $supervisors->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->nextPageUrl() }}"
                  aria-label="Next">
                  <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                </a>
              </li>

              {{-- Last Page --}}
              <li
                class="dt-paging-button page-item {{ $supervisors->currentPage() == $supervisors->lastPage() ? 'disabled' : '' }}">
                <a class="page-link" style="border-radius: 5px;"
                  href="{{ $supervisors->url($supervisors->lastPage()) }}" aria-label="Last">
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

{{-- <script>
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
</script> --}}

{{-- @push('scripts')
<script>
  function downloadOfferLetterCSV() {
    // English: Get all current URL parameters (search, perpage, etc.)
    const urlParams = new URLSearchParams(window.location.search);
    
    // English: Define the base export route
    const baseRoute = "{{ route('manager.offer-letter.export') }}";
    
    // English: Combine route with current filters and redirect for download
    const finalUrl = baseRoute + '?' + urlParams.toString();
    
    // English: Redirect to trigger the streamDownload controller
    window.location.href = finalUrl;
}
</script>
@endpush --}}


@push('scripts')

<script>
  /**
 * English: Listens for clicks on 'View Reason' buttons 
 * and populates the modal with the specific row's data.
 */
// document.querySelectorAll('.view-reason-btn').forEach(button => {
//     button.addEventListener('click', function() {
//         // English: Get data from the clicked button's attributes
//         const userName = this.getAttribute('data-name');
//         const reason = this.getAttribute('data-reason');

//         // English: Inject data into the modal elements
//         document.getElementById('modalUserName').innerText = userName;
//         document.getElementById('modalReasonText').innerText = reason;
//     });
});
</script>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('click', function (event) {
    if (event.target.classList.contains('status-update-btn')) {
        const id = event.target.getAttribute('data-id');
        const status = event.target.getAttribute('data-status');
        const name = event.target.getAttribute('data-name');
        
        const isAccept = (status === 'accept');

        Swal.fire({
            title: isAccept ? 'Are you sure?' : 'Reject Request?',
            text: `You want to ${status} the offer letter request for ${name}.`,
            icon: isAccept ? 'success' : 'warning',
            showCancelButton: true,
            confirmButtonColor: isAccept ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6e7881',
            confirmButtonText: isAccept ? 'Yes, Accept it!' : 'Yes, Reject it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // English: Fill the hidden form and submit
                document.getElementById('update_id').value = id;
                document.getElementById('update_status').value = status;
                document.getElementById('statusUpdateForm').submit();
            }
        });
    }
});
</script> --}}
<style>
  /* English comments: Force the SweetAlert2 container to be on top of everything */
  .swal2-container {
    z-index: 9999 !important;
  }
</style>






@endpush

@endsection