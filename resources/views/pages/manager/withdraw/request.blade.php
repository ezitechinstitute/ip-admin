@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Withdraw Request')

@section('content')

<div class="row">
<div class="col-xl-8">

<div class="card">
<div class="card-header">
<h5 class="card-title">Submit Withdraw Request</h5>
</div>

<div class="card-body">

    @if ($errors->any())
<div class="alert alert-danger">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('manager.withdraw.store') }}">
@csrf

<div class="mb-3">
<label class="form-label">Bank Name</label>
<input type="text" name="bank" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Account Number</label>
<input type="text" name="ac_no" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Account Holder Name</label>
<input type="text" name="ac_name" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Amount</label>
<input type="number" name="amount" class="form-control" required>
</div>

<div class="mb-3">
<label>Period</label>
<input type="text" name="period" class="form-control" placeholder="Example: Feb 2026">
</div>

<div class="mb-3">
<label class="form-label">Description</label>
<textarea name="description" class="form-control"></textarea>
</div>


<button type="submit" class="btn btn-primary">
Submit Withdraw Request
</button>

</form>

</div>
</div>

</div>
</div>

@endsection