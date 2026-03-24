@extends('layouts.layoutMaster')

@section('content')

<h4>Certificate Requests</h4>

<table class="table">

<thead>
<tr>
<th>Intern</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($requests as $req)

<tr>

<td>{{ $req->intern->name }}</td>

<td>{{ $req->status }}</td>

<td>

<form method="POST"
action="{{ route('manager.certificate.approve',$req->id) }}">

@csrf

<button class="btn btn-success">
Approve
</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

@endsection