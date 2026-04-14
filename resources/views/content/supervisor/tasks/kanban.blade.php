@extends('layouts/layoutMaster')

@section('title', 'Task Kanban')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- UPDATED HEADER BLOCK --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h4 class="mb-0">Task Kanban Board</h4>
        
        <div class="d-flex align-items-stretch gap-2">
            {{-- Filter Dropdown --}}
            <select id="internFilter" class="form-select shadow-sm" style="min-width: 220px;">
                <option value="all">-- Filter by Intern --</option>
                @foreach($tasks->pluck('intern_name')->unique()->sort() as $name)
                    <option value="{{ $name }}">{{ $name }}</option>
                @endforeach
            </select>
            
            {{-- Create Task Button (Fixed) --}}
            <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary shadow-sm text-nowrap d-flex align-items-center">
                <i class="ti tabler-plus me-1"></i> Create Task
            </a>
        </div>
    </div>

    {{-- Alert Container --}}
    <div id="kanban-alert" class="alert alert-success d-none shadow-sm" style="position: fixed; top: 20px; right: 20px; z-index: 9999;" role="alert">
        Status updated successfully!
    </div>

    <div class="kanban-wrapper" style="overflow-x: auto; padding-bottom: 20px; cursor: grab;">
        <div class="d-flex flex-nowrap" style="gap: 1.5rem;">
            @php
                $columns = [
                    'Assigned' => 'bg-primary',
                    'Ongoing' => 'bg-info',
                    'In Progress' => 'bg-info',
                    'Pending' => 'bg-warning',
                    'Submitted' => 'bg-success',
                    'Completed' => 'bg-success',
                    'Rejected' => 'bg-danger',
                    'Expired' => 'bg-secondary'
                ];
            @endphp

            @foreach($columns as $status => $color)
                <div class="kanban-column" style="width: 280px; min-width: 280px;">
                    <div class="card h-100 shadow-none border">
                        <div class="card-header d-flex justify-content-between align-items-center py-2 {{ $color }}">
                            <h6 class="mb-0 {{ $status == 'Pending' ? 'text-dark' : 'text-white' }} fw-bold">{{ $status }}</h6>
                            @php $count = $tasks->where('status', $status)->count(); @endphp
                            <span class="badge {{ $status == 'Pending' ? 'bg-dark text-white' : 'bg-white text-dark' }} rounded-pill column-badge">{{ $count }}</span>
                        </div>
                        
                        <div class="card-body p-2 bg-light bg-opacity-10 kanban-list" data-status="{{ $status }}" style="min-height: 500px;">
                            
                            @foreach($tasks->where('status', $status) as $task)
                                {{-- OVERDUE MATH --}}
                                @php
                                    $isOverdue = $task->end_date && \Carbon\Carbon::parse($task->end_date)->isBefore(now()->startOfDay()) 
                                                 && in_array($status, ['Assigned', 'Ongoing', 'In Progress', 'Pending']);
                                @endphp

                                {{-- TASK CARD --}}
                                <div class="card mb-2 shadow-sm kanban-task-card {{ $isOverdue ? 'border-danger border-2 overdue-glow' : 'border-0' }}" 
                                     data-task-id="{{ $task->id }}" 
                                     data-task-type="{{ $task->type }}"
                                     data-intern="{{ $task->intern_name }}">
                                     
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                @if($task->type === 'project')
                                                    <span class="badge bg-label-info" style="font-size: 0.65rem;">Project Task</span>
                                                @else
                                                    <span class="badge bg-label-primary" style="font-size: 0.65rem;">Standard Task</span>
                                                @endif
                                            </div>

                                            @if($isOverdue)
                                                <span class="badge bg-danger pulse-danger shadow-sm" style="font-size: 0.65rem;">
                                                    <i class="ti tabler-alert-triangle ti-xs"></i> OVERDUE
                                                </span>
                                            @endif
                                        </div>

                                        <h6 class="card-title mb-2 fw-bold" style="white-space: normal; line-height: 1.4;">{{ $task->title }}</h6>
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-secondary small">{{ $task->intern_name }}</span>
                                            
                                            @if($isOverdue)
                                                <small class="text-danger fw-bold"><i class="ti tabler-calendar-time ti-xs"></i> {{ \Carbon\Carbon::parse($task->end_date)->format('d M') }}</small>
                                            @else
                                                <small class="text-muted fw-semibold">{{ \Carbon\Carbon::parse($task->end_date)->format('d M') }}</small>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex justify-content-end gap-1 border-top pt-2">
                                            @if($task->type === 'project')
                                                <a href="{{ route('supervisor.projects.tasks.edit', [$task->project_id, $task->id]) }}" class="btn btn-xs btn-outline-secondary py-1 px-2">Edit</a>
                                            @else
                                                <a href="{{ route('supervisor.tasks.review', $task->id) }}" class="btn btn-xs btn-primary py-1 px-2">Review</a>
                                                <a href="{{ route('supervisor.tasks.edit', $task->id) }}" class="btn btn-xs btn-outline-secondary py-1 px-2">Edit</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* CSS safely injected directly into the content block */
