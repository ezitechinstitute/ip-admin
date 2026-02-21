@extends('layouts/layoutMaster')

@section('title', 'Remaining Balance')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@vite(['resources/assets/js/forms-pickers.js'])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="col-xl-12 col-md-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Payment Statistics</h5>
      </div>

      <div class="card-body d-flex align-items-end">
        <div class="w-100">
          <div class="row gy-3">

            <!-- Total Amount -->
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-primary me-4 p-2">
                  <i class="icon-base ti tabler-currency-dollar icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">6000</h5>
                  <small>Total Amount</small>
                </div>
              </div>
            </div>

            <!-- Payable Amount -->
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-success me-4 p-2">
                  <i class="icon-base ti tabler-cash icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">3000</h5>
                  <small>Payable Amount</small>
                </div>
              </div>
            </div>

            <!-- Remaining Amount -->
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-danger me-4 p-2">
                  <i class="icon-base ti tabler-alert-circle icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">3000</h5>
                  <small>Remaining Amount</small>
                </div>
              </div>
            </div>

            <!-- Next Due Date -->
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-info me-4 p-2">
                  <i class="icon-base ti tabler-calendar icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">15 March</h5>
                  <small>Next Due Date</small>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>  
<div class="col-12 mb-4">
    <h4 class="mt-4 mb-3">Remaining Balance</h4>
</div>
<div class="card">   
  <div class="card-datatable">
    <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center  dt-layout-full table-responsive overflow-auto"
          style="max-height: 600px;">
          <table class="datatables-users table border-start border-end " id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">
            <thead class="border-top sticky-top bg-card">
              <tr>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">#</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Paid Amount
                  </span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">Remaining Balance</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Next Due Date</span><span
                    class="dt-column-order"></span></th>
              <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Status</span><span
                    class="dt-column-order"></span></th>
              



              </tr>
            </thead>
            <tbody>
              {{--@forelse ($active as $intern)--}}
              <tr class="">

                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"></span>1</td>
                <td><span class="text-heading text-nowrap"><small></small>6000</span></td>
                <td><span class="text-heading text-nowrap"></span>3000</td>
                <td><span class="text-heading text-nowrap"></span>2026-03-15</td>
                <td><span class="text-heading text-nowrap badge bg-warning"></span> Pending</td>
              </tr>
              {{--@empty
              <tr><td colspan="11">
                <p class="text-center mb-0">No data available!</p></td></tr>
              @endforelse--}}







            </tbody>
            <tfoot></tfoot>
          </table>
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
          
          
@endsection

@section('vendor-script')
<script>
    // Calculate Remaining Amount automatically
    document.getElementById('calculateBtn').addEventListener('click', function() {
        const total = parseFloat(document.getElementById('totalAmount').value) || 0;
        const paid = parseFloat(document.getElementById('payableAmount').value) || 0;
        const remaining = total - paid;
        document.getElementById('remainingAmount').value = remaining >= 0 ? remaining : 0;
    });

    // Auto-calculate on page load
    window.addEventListener('load', function() {
        document.getElementById('calculateBtn').click();
    });
</script>
@endsection