@extends('layouts/layoutMaster')

@section('title', 'Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Feedback</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Feedback List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Feedback</th>
                            <th>Date</th>
                        </tr>
                    </thead>
      <tbody>
@forelse($feedbacks as $index => $feedback)
  <tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $feedback->name }}</td>
    <td>{{ $feedback->email }}</td>
    <td>{{ $feedback->feedback_text }}</td>
    <td>{{ $feedback->created_at }}</td>
  </tr>
@empty
  <tr>
    <td colspan="5" class="text-center">No feedback found</td>
  </tr>
@endforelse
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection