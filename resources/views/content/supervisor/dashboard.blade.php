@extends('layouts/layoutMaster')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
    <div class="row">
        <div class="col-12" style="margin-top: 85px;">
    <!-- KPI Row 1: Intern Lifecycle -->
    <div class="row g-6 mb-6">
        <!-- Interviewing -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-none border-0 bg-label-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h4 class="mb-0 fw-bold">{{ $interviewCount }}</h4>
                            <span class="text-heading small fw-semibold">Interviewing</span>
                        </div>
                        <div class="avatar avatar-md bg-white rounded-circle shadow-sm">
                            <i class="icon-base ti tabler-speakerphone ti-sm text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- To Contact -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-none border-0 bg-label-info">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h4 class="mb-0 fw-bold">{{ $contactCount }}</h4>
                            <span class="text-heading small fw-semibold">To Contact</span>
                        </div>
                        <div class="avatar avatar-md bg-white rounded-circle shadow-sm">
                            <i class="icon-base ti tabler-phone ti-sm text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Test -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-none border-0 bg-label-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h4 class="mb-0 fw-bold">{{ $testCount }}</h4>
                            <span class="text-heading small fw-semibold">In Test</span>
                        </div>
                        <div class="avatar avatar-md bg-white rounded-circle shadow-sm">
                            <i class="icon-base ti tabler-pencils ti-sm text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Selection Completed -->
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 shadow-none border-0 bg-label-success">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="content-left">
                            <h4 class="mb-0 fw-bold">{{ $completedCount }}</h4>
                            <span class="text-heading small fw-semibold">Selection Completed</span>
                        </div>
                        <div class="avatar avatar-md bg-white rounded-circle shadow-sm">
                            <i class="icon-base ti tabler-discount-check ti-sm text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Row 2: Management Tasks -->
    <div class="row g-6 mb-6">
        <!-- Total Assigned -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-primary rounded-3">
                        <i class="icon-base ti tabler-users ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $totalAssignedInterns }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Total Assigned</span>
                </div>
            </div>
        </div>

        <!-- Active Interns -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-success rounded-3">
                        <i class="icon-base ti tabler-user-check ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $activeInterns }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Active Interns</span>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-warning rounded-3">
                        <i class="icon-base ti tabler-clipboard-list ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $pendingTaskReviews }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Pending Reviews</span>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-info rounded-3">
                        <i class="icon-base ti tabler-circle-check ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $tasksCompletedToday }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Completed Today</span>
                </div>
            </div>
        </div>

        <!-- Overdue Tasks -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-danger rounded-3">
                        <i class="icon-base ti tabler-alert-triangle ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $overdueTasks }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Overdue Tasks</span>
                </div>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="avatar avatar-md mx-auto mb-3 bg-label-secondary rounded-3">
                        <i class="icon-base ti tabler-briefcase ti-sm"></i>
                    </div>
                    <h4 class="mb-0 fw-bold">{{ $totalProjectsAssigned }}</h4>
                    <span class="text-muted small fw-medium text-nowrap">Total Projects</span>
                </div>
            </div>
        </div>
    </div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">New Intern Assigned</h5>
      </div>
      <div class="card-body">
        @forelse($newInterns as $intern)
          <div class="mb-3 border-bottom pb-2">
            <div><strong>{{ $intern->name }}</strong></div>
            <div class="text-muted small">{{ $intern->email }}</div>
            <div class="small">Technology: {{ $intern->int_technology }}</div>
            <div class="small">Status: {{ $intern->int_status }}</div>
          </div>
        @empty
          <p class="mb-0 text-muted">No recent intern assignments.</p>
        @endforelse
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Task Submissions</h5>
      </div>
      <div class="card-body">
        @forelse($taskSubmissions as $task)
          <div class="mb-3 border-bottom pb-2">
            <div><strong>{{ $task->task_title }}</strong></div>
            <div class="small">Intern ETI ID: {{ $task->eti_id }}</div>
            <div class="small">Status: {{ $task->task_status }}</div>
            <div class="text-muted small">{{ $task->updated_at }}</div>
          </div>
        @empty
          <p class="mb-0 text-muted">No task submissions found.</p>
        @endforelse
      </div>
    </div>
  </div>

<div class="row g-4 mt-2">
  <!-- Notifications -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="mb-0">System Notifications</h5>
        <span class="badge bg-primary rounded-pill">{{ count($notifications) }}</span>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush">
          @forelse($notifications as $notif)
            <li class="list-group-item px-0 pb-3">
              <div class="d-flex align-items-start">
                <div class="flex-shrink-0 mt-1">
                  <i class="ti ti-bell-ringing text-warning ti-sm me-2"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-medium">{{ $notif->type }}</span>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
                  </div>
                  <p class="mb-0 small">{{ $notif->message }}</p>
                  @if($notif->eti_id)
                    <small class="text-primary">Intern: {{ $notif->eti_id }}</small>
                  @endif
                </div>
              </div>
            </li>
          @empty
            <li class="list-group-item text-center text-muted">No new notifications</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

  <!-- Activity Logs -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="mb-0">Recent Activity Log</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <thead>
              <tr>
                <th>Action</th>
                <th>Details</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activityLogs as $log)
                <tr>
                  <td><span class="badge bg-label-secondary">{{ $log->action }}</span></td>
                  <td class="small">{{ $log->details }}</td>
                  <td class="small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center">No recent activity</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
        </div>
</div>
</div>
</div>
@endsection
</div>