@extends('layouts/layoutMaster')

@section('title', 'Task Kanban')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/jkanban/jkanban.scss', 
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 
  'resources/assets/vendor/libs/quill/typography.scss',
  'resources/assets/vendor/libs/quill/katex.scss', 
  'resources/assets/vendor/libs/quill/editor.scss'
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/app-kanban.scss')
<style>
    /* FIX: Force Horizontal Scroll */
    .kanban-wrapper {
        display: block;
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        padding-bottom: 20px; /* Space for scrollbar */
    }

    .kanban-container {
        display: inline-flex;
        gap: 1.25rem;
        align-items: flex-start;
    }

    /* Column Widths */
    .kanban-column {
        width: 300px;
        min-width: 300px;
        display: inline-block;
        vertical-align: top;
        white-space: normal; /* Allow text inside cards to wrap */
    }

    /* Card Styling */
    .kanban-board .kanban-drag { min-height: 500px; }
    .expired-card { background-color: #fff0f0 !important; border: 1px solid #ffdada !important; opacity: 0.85; }
    .overdue-glow { background-color: #fff9f9; }
    
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(234, 84, 85, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0); }
    }
    .pulse-danger { animation: pulse-red 2s infinite; }

    /* Custom Scrollbar Look */
    .kanban-wrapper::-webkit-scrollbar { height: 8px; }
    .kanban-wrapper::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .kanban-wrapper::-webkit-scrollbar-thumb { background: #c1c4d6; border-radius: 10px; }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js', 
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/select2/select2.js', 
  'resources/assets/vendor/libs/jkanban/jkanban.js',
  'resources/assets/vendor/libs/quill/katex.js', 
  'resources/assets/vendor/libs/quill/quill.js'
])
@endsection