.kanban-wrapper::-webkit-scrollbar { height: 10px; }
.kanban-wrapper::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
.kanban-wrapper::-webkit-scrollbar-thumb { background: #c1c4d6; border-radius: 4px; }
.kanban-wrapper::-webkit-scrollbar-thumb:hover { background: #a5a8be; }
.kanban-wrapper:active { cursor: grabbing !important; }
.kanban-task-card { transition: all 0.2s ease; cursor: grab; }
.kanban-task-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
.sortable-ghost { opacity: 0.4; background-color: #f8f9fa; border: 2px dashed #696cff !important; }
.sortable-drag { cursor: grabbing !important; }
.overdue-glow { background-color: #fff9f9; }
@keyframes pulse-red {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(234, 84, 85, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0); }
}
.pulse-danger { animation: pulse-red 2s infinite; }
</style>

{{-- Load Sortable JS directly --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // 1. FILTERING LOGIC 
    const filterSelect = document.getElementById('internFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', function(e) {
            const selectedValue = e.target.value.trim();
            const cards = document.querySelectorAll('.kanban-task-card');

            cards.forEach(card => {
                const cardIntern = card.getAttribute('data-intern') ? card.getAttribute('data-intern').trim() : '';
                if (selectedValue === 'all' || cardIntern === selectedValue) {
                    card.style.display = 'block'; // Show card
                } else {
                    card.style.display = 'none'; // Hide card
                }
            });
            updateColumnBadges();
        });
    }

    // 2. BADGE RE-CALCULATOR
    function updateColumnBadges() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            let visibleCount = 0;
            col.querySelectorAll('.kanban-task-card').forEach(card => {
                if (card.style.display !== 'none') {
                    visibleCount++;
                }
            });
            col.querySelector('.column-badge').textContent = visibleCount;
        });
    }

    // 3. MOUSE DRAG TO SCROLL LOGIC
    const slider = document.querySelector('.kanban-wrapper');
    let isDown = false; let startX; let scrollLeft;

    if (slider) {
        slider.addEventListener('mousedown', (e) => {
            if(e.target.closest('.kanban-task-card') || e.target.closest('button') || e.target.closest('a') || e.target.closest('select')) return;
            isDown = true;
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => { isDown = false; });
        slider.addEventListener('mouseup', () => { isDown = false; });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed
            slider.scrollLeft = scrollLeft - walk;
        });
    }

    // 4. DRAG AND DROP LOGIC (SortableJS)
    try {
        document.querySelectorAll('.kanban-list').forEach(column => {
            new Sortable(column, {
                group: 'shared',
                animation: 150,
                ghostClass: 'sortable-ghost',
                dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    const itemEl = evt.item; 
                    const toColumn = evt.to; 
                    
                    if (evt.from !== toColumn) {
                        const taskId = itemEl.getAttribute('data-task-id');
                        const taskType = itemEl.getAttribute('data-task-type');
                        const newStatus = toColumn.getAttribute('data-status');

                        fetch('{{ route('supervisor.tasks.updateStatusAjax') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                type: taskType,
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateColumnBadges();
                                const alertBox = document.getElementById('kanban-alert');
                                alertBox.classList.remove('d-none');
                                setTimeout(() => { alertBox.classList.add('d-none'); }, 2500);
                            } else {
                                alert('Failed to update status.');
                                window.location.reload(); 
                            }
                        })
                        .catch(error => window.location.reload());
                    }
                },
            });
        });
    } catch (err) {
        console.error("Drag and drop could not be initialized:", err);
    }

});
</script>
@endsection