@extends('layouts.app')
@section('content')
<div class="container">
    <div class="progress-bar mb-4">
        <div class="progress-step active">Step 1</div>
        <div class="progress-step">Step 2</div>
        <div class="progress-step">Step 3</div>
    </div>
    <h2>Ezitech Internship Application</h2>
    <p>Complete this quick assessment to find the best internship path for you.</p>
    <small>This assessment takes about 3–4 minutes and helps us recommend the best internship program based on your skills.</small>
    <form method="POST" action="{{ route('internship.step2') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Country</label>
            <input type="text" name="country" class="form-control" required>
        </div>
        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="form-group">
            <label>WhatsApp Number</label>
            <input type="text" name="whatsapp" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
        </div>
        <div class="form-group">
            <label>University / Institute</label>
            <input type="text" name="university" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Technology Interest</label>
            <select name="technology" class="form-control" required>
                <option value="MERN Stack">MERN Stack</option>
                <option value="Frontend Development">Frontend Development</option>
                <option value="Backend Development">Backend Development</option>
                <option value="Python Development">Python Development</option>
                <option value="UI/UX Design">UI/UX Design</option>
            </select>
        </div>
        <div class="form-group">
            <label>Profile Image (optional)</label>
            <input type="file" name="profile_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Next → Skill Assessment</button>
    </form>
</div>
@endsection