@extends('layouts.layoutMaster')

@section('content')

<h4>Certificate Templates</h4>

<form method="POST" action="{{ route('admin.certificate.template.store') }}" enctype="multipart/form-data">
@csrf

<div class="mb-3">
<label>Certificate Type</label>

<select name="type" class="form-control">

<option value="internship">Internship</option>
<option value="course">Course</option>

</select>
</div>

<div class="mb-3">
<label>Upload Template</label>

<input type="file" name="template_file" class="form-control">
</div>

<button class="btn btn-primary">Upload Template</button>

</form>


<hr>

<table class="table">

<thead>
<tr>
<th>ID</th>
<th>Type</th>
<th>File</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($templates as $template)

<tr>

<td>{{ $template->id }}</td>

<td>{{ $template->type }}</td>

<td>{{ $template->template_path }}</td>

<td>

<form method="POST" action="{{ route('admin.certificate.template.delete',$template->id) }}">
@csrf
@method('DELETE')

<button class="btn btn-danger">Delete</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

@endsection