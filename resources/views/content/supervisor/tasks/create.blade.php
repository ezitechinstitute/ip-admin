@extends('layouts/layoutMaster')

@section('title', 'Create Task')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Create New Task</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('supervisor.tasks.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Assign To Interns (Multi-select)</label>
                        <select name="eti_ids[]" class="form-select select2" multiple required>
                            @foreach($interns as $intern)
                                <option value="{{ $intern->eti_id }}">{{ $intern->name }} ({{ $intern->eti_id }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Task Title</label>
                    <input type="text" name="task_title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Task Description</label>
                    <textarea name="task_description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="task_start" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="task_end" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Task Points / Total Marks</label>
                        <input type="number" name="task_points" class="form-control" value="10" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Assign Task</button>
                    <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
