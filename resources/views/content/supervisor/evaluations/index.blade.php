@extends('layouts/layoutMaster')

@section('title', 'Evaluations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monthly Evaluations</h4>
            <p class="text-muted mb-0">Review and manage intern performance metrics.</p>
        </div>
        <span class="badge bg-label-primary rounded-pill">{{ $evaluations->total() }} Records</span>
    </div>

    <div class="card">
        {{-- Search Header --}}
        <div class="card-header border-bottom">
            <div class="row">
                <div class="col-md-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search Intern ID or Name...">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Intern</th>
                        <th>Month</th>
                        <th class="text-center">Overall Score</th>
                        <th>Skill Breakdown</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($evaluations as $eval)
                        <tr>
                            {{-- Intern Identity --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($eval->intern_name, 0, 1)) }}</span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-heading">{{ $eval->intern_name }}</span>
                                        <small class="text-muted">{{ $eval->eti_id }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Evaluation Month --}}
                            <td>
                                <span class="badge bg-label-info">
                                    <i class="ti ti-calendar-event ti-xs me-1"></i>{{ $eval->month }}
                                </span>
                            </td>

                            {{-- Overall Score --}}
                            <td class="text-center">
                                <h5 class="mb-0 fw-bold text-primary">{{ $eval->overall_score }}<small class="text-muted">/10</small></h5>
                            </td>

                            {{-- Performance Breakdown --}}
                            <td>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $eval->technical_skills * 10 }}%" title="Technical"></div>
                                    <div class="progress-bar bg-info" style="width: {{ $eval->problem_solving * 10 }}%" title="Problem Solving"></div>
                                    <div class="progress-bar bg-warning" style="width: {{ $eval->communication * 10 }}%" title="Communication"></div>
                                    <div class="progress-bar bg-danger" style="width: {{ $eval->task_completion * 10 }}%" title="Task Completion"></div>
                                </div>
                                <div class="d-flex gap-2" style="font-size: 0.6rem; text-transform: uppercase; font-weight: 600;">
                                    <span class="text-success">● Tech</span>
                                    <span class="text-info">● Prob</span>
                                    <span class="text-warning">● Comm</span>
                                    <span class="text-danger">● Task</span>
                                </div>
                            </td>

                            {{-- Actions (Consistent with Projects Module) --}}
                            <td class="text-center">
                                <div class="d-inline-block">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow">
                                        {{-- View Details (Triggers Sidebar) --}}
                                        <button class="dropdown-item" type="button" 
                                                data-bs-toggle="offcanvas" 
                                                data-bs-target="#offcanvasEvalDetail" 
                                                onclick="showEvalDetails({{ json_encode($eval) }})">
                                            <i class="ti ti-eye me-2 text-primary"></i> View Details
                                        </button>
                                        
                                        <a href="{{ route('supervisor.viewIntern', $eval->eti_id) }}" class="dropdown-item">
                                            <i class="ti ti-user-circle me-2"></i> Intern Profile
                                        </a>

                                        <a href="{{ route('supervisor.evaluations.edit', $eval->id) }}" class="dropdown-item">
                                            <i class="ti ti-edit me-2"></i> Edit Evaluation
                                        </a>

                                        <div class="dropdown-divider"></div>

                                        <form action="{{ route('supervisor.evaluations.delete', $eval->id) }}" method="POST" onsubmit="return confirm('Permanently delete this evaluation?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ti ti-trash me-2"></i> Delete Record
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5">No evaluation records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer border-top">
            {{ $evaluations->links() }}
        </div>
    </div>
</div>

{{-- Detail Sidebar (Clean View Mode) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEvalDetail" style="width: 400px;">
    <div class="offcanvas-header border-bottom bg-light">
        <h5 class="offcanvas-title fw-bold">Evaluation Summary</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <div class="text-center mb-4 pb-3 border-bottom">
            <div class="avatar avatar-xl mb-3 mx-auto">
                <span class="avatar-initial rounded-circle bg-label-primary fs-2" id="detail_avatar">?</span>
            </div>
            <h4 class="mb-1" id="detail_name">Intern Name</h4>
            <span class="badge bg-label-secondary" id="detail_id">ETI-0000</span>
        </div>

        <h6 class="text-uppercase text-muted small fw-bold mb-3">Performance Scores</h6>
        <div class="vstack gap-4 mb-4">
            <div>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Technical Skills</span>
                    <span class="fw-bold" id="val_tech">0/10</span>
                </div>
                <div class="progress" style="height: 8px;"><div id="pb_tech" class="progress-bar bg-success" style="width: 0%"></div></div>
            </div>
            <div>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Problem Solving</span>
                    <span class="fw-bold" id="val_prob">0/10</span>
                </div>
                <div class="progress" style="height: 8px;"><div id="pb_prob" class="progress-bar bg-info" style="width: 0%"></div></div>
            </div>
            <div>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Communication</span>
                    <span class="fw-bold" id="val_comm">0/10</span>
                </div>
                <div class="progress" style="height: 8px;"><div id="pb_comm" class="progress-bar bg-warning" style="width: 0%"></div></div>
            </div>
            <div>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Task Completion</span>
                    <span class="fw-bold" id="val_task">0/10</span>
                </div>
                <div class="progress" style="height: 8px;"><div id="pb_task" class="progress-bar bg-danger" style="width: 0%"></div></div>
            </div>
        </div>

        <div class="alert bg-label-primary d-flex align-items-center mt-auto">
            <i class="ti ti-info-circle me-2"></i>
            <span>Evaluation period: <strong id="detail_month">-</strong></span>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function showEvalDetails(data) {
    document.getElementById('detail_name').innerText = data.intern_name;
    document.getElementById('detail_id').innerText = data.eti_id;
    document.getElementById('detail_avatar').innerText = data.intern_name.charAt(0).toUpperCase();
    document.getElementById('detail_month').innerText = data.month;

    const setSkill = (valId, pbId, val) => {
        document.getElementById(valId).innerText = val + '/10';
        document.getElementById(pbId).style.width = (val * 10) + '%';
    };

    setSkill('val_tech', 'pb_tech', data.technical_skills);
    setSkill('val_prob', 'pb_prob', data.problem_solving);
    setSkill('val_comm', 'pb_comm', data.communication);
    setSkill('val_task', 'pb_task', data.task_completion);
}
</script>
@endsection