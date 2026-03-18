@extends('layouts/layoutMaster')

@section('title', 'Profile Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Profile Settings</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Supervisor Profile</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" value="{{ $profile->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $profile->email }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" class="form-control" value="{{ $profile->phone }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="{{ $profile->role }}" readonly>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection