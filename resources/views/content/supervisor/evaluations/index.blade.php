@extends('layouts/layoutMaster')

@section('title', 'Evaluation Records')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Intern Monthly Evaluations</h4>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Intern</th>
                            <th>Month</th>
                            <th>Overall Score</th>
                            <th>Technical</th>
                            <th>Problem Solving</th>
                            <th>Communication</th>
                            <th>Task Completion</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if($evaluations->count())
                            @foreach($evaluations as $eval)
                                <tr>
                                    <td>{{ $eval->intern_name }} ({{ $eval->eti_id }})</td>

                                    <td>{{ $eval->month }}</td>

                                    <td>
                                        <span class="badge bg-label-primary">
                                            {{ $eval->overall_score }}/10
                                        </span>
                                    </td>

                                    <td>{{ $eval->technical_skills }}/10</td>
                                    <td>{{ $eval->problem_solving }}/10</td>
                                    <td>{{ $eval->communication }}/10</td>
                                    <td>{{ $eval->task_completion }}/10</td>

                                    <td>{{ date('Y-m-d', strtotime($eval->created_at)) }}</td>

                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('supervisor.viewIntern', $eval->eti_id) }}" class="btn btn-sm btn-label-secondary">
                                                Profile
                                            </a>

                                            <a href="{{ route('supervisor.evaluations.edit', $eval->id) }}" class="btn btn-sm btn-label-info">
                                                Edit
                                            </a>

                                            <form action="{{ route('supervisor.evaluations.delete', $eval->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this evaluation?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-label-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="text-center">No evaluations recorded yet</td>
                            </tr>
                        @endif
                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $evaluations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection