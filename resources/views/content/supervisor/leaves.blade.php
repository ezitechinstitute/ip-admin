@extends('layouts/layoutMaster')

@section('title', 'Leaves')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Leaves</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Leave List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supervisor</th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
@forelse($leaves as $index => $leave)
  <tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $leave->supervisor_id }}</td>
    <td>{{ $leave->leave_type }}</td>
    <td>{{ $leave->from_date }}</td>
    <td>{{ $leave->to_date }}</td>
    <td>{{ $leave->status }}</td>
  </tr>
@empty
  <tr>
    <td colspan="6" class="text-center">No leaves found</td>
  </tr>
@endforelse
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection