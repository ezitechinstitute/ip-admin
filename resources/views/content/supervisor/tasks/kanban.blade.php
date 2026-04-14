@extends('layouts/layoutMaster')

@section('title', 'Task Kanban')

{{-- Add SortableJS for smooth drag and drop --}}
@section('vendor-script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Task Kanban Board</h4>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">Create Task</a>
    </div>

    {{-- Alert Container for silent background updates --}}
    <div id="kanban-alert" class="alert alert-success d-none shadow-sm" style="position: fixed; top: 20px; right: 20px; z-index: 9999;" role="alert">
        Status updated successfully!
    </div>

    <div class="kanban-wrapper">
        <div class="d-flex flex-nowrap g-3" style="padding-bottom: 20px;">
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
                <div class="kanban-column" style="width: 280px; min-width: 280px; margin-right: 1.5rem;">
                    <div class="card h-100 shadow-none border">
                        <div class="card-header d-flex justify-content-between align-items-center py-2 {{ $color }}">
                            <h6 class="mb-0 {{ $status == 'Pending' ? 'text-dark' : 'text-white' }} fw-bold">{{ $status }}</h6>
                            @php $count = $tasks->where('status', $status)->count(); @endphp
                            <span class="badge {{ $status == 'Pending' ? 'bg-dark text-white' : 'bg-white text-dark' }} rounded-pill column-badge">{{ $count }}</span>
                        </div>
                        
                        {{-- 🔥 ADDED: kanban-list class and data-status attribute --}}
                        <div class="card-body p-2 bg-light bg-opacity-10 kanban-list" data-status="{{ $status }}" style="min-height: 500px;">
                            
                            @foreach($tasks->where('status', $status) as $task)
                                {{-- 🔥 ADDED: data-task-id and data-task-type attributes --}}
                                <div class="card mb-2 shadow-sm border-0 kanban-task-card" data-task-id="{{ $task->id }}" data-task-type="{{ $task->type }}" style="cursor: grab;">
                                    <div class="card-body p-3">
                                        @if($task->type === 'project')
                                            <span class="badge bg-label-info mb-2" style="font-size: 0.65rem;">Project Task</span>
                                        @else
                                            <span class="badge bg-label-primary mb-2" style="font-size: 0.65rem;">Standard Task</span>
                                        @endif

                                        <h6 class="card-title mb-2 fw-bold" style="white-space: normal; line-height: 1.4;">{{ $task->title }}</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-secondary small">{{ $task->intern_name }}</span>
                                            <small class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($task->end_date)->format('d M') }}</small>
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
    /* Add this inside your existing <style> block */
    .kanban-wrapper {
        overflow-x: auto;
        padding-bottom: 20px;
        cursor: grab; /* Shows the grab hand */
    }
    .kanban-wrapper:active {
        cursor: grabbing; /* Shows a closed hand when dragging */
    }
    .kanban-wrapper::-webkit-scrollbar {
        height: 10px; /* Made slightly thicker so it's easier to click if needed */
    }
    .kanban-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1; 
        border-radius: 4px;
    }
    .kanban-wrapper::-webkit-scrollbar-thumb {
        background: #c1c4d6; 
        border-radius: 4px;
    }
    .kanban-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a5a8be; 
    }
.kanban-wrapper {
    overflow-x: auto;
    padding-bottom: 20px;
}
.kanban-wrapper::-webkit-scrollbar {
    height: 8px;
}
.kanban-wrapper::-webkit-scrollbar-thumb {
    background: #dcdfe6;
    border-radius: 4px;
}
.kanban-task-card {
    transition: all 0.2s ease;
    border: 1px solid transparent !important;
}
.kanban-task-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    border-color: rgba(0,0,0,0.05) !important;
}
.sortable-ghost {
    opacity: 0.4;
    background-color: #f8f9fa;
    border: 2px dashed #696cff !important;
}
.sortable-drag {
    cursor: grabbing !important;
}
</style>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ==========================================
    // ADD THIS: MOUSE DRAG TO SCROLL LOGIC
    // ==========================================
    const slider = document.querySelector('.kanban-wrapper');
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
        // Prevent background scrolling if they are clicking a task card or button!
        if(e.target.closest('.kanban-task-card') || e.target.closest('a') || e.target.closest('button')) {
            return;
        }
        
        isDown = true;
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
        isDown = false;
    });

    slider.addEventListener('mouseup', () => {
        isDown = false;
    });

    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 1.5; // Multiply by 1.5 to scroll slightly faster than the mouse moves
        slider.scrollLeft = scrollLeft - walk;
    });
    // 1. Select all the columns where tasks can be dropped
    const kanbanColumns = document.querySelectorAll('.kanban-list');

    // 2. Loop through each column and make it a "Sortable" area
    kanbanColumns.forEach(column => {
        new Sortable(column, {
            group: 'shared', // This allows dragging BETWEEN different columns
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            
            // 3. What happens when you drop the card?
            onEnd: function (evt) {
                const itemEl = evt.item;  // The task card HTML element
                const toColumn = evt.to;  // The column it was dropped into
                
                // Only trigger if it was actually moved to a DIFFERENT column
                if (evt.from !== toColumn) {
                    const taskId = itemEl.getAttribute('data-task-id');
                    const taskType = itemEl.getAttribute('data-task-type');
                    const newStatus = toColumn.getAttribute('data-status');

                    // Send the update to the database in the background
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
                            // Update the little number badges dynamically
                            updateColumnBadges();
                            
                            // Show a quick success toast
                            const alertBox = document.getElementById('kanban-alert');
                            alertBox.classList.remove('d-none');
                            setTimeout(() => { alertBox.classList.add('d-none'); }, 2500);
                        } else {
                            alert('Failed to update status. Reverting.');
                            window.location.reload(); // Quick reset if it fails
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.reload();
                    });
                }
            },
        });
    });

    // Helper function to recount the tasks in each column and update the pill badge
    function updateColumnBadges() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            const taskCount = col.querySelectorAll('.kanban-task-card').length;
            col.querySelector('.column-badge').textContent = taskCount;
        });
    }
});
</script>
@endsection
@endsection