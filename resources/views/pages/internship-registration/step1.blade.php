@extends('layouts.blankLayout')

@section('content')

<div class="container-fluid px-3">
    <div class="w-100" style="background-image: url('{{ asset('images/bg.jpg') }}'); background-size: cover; min-height: 100vh;">

        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card mt-4">
                    <div class="card-body">

                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/branding/logo.png') }}"
                                 class="img-fluid"
                                 style="max-width: 160px;">
                        </div>

                        <h4 class="text-center">Internship Registration Form</h4>

                        <div class="mb-4">
                            <div class="progress" style="height: 20px;">
                                <div id="progressBar"
                                     class="progress-bar progress-bar-striped progress-bar-animated"
                                     style="width: 0%">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-2 text-center">
                                <div class="w-100">
                                    <small class="fw-bold text-primary">Registration</small>
                                </div>
                                <div class="w-100">
                                    <small>Assessment Test</small>
                                </div>
                                <div class="w-100">
                                    <small>Choose Plan</small>
                                </div>
                            </div>
                        </div>

<form id="registerForm" action="{{ route('intern.register.postStep1') }}" method="POST">
            @csrf

             <!-- ✅ ADD THIS - DISPLAY ALL ERRORS -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> Please fix the following issues:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control form-control-lg" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <select name="country" class="form-control form-control-lg" required>
                                        <option value="">Select Country</option>
                                        <option value="AF">Afghanistan</option>
                                        <option value="PK">Pakistan</option>
                                        <option value="IN">India</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="CA">Canada</option>
                                        <option value="AU">Australia</option>
                                        <option value="DE">Germany</option>
                                        <option value="FR">France</option>
                                        <option value="AE">UAE</option>
                                        <option value="SA">Saudi Arabia</option>
                                        <option value="CN">China</option>
                                        <option value="JP">Japan</option>
                                        <option value="TR">Turkey</option>
                                        <option value="BD">Bangladesh</option>
                                        <option value="MY">Malaysia</option>
                                        <option value="ID">Indonesia</option>
                                        <option value="IT">Italy</option>
                                        <option value="ES">Spain</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city"  class="form-control form-control-lg" required>
                                </div>
                            </div>

                            <div class="row mt-3">

                                <div class="col-md-6">
                                    <label class="form-label">WhatsApp Number</label>
                                    <input type="tel" name="whatsapp" id="whatsapp" class="form-control form-control-lg" required>
                                    <small id="whatsappError" class="text-danger d-none">
                                        Invalid WhatsApp number
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-control form-control-lg" required>
                                        <option value="">Select Gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Profile Image (Optional)</label>
                                    <input type="file" name="profile_image" class="form-control form-control-lg">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Join Date</label>
                                    <input type="date" name="join_date" class="form-control form-control-lg" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" id="dob" class="form-control form-control-lg" required>
                                    <small id="ageError" class="text-danger d-none">
                                        Minimum age is 15 years
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">University</label>
                                    <select name="university" class="form-control form-control-lg" required>
                                        <option value="">Select University</option>
                                        <option>COMSATS University Islamabad</option>
                                        <option>NUST</option>
                                        <option>FAST-NUCES</option>
                                        <option>University of Punjab</option>
                                        <option>UET Lahore</option>
                                        <option>UET Taxila</option>
                                        <option>Bahria University</option>
                                        <option>Air University</option>
                                        <option>Virtual University</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Interview Type</label>
                                    <select name="interview_type" class="form-control form-control-lg" required>
                                        <option value="">Select Type</option>
                                        <option>Onsite</option>
                                        <option>Remote</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Technology</label>
                                    <select name="technology" id="techSelect" class="form-control form-control-lg" required>
                                        <option value="">Select Technology</option>
                                        <option>MERN Stack</option>
                                        <option>Front-End Development</option>
                                        <option>Backend (Laravel/PHP)</option>
                                        <option>AI & ML</option>
                                        <option>Data Science</option>
                                        <option>UI/UX</option>
                                        <option>Cyber Security</option>
                                        <option>DevOps</option>
                                        <option value="other">Other</option>
                                    </select>

                                    <input type="text" name="custom_technology" id="customTech"
                                           class="form-control form-control-lg mt-2"
                                           placeholder="Enter Technology"
                                           style="display:none;">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Duration</label>
                                    <select name="duration" class="form-control form-control-lg" required>
                                        <option value="">Select Duration</option>
                                        <option>1 Month</option>
                                        <option>2 Month</option>
                                        <option>3 Month</option>
                                        <option>6 Month</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Internship Type</label>
                                    <select name="internship_type" class="form-control form-control-lg" required>
                                        <option value="">Select Type</option>
                                        <option>Onsite</option>
                                        <option>Remote</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 mb-3">
                                <button type="submit" class="btn btn-primary w-100 btn-lg">
                                    Register
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

<style>
  /* Dropdown background */
/* select.form-control {
    background-color: #000 !important;
    color: #fff !important;
} */

/* Dropdown options (limited browser support but works in most modern ones) */
select.form-control option {
    background-color: #000;
    color: #fff;
}
.iti {
    width: 100%;
}
.iti input {
    width: 100% !important;
    padding-left: 82px !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>
const form = document.getElementById('registerForm');
const inputs = form.querySelectorAll('input, select');
const progressBar = document.getElementById('progressBar');

const whatsappInput = document.getElementById("whatsapp");
const dob = document.getElementById("dob");

const ageError = document.getElementById("ageError");
const whatsappError = document.getElementById("whatsappError");

const iti = window.intlTelInput(whatsappInput, {
    initialCountry: "pk",
    separateDialCode: true,
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
});

function getAge(dobValue) {
    let birthDate = new Date(dobValue);
    let today = new Date();

    let age = today.getFullYear() - birthDate.getFullYear();
    let m = today.getMonth() - birthDate.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }

    return age;
}

function updateProgress() {
    let filled = 0;

    inputs.forEach(input => {
        if (input.type === "file") return;

        if (input.value && input.value.trim() !== "") {
            filled++;
        }
    });

    let total = inputs.length - 1;
    let progress = (filled / total) * 33;

    progressBar.style.width = progress + "%";
}

form.addEventListener('submit', function(e) {
    e.preventDefault();

    let age = getAge(dob.value);
    if (age < 15) {
        dob.focus();
        ageError.classList.remove("d-none");
        return;
    } else {
        ageError.classList.add("d-none");
    }

    if (!iti.isValidNumber()) {
        whatsappInput.focus();
        whatsappError.classList.remove("d-none");
        return;
    } else {
        whatsappError.classList.add("d-none");
    }

    form.submit();
});

document.getElementById('techSelect').addEventListener('change', function() {
    if (this.value === 'other') {
        customTech.style.display = 'block';
    } else {
        customTech.style.display = 'none';
        customTech.value = '';
    }
});

inputs.forEach(input => {
    input.addEventListener('input', updateProgress);
    input.addEventListener('change', updateProgress);
});

updateProgress();
</script>

@endsection