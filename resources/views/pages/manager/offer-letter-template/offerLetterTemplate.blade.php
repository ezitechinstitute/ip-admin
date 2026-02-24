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
    <h4 class="mt-6 mb-1">Offer Letter Management</h4>
</div>

{{-- Error Messages --}}
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="display:none;">
  Error message here
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

{{-- Success Message --}}
<div class="alert alert-success alert-dismissible fade show" role="alert" style="display:none;">
  Success message here
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<script>
  setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
          alert.classList.remove('show');
          alert.classList.add('hide');
          setTimeout(() => alert.remove(), 500);
      });
  }, 5000);
</script>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Generate Offer Letter</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">+ Create Template</button>
    </div>

    <div class="card-datatable">
        <div class="table-responsive" style="max-height:500px;">
            <table class="datatables-users table dataTable" style="width:100%">
  <thead class="border-top sticky-top bg-card">
              <tr>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">#</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Template Name
                  </span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">Technology</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Created AT</span><span
                    class="dt-column-order"></span></th>
              <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Action</span><span
                    class="dt-column-order"></span></th>
              



              </tr>
            </thead>
                <tbody>
                    <tr>
                                        <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"></span>1</td>
                         <td><span class="text-heading text-nowrap"><small></small>Front-End Internship</span></td>
                <td><span class="text-heading text-nowrap"></span>React</td>
                <td><span class="text-heading text-nowrap"></span>2026-03-15</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Generate PDF</a></li>
                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    {{--<tr>
                        <td colspan="5" class="text-center">No more templates available!</td>
                    </tr>--}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create Template Modal --}}
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-2">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <h4 class="mb-4">Create Offer Letter Template</h4>

                <form id="createTemplateForm" novalidate>
                    <div class="mb-3">
                        <label for="templateName" class="form-label">Template Name</label>
                        <input type="text" id="templateName" class="form-control" placeholder="Enter template name">
                    </div>

                    <div class="mb-3">
                        <label for="templateContent" class="form-label">Template Content</label>
                        <textarea id="templateContent" class="form-control" rows="8" placeholder="Use dynamic fields: {intern_name}, {technology}, {intern_id}, {join_date}, {end_date}, {duration}"></textarea>
                        <small class="text-muted">Dynamic fields will be auto-filled when generating offer letters.</small>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Template</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- Generate Offer Letter Modal --}}
<div class="modal fade" id="generateOfferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-2">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <h4 class="mb-4">Generate Offer Letter</h4>

                <form id="generateOfferForm" novalidate>
                    <div class="mb-3">
                        <label for="internName" class="form-label">Intern Name</label>
                        <input type="text" id="internName" class="form-control" placeholder="John Doe">
                    </div>

                    <div class="mb-3">
                        <label for="internTechnology" class="form-label">Internship Technology</label>
                        <input type="text" id="internTechnology" class="form-control" placeholder="Front-end Developer">
                    </div>

                    <div class="mb-3">
                        <label for="internID" class="form-label">Intern ID</label>
                        <input type="text" id="internID" class="form-control" placeholder="12345">
                    </div>

                    <div class="mb-3">
                        <label for="joinDate" class="form-label">Join Date</label>
                        <input type="date" id="joinDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <input type="text" id="duration" class="form-control" placeholder="3 Months">
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success">Generate PDF</button>
                        <button type="button" class="btn btn-info">Email to Candidate</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection