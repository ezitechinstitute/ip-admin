@extends('layouts/layoutMaster')

@section('title', 'Project Dashboard')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        --primary-gradient: linear-gradient(135deg, #3b82f6, #1e40af);
        --success-gradient: linear-gradient(135deg, #10b981, #047857);
        --warning-gradient: linear-gradient(135deg, #f59e0b, #b45309);
        --info-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Premium Card */
    .premium-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    /* Stat Cards */
    .stat-card-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .stat-card-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-gradient);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .stat-card-premium:hover::after {
        transform: scaleX(1);
    }

    .stat-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px -15px rgba(0, 0, 0, 0.2);
    }

    .stat-icon-premium {
        width: 60px;
        height: 60px;
        background: var(--stat-bg);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-value-premium {
        font-size: 2.2rem;
        font-weight: 800;
        background: var(--stat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    /* Badge Styles */
    .badge-custom {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-ongoing { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .badge-submitted { background: rgba(139,92,246,0.15); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.3); }
    .badge-approved { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-rejected { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

    /* Quick Stats */
    .quick-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-stat-item {
        flex: 1;
        min-width: 100px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .quick-stat-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    /* Table Styles */
    .projects-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .projects-table {
        width: 100%;
        min-width: 650px;
        margin-bottom: 0;
    }

    .projects-table thead th {
        background: rgba(255, 255, 255, 0.9);
        padding: 0.75rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        white-space: nowrap;
    }

    .projects-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .projects-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .projects-table tbody td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    .timeline-cell {
        min-width: 100px;
    }

    /* Project Cards for Mobile */
    .project-mobile-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .project-mobile-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.1);
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @media (max-width: 768px) {
        .stat-card-premium {
            padding: 1rem;
        }
        .stat-value-premium {
            font-size: 1.5rem;
        }
        .stat-icon-premium {
            width: 45px;
            height: 45px;
        }
        .quick-stats {
            flex-wrap: wrap;
        }
        .quick-stat-item {
            min-width: calc(50% - 0.5rem);
        }
        .desktop-table {
            display: none;
        }
        .mobile-cards-view {
            display: block !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-cards-view {
            display: none;
        }
        .desktop-table {
            display: block;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-briefcase-fill text-primary me-2"></i>Project Dashboard
            </h4>
            <p class="text-muted small mb-0">Manage and track your assigned projects</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <i class="bi bi-calendar-week me-1"></i> {{ now()->format('F Y') }}
            </span>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['icon'=>'bi-briefcase','count'=>$stats['total'] ?? 0,'label'=>'Total Projects','gradient'=>'linear-gradient(135deg, #3b82f6, #1e40af)','bg'=>'rgba(59,130,246,0.1)','trend'=>'+12%'],
                ['icon'=>'bi-hourglass-split','count'=>$stats['ongoing'] ?? 0,'label'=>'Ongoing','gradient'=>'linear-gradient(135deg, #f59e0b, #b45309)','bg'=>'rgba(245,158,11,0.1)','trend'=>'+5%'],
                ['icon'=>'bi-upload','count'=>$stats['submitted'] ?? 0,'label'=>'Submitted','gradient'=>'linear-gradient(135deg, #8b5cf6, #6d28d9)','bg'=>'rgba(139,92,246,0.1)','trend'=>'+8%'],
                ['icon'=>'bi-check-circle','count'=>$stats['approved'] ?? 0,'label'=>'Approved','gradient'=>'linear-gradient(135deg, #10b981, #047857)','bg'=>'rgba(16,185,129,0.1)','trend'=>'+15%'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 col-6 animate-card" style="animation-delay: {{ 0.1 + ($loop->index * 0.05) }}s;">
            <div class="stat-card-premium" style="--stat-gradient: {{ $card['gradient'] }}; --stat-bg: {{ $card['bg'] }}">
                <div class="stat-icon-premium" style="background: {{ $card['bg'] }}">
                    <i class="bi {{ $card['icon'] }} fs-2" style="background: {{ $card['gradient'] }}; -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                <div class="stat-value-premium">{{ $card['count'] }}</div>
                <div class="stat-label-premium text-muted small mt-1">{{ $card['label'] }}</div>
                <div class="mt-2">
                    <small class="text-success"><i class="bi bi-arrow-up-short"></i>{{ $card['trend'] }} vs last month</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Stats Row --}}
    <div class="quick-stats mb-4 animate-card" style="animation-delay: 0.3s;">
        <div class="quick-stat-item">
            <i class="bi bi-calendar-check text-primary fs-4"></i>
            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('F j, Y') }}</div>
            <small class="text-muted">Current Date</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-star-fill text-warning fs-4"></i>
            <div class="fw-bold">{{ $stats['approved'] ?? 0 }}/{{ $stats['total'] ?? 0 }}</div>
            <small class="text-muted">Completion Rate</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-trophy-fill text-success fs-4"></i>
            <div class="fw-bold">{{ $stats['approved'] ?? 0 }}</div>
            <small class="text-muted">Achievements</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-clock-fill text-info fs-4"></i>
            <div class="fw-bold">{{ $stats['ongoing'] ?? 0 }}</div>
            <small class="text-muted">Active Projects</small>
        </div>
    </div>

    {{-- Projects Section --}}
    <div class="premium-card animate-card" style="animation-delay: 0.4s;">
        <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Assigned Projects</h5>
                <p class="text-muted small mb-0">Complete list of your assigned projects</p>
            </div>
            <div class="input-group" style="width: 250px;">
                <input type="text" id="projectSearch" class="form-control form-control-sm rounded-pill" placeholder="Search projects...">
                <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3"></i>
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="desktop-table projects-table-container">
            <table class="projects-table" id="projectTable">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Technology</th>
                        <th>Supervisor</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    @php
                        $statusClass = match(strtolower($project->pstatus)) {
                            'ongoing' => 'ongoing',
                            'submitted' => 'submitted',
                            'approved' => 'approved',
                            'rejected' => 'rejected',
                            default => 'secondary'
                        };
                        $statusIcon = match(strtolower($project->pstatus)) {
                            'ongoing' => 'hourglass-split',
                            'submitted' => 'upload',
                            'approved' => 'check-circle',
                            'rejected' => 'x-circle',
                            default => 'circle'
                        };
                        $startDate = \Carbon\Carbon::parse($project->start_date);
                        $endDate = \Carbon\Carbon::parse($project->end_date);
                    @endphp
                    <tr data-title="{{ strtolower($project->title) }}">
                        <td>
                            <div class="fw-semibold">{{ $project->title }}</div>
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 35) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="bi bi-code-slash me-1"></i>{{ \Illuminate\Support\Str::limit($project->tech_stack ?? 'N/A', 15) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-circle text-primary"></i>
                                <span>{{ \Illuminate\Support\Str::limit($project->supervisor->name ?? 'Not Assigned', 12) }}</span>
                            </div>
                        </td>
                        <td class="timeline-cell">
                            <small class="text-muted">
                                {{ $startDate->format('d M Y') }}<br>
                                <i class="bi bi-arrow-right"></i><br>
                                {{ $endDate->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            <span class="badge-custom badge-{{ $statusClass }}">
                                <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                {{ ucfirst($project->pstatus) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('intern.projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">No projects assigned yet</p>
                            <small class="text-muted">Projects assigned to you will appear here</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards View --}}
        <div class="mobile-cards-view p-3">
            @forelse($projects as $project)
            @php
                $statusClass = match(strtolower($project->pstatus)) {
                    'ongoing' => 'ongoing',
                    'submitted' => 'submitted',
                    'approved' => 'approved',
                    'rejected' => 'rejected',
                    default => 'secondary'
                };
                $statusIcon = match(strtolower($project->pstatus)) {
                    'ongoing' => 'hourglass-split',
                    'submitted' => 'upload',
                    'approved' => 'check-circle',
                    'rejected' => 'x-circle',
                    default => 'circle'
                };
                $startDate = \Carbon\Carbon::parse($project->start_date);
                $endDate = \Carbon\Carbon::parse($project->end_date);
            @endphp
            <div class="project-mobile-card" data-title="{{ strtolower($project->title) }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="fw-bold">{{ $project->title }}</div>
                    <span class="badge-custom badge-{{ $statusClass }}">
                        <i class="bi bi-{{ $statusIcon }} me-1"></i>
                        {{ ucfirst($project->pstatus) }}
                    </span>
                </div>
                <div class="text-muted small mb-2">
                    {{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 80) }}
                </div>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-code-slash me-1"></i>{{ \Illuminate\Support\Str::limit($project->tech_stack ?? 'N/A', 20) }}
                    </span>
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-person me-1"></i>{{ \Illuminate\Support\Str::limit($project->supervisor->name ?? 'Not Assigned', 15) }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <small class="text-muted">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</small>
                    </div>
                    <a href="{{ route('intern.projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-folder-x fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No projects assigned yet</p>
            </div>
            @endforelse
        </div>

        @if($projects->hasPages())
        <div class="p-3 border-top bg-transparent">
            {{ $projects->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

<script>
// Search functionality
document.getElementById('projectSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    
    // Desktop table rows
    const rows = document.querySelectorAll('#projectTable tbody tr');
    rows.forEach(row => {
        const title = row.dataset?.title || '';
        if (title.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Mobile cards
    const cards = document.querySelectorAll('.project-mobile-card');
    cards.forEach(card => {
        const title = card.dataset?.title || '';
        if (title.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
@endsection