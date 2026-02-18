<<<<<<< Updated upstream
@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Manager Dashboard')

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
'resources/assets/vendor/libs/swiper/swiper.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/fonts/flag-icons.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss'
])
@endsection
@section('page-style')
@vite('resources/assets/vendor/scss/pages/cards-advance.scss')
@endsection
@section('vendor-script')
@vite([
'resources/assets/vendor/libs/apex-charts/apexcharts.js',
'resources/assets/vendor/libs/swiper/swiper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@vite(['resources/assets/js/forms-pickers.js'])
@endsection

@section('content')
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
<div class="row g-6">

  <div class="col-xl-4">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-7">
          <div class="card-body text-nowrap">
            <h5 class="card-title mb-0">Congratulations 🎉 Kashif Saeed !</h5>
            <p class="mb-2 text-success">You have earned in Feb</p>
            <div style="display: flex; flex-direction: column" class="mb-1">
              <h3 class="mb-0">PKR : 40,000</h3>
            </div>
            <a href="{{route('intern-accounts-admin')}}" class="btn btn-primary">Withraw Amount</a>
          </div>
        </div>
        <div class="col-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png')}}" height="140" alt="view sales" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-8 col--12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Manager Statistics</h5>
        <a href="{{route('managers')}}" class="btn btn-outline-primary rounded-pill btn-xs p-0!">View</a>
      </div>
      <div class="card-body d-flex align-items-end">
        <div class="w-100">
          <div class="row gy-3">

            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-primary me-4 p-2">
                  <i class="icon-base ti tabler-user-check icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">35</h5>
                  <small>Interview</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-info me-4 p-2"><i class="icon-base ti tabler-users icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">20</h5>
                  <small>Contacted</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-danger me-4 p-2">
                  <i class="icon-base ti tabler-list-check icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">15</h5>
                  <small>Test Attempts</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-success me-4 p-2">
                  <i class="icon-base ti tabler-certificate icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">10</h5>
                  <small>Test Completed</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="col-xl-6 col-xl-6">
  <div class="card h-100">
    <div class="card-header d-flex justify-content-between">
      <h5 class="card-title mb-0">Manager KPI Overview</h5>
    </div>

    <div class="card-body d-flex align-items-end">
      <div class="w-100">

        <div class="row gy-8">

          <!-- Manager Shift -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-primary me-4 p-2">
                <i class="ti tabler-clock icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $managerHours ?? 8 }}</h5>
                <small>Manager Shift (hrs)</small>
              </div>
            </div>
          </div>

          <!-- Total Interns -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-info me-4 p-2">
                <i class="ti tabler-users-group icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $totalInterns ?? 0 }}</h5>
                <small>Total Interns</small>
              </div>
            </div>
          </div>

          <!-- Pending Interviews -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-warning me-4 p-2">
                <i class="ti tabler-user-search icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $pendingInterviews ?? 0 }}</h5>
                <small>Pending Interviews</small>
              </div>
            </div>
          </div>

          <!-- Pending Test Reviews -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-danger me-4 p-2">
                <i class="ti tabler-clipboard-check icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $pendingTests ?? 0 }}</h5>
                <small>Test Reviews</small>
              </div>
            </div>
          </div>

          <!-- Ongoing Projects -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-success me-4 p-2">
                <i class="ti tabler-briefcase icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $ongoingProjects ?? 0 }}</h5>
                <small>Ongoing Projects</small>
              </div>
            </div>
          </div>

          <!-- Revenue -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-dark me-4 p-2">
                <i class="ti tabler-currency-rupee icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $monthlyRevenue ?? 0 }}</h5>
                <small>Monthly Revenue</small>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>




<div class="col-xl-6 col-xl-6">
  <div class="card h-100">
    <div class="card-header d-flex justify-content-between">
      <h5 class="card-title mb-0">Interview Pipeline</h5>
    </div>

    <div class="card-body d-flex align-items-end">
      <div class="w-100">

        <div class="row gy-8">

          <!-- Manager Shift -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-primary me-4 p-2">
<i class="ti tabler-file-text icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $managerHours ?? 14 }}</h5>
                <small>New Applications</small>
              </div>
            </div>
          </div>

          <!-- Total Interns -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-info me-4 p-2">
                <i class="ti tabler-users-group icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $totalInterns ?? 0 }}</h5>
                <small>Contacted</small>
              </div>
            </div>
          </div>

          <!-- Pending Interviews -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-warning me-4 p-2">
                <i class="ti tabler-user-search icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $pendingInterviews ?? 0 }}</h5>
                <small>Test Assigned</small>
              </div>
            </div>
          </div>

          <!-- Pending Test Reviews -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-danger me-4 p-2">
                <i class="ti tabler-clipboard-check icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $pendingTests ?? 0 }}</h5>
                <small>Test Completed</small>
              </div>
            </div>
          </div>

          <!-- Ongoing Projects -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-success me-4 p-2">
                <i class="ti tabler-briefcase icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $ongoingProjects ?? 0 }}</h5>
                <small>Interview Scheduled</small>
              </div>
            </div>
          </div>

          <!-- Revenue -->
          <div class="col-md-6 col-6">
            <div class="d-flex align-items-center">
              <div class="badge rounded bg-label-dark me-4 p-2">
<i class="ti tabler-user-check icon-lg"></i>
              </div>
              <div class="card-info">
                <h5 class="mb-0 counter">{{ $monthlyRevenue ?? 0 }}</h5>
                <small>Selected</small>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>
</div>


  {{--<div class="col-xxl-6 col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title mb-0">
          <h5 class="mb-1">Internship Mode</h5>
          <p class="card-subtitle text-muted">Real-time Comparison</p>
        </div>
        <div class="dropdown">
          <button class="btn btn-text-secondary btn-icon rounded-pill p-2" type="button" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-dots-vertical icon-md"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item filter-mode" href="javascript:void(0);" data-val="monthly">Monthly</a>
            <a class="dropdown-item filter-mode" href="javascript:void(0);" data-val="quarterly">Quarterly</a>
            <a class="dropdown-item filter-mode" href="javascript:void(0);" data-val="yearly">Yearly</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-6 mb-4">
          <div class="d-flex align-items-center">
            <span class="badge bg-label-primary p-1_5 rounded me-2"><i
                class="ti tabler-building-community ti-xs"></i></span>
            <div>
              <p class="mb-0 small">Onsite</p>
              <h6 class="mb-0" id="onsite-total-display">{{ $totalOnsite }}</h6>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <span class="badge bg-label-info p-1_5 rounded me-2"><i class="ti tabler-device-laptop ti-xs"></i></span>
            <div>
              <p class="mb-0 small">Remote</p>
              <h6 class="mb-0" id="remote-total-display">{{ $totalRemote }}</h6>
            </div>
          </div>
        </div>
        <div id="internshipModeChart"></div>
      </div>
    </div>
  </div>--}}


 {{-- <div class="col-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Communication Panel</h5>
        <span class="badge bg-label-primary">Targeted Messaging</span>
      </div>
      <div class="card-body">
        <form id="communicationForm" method="POST" action="{{route('admin.send-broadcast')}}">
          @csrf
          <div class="row g-4">


            <div class="col-md-6">
              <label for="fromDate" class="form-label">From Date</label>
              <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="fromDate" name="from_date" />
            </div>
            <div class="col-md-6">
              <label for="toDate" class="form-label">To Date</label>
              <input type="text" class="form-control" placeholder="YYYY-MM-DD" id="toDate" name="to_date" />
            </div>

            <div class="col-md-6">
              <label class="form-label">Internship Type</label>
              <select class="form-select select2" id="internshipType" name="int_status">
                <option value="all">All Interns</option>
                <option value="Test">Test (Under Assessment)</option>
                <option value="Active">Active (Currently Working)</option>
                <option value="Completed">Completed (Alumni)</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Technology Stack</label>
              <select class="form-select select2" id="techStack" name="int_technology">
                <option value="all">All Technologies</option>
                @foreach ($activeTechnologies as $technology)
                <option value="{{$technology->technology}}">{{$technology->technology}}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 mt-4">
              <label class="form-label">Broadcast Message</label>
              <textarea class="form-control" rows="4" name="message"
                placeholder="Type your message for targeted interns..."></textarea>
            </div>

            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">
                <i class="ti tabler-send me-1"></i> Send Targeted Message
              </button>
              <button type="reset" class="btn btn-label-secondary">Clear Filters</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>




</div>--}}
@endsection

@push('scripts')
<script>
document.querySelectorAll('.counter').forEach(counter => {
  let target = parseInt(counter.innerText) || 0;
  let count = 0;
  let speed = target / 50;

  function updateCount() {
    if (count < target) {
      count += speed;
      counter.innerText = Math.floor(count);
      requestAnimationFrame(updateCount);
    } else {
      counter.innerText = target;
    }
  }

  updateCount();
});
</script>


{{--<script>
  document.addEventListener('DOMContentLoaded', function () {
    const chartEl = document.querySelector('#internshipModeChart');
    const onsiteTotalEl = document.getElementById('onsite-total-display');
    const remoteTotalEl = document.getElementById('remote-total-display');
    let chart;

    // Helper to clean arrays
    const cleanData = (data) => {
        let arr = Array.isArray(data) ? data : Object.values(data);
        return arr.map(v => v || 0);
    };

    const allData = {
        monthly: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            onsite: cleanData(@json($onsiteMonthly)),
            remote: cleanData(@json($remoteMonthly))
        },
        quarterly: {
            categories: ['Q1', 'Q2', 'Q3', 'Q4'],
            onsite: cleanData(@json($onsiteQuarterly)),
            remote: cleanData(@json($remoteQuarterly))
        },
        yearly: {
            categories: @json($years).map(String),
            onsite: cleanData(@json($onsiteYearly)),
            remote: cleanData(@json($remoteYearly))
        }
    };

    function renderChart(type) {
        if (!chartEl) return;
        if (chart) chart.destroy();

        const selected = allData[type];
        const isDark = document.documentElement.classList.contains('dark-style');
        const labelColor = isDark ? '#b6bee3' : '#a1acb8';
        const borderColor = isDark ? '#434968' : '#e5e7eb';

        // Force Inline Style to remove white BG
        chartEl.style.backgroundColor = 'transparent';

        const options = {
            chart: {
                height: 250,
                type: 'bar',
                toolbar: { show: false },
                background: 'transparent',
                foreColor: labelColor,
                animations: { enabled: true }
            },
            theme: { mode: isDark ? 'dark' : 'light' },
            plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
            colors: ['#7367f0', '#00bad1'],
            series: [
                { name: 'Onsite', data: selected.onsite },
                { name: 'Remote', data: selected.remote }
            ],
            xaxis: { 
                categories: selected.categories,
                labels: { style: { colors: labelColor } },
                axisBorder: { show: false }
            },
            yaxis: { labels: { style: { colors: labelColor } } },
            grid: { borderColor: borderColor, strokeDashArray: 5 },
            legend: { position: 'bottom', labels: { colors: labelColor } },
            dataLabels: { enabled: false }
        };

        chart = new ApexCharts(chartEl, options);
        chart.render();

        // Update Numbers
        onsiteTotalEl.innerText = selected.onsite.reduce((a, b) => a + b, 0);
        remoteTotalEl.innerText = selected.remote.reduce((a, b) => a + b, 0);
    }

    renderChart('monthly');

    document.querySelectorAll('.filter-mode').forEach(btn => {
        btn.addEventListener('click', function() {
            const mode = this.getAttribute('data-val');
            renderChart(mode);
            document.querySelector('.card-subtitle').innerText = 
                mode.charAt(0).toUpperCase() + mode.slice(1) + ' Internship Comparison';
        });
    });
});
</script>--}}

<script>
  document.addEventListener('DOMContentLoaded', function () {
  

    // Flatpickr Initialization
    const flatpickrFrom = document.querySelector('#fromDate');
    const flatpickrTo = document.querySelector('#toDate');

    if (flatpickrFrom) {
        flatpickrFrom.flatpickr({
            monthSelectorType: 'static',
            dateFormat: 'Y-m-d', // Database friendly format
            allowInput: true
        });
    }

    if (flatpickrTo) {
        flatpickrTo.flatpickr({
            monthSelectorType: 'static',
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }
    
});
</script>


<script>
$('#communicationForm').on('submit', function(e) {
    e.preventDefault(); // Yeh line page refresh hone se rokti hai
    
    let formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            // Error 405 se bachne ke liye redirect na karein, sirf alert dikhayein
            alert(res.message); 
        },
        error: function(err) {
            console.log(err);
        }
    });
});
</script>
@endpush
=======
<h1>My manager dashborad!</h1>
>>>>>>> Stashed changes
