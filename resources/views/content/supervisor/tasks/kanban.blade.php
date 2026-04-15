@extends('layouts/layoutMaster')

@section('title', 'Task Kanban')

{{-- 1. MAIN CONTENT SECTION --}}
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <h4 class="mb-0">Task Kanban Board</h4>
        
        <div class="d-flex align-items-stretch gap-2">
            <select id="internFilter" class="form-select shadow-sm border-primary" style="min-width: 250px;">
                <option value="all">-- All Interns --</option>
                @foreach($interns as $intern)
                    <option value="{{ $intern->eti_id }}">{{ $intern->name }}</option>
                @endforeach
            </select>
            
            <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary shadow-sm text-nowrap d-flex align-items-center">
                <i class="ti tabler-plus me-1"></i> Create Task
            </a>
        </div>
    </div>

    <div id="kanban-alert" class="alert alert-success d-none shadow-sm" style="position: fixed; top: 20px; right: 20px; z-index: 9999;" role="alert">
        Status updated successfully!
    </div>

    <div class="kanban-wrapper">
        <div class="d-flex flex-nowrap" style="gap: 1.5rem; padding-bottom: 20px;">
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
                            <span class="badge {{ $status == 'Pending' ? 'bg-dark text-white' : 'bg-white text-dark' }} rounded-pill column-badge">0</span>
                        </div>
                        
                        <div class="card-body p-2 bg-light bg-opacity-10 kanban-list" data-status="{{ $status }}" style="min-height: 500px;">
                            {{-- Tasks inject here --}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection 
{{-- 🔥 THE MISSING ENDSECTION THAT BROKE YOUR LAYOUT IS ADDED ABOVE 🔥 --}}


{{-- 2. STYLES SECTION --}}
@section('page-style')
<style>
    /* Add this inside your <style> block */
.expired-card { 
    background-color: #fff0f0 !important; /* Soft red tint */
    opacity: 0.85; /* Makes it look slightly faded/inactive */
    border: 1px solid #ffdada !important;
}
.expired-card:hover {
    opacity: 1; /* Brightens up when hovered */
}
.kanban-wrapper { overflow-x: auto; cursor: grab; }
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
@endsection


{{-- 3. SCRIPTS SECTION --}}
@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const filterSelect = document.getElementById('internFilter');
    
    if (filterSelect) {
        filterSelect.addEventListener('change', function(e) {
            const etiId = e.target.value;

            document.querySelectorAll('.kanban-list').forEach(list => list.innerHTML = '');
            updateColumnBadges();

            // if (etiId === 'all') return; 

            fetch(`{{ route('supervisor.tasks.fetchKanbanAjax') }}?eti_id=${etiId}`)
                .then(response => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.json();
                })
                .then(data => {
                    data.tasks.forEach(task => {
                        const column = document.querySelector(`.kanban-list[data-status="${task.status}"]`);
                        if (!column) return;

                        // 1. Determine Card Styling & Badges based on Status
                        let cardClass = 'border-0';
                        let customBadge = '';
                        let dateText = `<small class="text-muted fw-semibold">${task.end_date}</small>`;

                        if (task.status === 'Expired') {
                            // Expired Task Styling (Solid Red, Faded)
                            cardClass = 'border-danger expired-card';
                            customBadge = '<span class="badge bg-danger shadow-sm" style="font-size: 0.65rem;"><i class="ti tabler-ban ti-xs"></i> EXPIRED</span>';
                            dateText = `<small class="text-danger fw-bold"><i class="ti tabler-calendar-time ti-xs"></i> ${task.end_date}</small>`;
                        } 
                        else if (task.is_overdue) {
                            // Overdue Task Styling (Active, Pulsing)
                            cardClass = 'border-danger border-2 overdue-glow';
                            customBadge = '<span class="badge bg-danger pulse-danger shadow-sm" style="font-size: 0.65rem;"><i class="ti tabler-alert-triangle ti-xs"></i> OVERDUE</span>';
                            dateText = `<small class="text-danger fw-bold"><i class="ti tabler-calendar-time ti-xs"></i> ${task.end_date}</small>`;
                        }

                        const badgeType = task.type === 'project' ? '<span class="badge bg-label-info" style="font-size: 0.65rem;">Project Task</span>' : '<span class="badge bg-label-primary" style="font-size: 0.65rem;">Standard Task</span>';
                        
                        // 2. Determine Action Buttons
                        let actionButtons = task.type === 'project' 
                            ? `<a href="${task.edit_url}" class="btn btn-xs btn-outline-secondary py-1 px-2">Edit</a>`
                            : `<a href="${task.review_url}" class="btn btn-xs btn-primary py-1 px-2">Review</a>
                               <a href="${task.edit_url}" class="btn btn-xs btn-outline-secondary py-1 px-2">Edit</a>`;

                        // 3. Build the HTML Card
                        const cardHtml = `
                            <div class="card mb-2 shadow-sm kanban-task-card ${cardClass}" data-task-id="${task.id}" data-task-type="${task.type}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>${badgeType}</div>
                                        ${customBadge}
                                    </div>
                                    <h6 class="card-title mb-2 fw-bold" style="white-space: normal; line-height: 1.4;">${task.title}</h6>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-label-secondary small">${task.intern_name}</span>
                                        ${dateText}
                                    </div>
                                    <div class="d-flex justify-content-end gap-1 border-top pt-2">
                                        ${actionButtons}
                                    </div>
                                </div>
                            </div>
                        `;
                        column.insertAdjacentHTML('beforeend', cardHtml);
                    });
                    updateColumnBadges();
                })
                .catch(error => console.error("AJAX Error:", error));
        });

        // Trigger change on load to fetch tasks immediately
        filterSelect.dispatchEvent(new Event('change'));
    }

    function updateColumnBadges() {
        document.querySelectorAll('.kanban-column').forEach(col => {
            const taskCount = col.querySelectorAll('.kanban-task-card').length;
            col.querySelector('.column-badge').textContent = taskCount;
        });
    }

    const slider = document.querySelector('.kanban-wrapper');
    let isDown = false; let startX; let scrollLeft;
    if (slider) {
        slider.addEventListener('mousedown', (e) => {
            if(e.target.closest('.kanban-task-card') || e.target.closest('button') || e.target.closest('a') || e.target.closest('select')) return;
            isDown = true; slider.style.cursor = 'grabbing';
            startX = e.pageX - slider.offsetLeft; scrollLeft = slider.scrollLeft;
        });
        slider.addEventListener('mouseleave', () => { isDown = false; slider.style.cursor = 'grab'; });
        slider.addEventListener('mouseup', () => { isDown = false; slider.style.cursor = 'grab'; });
        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return; e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            slider.scrollLeft = scrollLeft - (x - startX) * 2;
        });
    }

    if (typeof Sortable !== 'undefined') {
        document.querySelectorAll('.kanban-list').forEach(column => {
            new Sortable(column, {
                group: 'shared', animation: 150, ghostClass: 'sortable-ghost', dragClass: 'sortable-drag',
                onEnd: function (evt) {
                    const itemEl = evt.item; const toColumn = evt.to; 
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
                                const alertBox = document.getElementById('kanban-alert');
                                alertBox.classList.remove('d-none');
                                setTimeout(() => { alertBox.classList.add('d-none'); }, 2500);
                            } else {
                                alert('Failed to update status.'); window.location.reload(); 
                            }
                        }).catch(error => window.location.reload());
                    }
                },
            });
        });
    }
});
</script>
@endsection