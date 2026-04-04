@extends('layouts/layoutMaster')

@section('title', 'Certificate Requests')

@section('content')
<div class="col-12 mb-6"><h4 class="mt-6 mb-1">Certificate Requests</h4></div>
@if($errors->any())
@foreach($errors->all() as $error)
<div class="alert alert-danger">{{ $error }}</div>
@endforeach
@endif
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="card"><div class="table-responsive"><table class="table"><thead><tr><th>#</th><th>Request ID</th><th>Intern</th><th>Email</th><th>Type</th><th>Status</th><th>Created</th><th>Action</th></tr></thead><tbody>
@forelse($requests as $key => $req)
<tr>
<td>{{ $requests->firstItem() + $key }}</td>
<td>{{ $req->certificate_request_id }}</td>
<td>{{ $req->intern_name }}</td>
<td>{{ $req->email }}</td>
<td>{{ $req->certificate_type == 'internship' ? 'Internship' : 'Course Completion' }}</td>
<td><span class="badge {{ $req->status == 'approved' ? 'bg-success' : ($req->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">{{ ucfirst($req->status) }}</span></td>
<td>{{ $req->created_at }}</td>
<td>
@if($req->status === 'pending')
<form method="POST" action="{{ route('manager.certificate.request.update-status') }}" style="display:inline;">
@csrf
<input type="hidden" name="id" value="{{ $req->id }}">
<input type="hidden" name="status" value="approved">
<input type="hidden" name="certificate_type" value="{{ $req->certificate_type }}">
<button class="btn btn-sm btn-success" type="submit">Approve</button>
</form>
<button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">Reject</button>
@elseif($req->status === 'approved')
<a class="btn btn-sm btn-primary" href="{{ route('manager.certificate.request.download', ['id' => $req->id]) }}">Download</a>
@else
<span class="text-muted">No actions</span>
@endif
</td>
</tr>
<tr><td colspan="8"><small>Reason: {{ $req->reason ?? 'N/A' }}</small></td></tr>

<div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>Reject Reason</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('manager.certificate.request.update-status') }}">@csrf
<input type="hidden" name="id" value="{{ $req->id }}"><input type="hidden" name="status" value="rejected"><div class="mb-3"><label>Certificate Type</label><select name="certificate_type" class="form-control"><option value="internship" {{ $req->certificate_type == 'internship' ? 'selected' : '' }}>Internship</option><option value="course_completion" {{ $req->certificate_type == 'course_completion' ? 'selected' : '' }}>Course Completion</option></select></div>
<div class="mb-3"><label>Reason</label><textarea required class="form-control" name="reason" rows="3">{{ $req->reason }}</textarea></div>
<div class="text-end"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button> <button class="btn btn-danger" type="submit">Reject</button></div></form></div></div></div></div>

@empty
<tr><td colspan="8" class="text-center">No certificate requests yet.</td></tr>
@endforelse
</tbody></table></div><div class="card-footer">{{ $requests->links() }}</div></div>

@endsection