@section('content')
<div class="app-kanban">
    {{-- Header Section: Fixed Button Height and Alignment --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h4 class="mb-0">Intern Task Board</h4>
        
        <div class="d-flex align-items-center gap-3">
            <div style="min-width: 250px;">
                <select id="internFilter" class="select2 form-select" data-placeholder="Filter by Intern">
                    <option value="all">-- All Interns --</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->eti_id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- h-100 and align-self-center ensures it matches the height of the select box --}}
            <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary d-flex align-items-center h-100 shadow-sm" style="height: 38px;">
                <i class="ti tabler-plus me-1"></i> Add Task
            </a>
        </div>
    </div>

    {{-- Kanban Wrapper --}}
    <div class="kanban-wrapper">
        <div class="kanban-container">
            @php
                $columns = [
                    'Assigned' => 'bg-label-primary',
                    'Ongoing' => 'bg-label-info',
                    'In Progress' => 'bg-label-info',
                    'Pending' => 'bg-label-warning',
                    'Submitted' => 'bg-label-success',
                    'Completed' => 'bg-label-success',
                    'Rejected' => 'bg-label-danger',
                    'Expired' => 'bg-label-secondary'
                ];
            @endphp

            @foreach($columns as $status => $color)
                <div class="kanban-column">
                    <div class="card shadow-none border-0 bg-transparent">
                        <div class="card-header d-flex justify-content-between align-items-center p-2 mb-3 rounded {{ $color }}">
                            <h6 class="mb-0 fw-bold text-uppercase small">{{ $status }}</h6>
                            <span class="badge bg-white text-dark rounded-circle column-badge">0</span>
                        </div>
                        
                        <div class="kanban-list d-flex flex-column gap-3" data-status="{{ $status }}" style="min-height: 600px;">
                            {{-- Tasks injected via AJAX --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterSelect = document.getElementById('internFilter');
    let isFetching = false; // Prevent multiple simultaneous AJAX calls

    // 1. Initialize Sortable FIRST
    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('.kanban-list').forEach(column => {
            new Sortable(column, {
                group: 'shared', 
                animation: 150, 
                ghostClass: 'bg-label-primary',
                onEnd: function (evt) {
                    const itemEl = evt.item; 
                    const toColumn = evt.to; 
                    if (evt.from !== toColumn) {
                        const taskId = itemEl.getAttribute('data-task-id');
                        const taskType = itemEl.getAttribute('data-task-type');
                        const newStatus = toColumn.getAttribute('data-status');

                        fetch('{{ route('supervisor.tasks.updateStatusAjax') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ task_id: taskId, type: taskType, status: newStatus })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateColumnBadges();
                            }
                        });
                    }
                }
            });
        });
    }

    // 2. The Task Fetching Function
    function loadKanbanTasks(etiId) {
        if (isFetching) return;
        isFetching = true;

        // Clear columns before injection to prevent duplication
        document.querySelectorAll('.kanban-list').forEach(list => list.innerHTML = '');

        fetch(`{{ route('supervisor.tasks.fetchKanbanAjax') }}?eti_id=${etiId}`)
            .then(response => response.json())
            .then(data => {
                data.tasks.forEach(task => {
                    const column = document.querySelector(`.kanban-list[data-status="${task.status}"]`);
                    if (!column) return;

                    let cardClass = 'border-0';
                    let customBadge = '';
                    let dateText = `<small class="text-muted"><i class="ti tabler-calendar ti-xs me-1"></i>${task.end_date}</small>`;

                    if (task.status === 'Expired') {
                        cardClass = 'expired-card';
                        customBadge = '<span class="badge bg-label-danger btn-xs">EXPIRED</span>';
                    } 
                    else if (task.is_overdue) {
                        cardClass = 'overdue-glow border-start border-danger border-3';
                        customBadge = '<span class="badge bg-danger pulse-danger btn-xs">OVERDUE</span>';
                    }

                    const typeBadge = task.type === 'project' 
                        ? '<span class="badge bg-label-info btn-xs">Project</span>' 
                        : '<span class="badge bg-label-primary btn-xs">Task</span>';
                    
                    let actionButtons = `<a href="${task.edit_url}" class="btn btn-sm btn-icon text-secondary"><i class="ti tabler-edit ti-xs"></i></a>`;
                    if (task.type !== 'project') {
                        actionButtons = `<a href="${task.review_url}" class="btn btn-sm btn-icon text-primary"><i class="ti tabler-eye ti-xs"></i></a>` + actionButtons;
                    }

                    const cardHtml = `
                        <div class="card kanban-task-card shadow-sm ${cardClass}" data-task-id="${task.id}" data-task-type="${task.type}" style="cursor:grab">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <div class="d-flex gap-1">${typeBadge}${customBadge}</div>
                                    <div class="avatar avatar-xs">
                                        <span class="avatar-initial rounded-circle bg-label-secondary small">${task.intern_name.charAt(0)}</span>
                                    </div>
                                </div>
                                <h6 class="mb-2 fw-bold text-heading">${task.title}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted fw-medium">${task.intern_name}</small>
                                    ${dateText}
                                </div>
                                <div class="d-flex justify-content-end mt-3 pt-2 border-top">
                                    ${actionButtons}
                                </div>
                            </div>
                        </div>
                    `;
                    column.insertAdjacentHTML('beforeend', cardHtml);
                });
                updateColumnBadges();
                isFetching = false;
            })
            .catch(() => { isFetching = false; });
    }

    // 3. Initialize Select2 and Handlers
    if ($.fn.select2) {
        $('#internFilter').select2({
            placeholder: "Filter by Intern",
            allowClear: true
        }).on('change', function() {
            loadKanbanTasks($(this).val());
        });
    } else {
        filterSelect.addEventListener('change', function() {
            loadKanbanTasks(this.value);
        });
    }

    function updateColumnBadges() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            const taskCount = col.querySelectorAll('.kanban-task-card').length;
            const badge = col.querySelector('.column-badge');
            if(badge) badge.textContent = taskCount;
        });
    }

    // Initial Load
    loadKanbanTasks('all');
});
</script>
@endsection   