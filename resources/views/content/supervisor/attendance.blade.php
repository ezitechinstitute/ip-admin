@extends('layouts/layoutMaster')

@section('title', 'Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Attendance</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Attendance List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
        <tbody>
@forelse($attendance as $index => $row)
  <tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $row->eti_id }}</td>
    <td>{{ \Carbon\Carbon::parse($row->start_shift)->format('Y-m-d') }}</td>
    <td>{{ $row->status }}</td>
    <td>{{ $row->start_shift }}</td>
    <td>{{ $row->end_shift ?? 'N/A' }}</td>
  </tr>
@empty
  <tr>
    <td colspan="6" class="text-center">No attendance found</td>
  </tr>
@endforelse
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection