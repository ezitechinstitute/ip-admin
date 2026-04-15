@extends('layouts.blankLayout')

@section('content')

<div class="container-fluid px-3">
    <div class="w-100" style="background-image: url('{{ asset('images/bg.jpg') }}'); background-size: cover; min-height: 100vh;">

        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card mt-4">
                    <div class="card-body">

                        <!-- Logo -->
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/branding/logo.png') }}"
                                 class="img-fluid"
                                 style="max-width: 160px;">
                        </div>

                        <h4 class="text-center">Welcome to Internship Registration Form</h4>

                        <!-- Progress -->
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

                        <!-- FORM -->
                        <form id="registerForm" action="{{ route('intern.register.step2') }}" method="GET">

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Country</label>
                                    <select class="form-control" required>
                                        <option value="">--Select Country--</option>
                                        <option>Pakistan</option>
                                        <option>India</option>
                                        <option>United States</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                               <div class="col-md-6">
    <label>WhatsApp Number</label>
    <input type="tel" id="whatsapp" class="form-control" required>

    <small id="whatsappError" class="text-danger d-none">
        Enter a valid WhatsApp number
    </small>
</div>

                                <div class="col-md-6">
                                    <label>Gender</label>
                                    <select class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Profile Image (Optional)</label>
                                    <input type="file" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Join Date</label>
                                    <input type="date" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Date of Birth</label>
                                    <input type="date" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>University</label>
                                    <select class="form-control" required>
                                        <option value="">--Select--</option>
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
                                    <label>Interview Type</label>
                                    <select class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option>Onsite</option>
                                        <option>Remote</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Technology</label>
                                    <select id="techSelect" class="form-control" required>
                                        <option value="">--Select--</option>
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

                                    <input type="text" id="customTech"
                                           class="form-control mt-2"
                                           placeholder="Enter Technology"
                                           style="display:none;">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Duration</label>
                                    <select class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option>1 Month</option>
                                        <option>2 Month</option>
                                        <option>3 Month</option>
                                        <option>6 Month</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Internship Type</label>
                                    <select class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option>Onsite</option>
                                        <option>Remote</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
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

<!-- STYLES -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

<style>

.iti {
    width: 100%;
}

.iti--allow-dropdown input,
.iti--separate-dial-code input {
    padding-left: 88px !important;
    padding-right: 52px !important;
}

.iti__country {
    color: #000;
}
</style>

<!-- SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>

<script>

const whatsappInput = document.querySelector("#whatsapp");

const iti = window.intlTelInput(whatsappInput, {
    initialCountry: "pk",
    separateDialCode: true,
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"
});
const form = document.getElementById('registerForm');
const inputs = form.querySelectorAll('input, select');
const progressBar = document.getElementById('progressBar');
const techSelect = document.getElementById('techSelect');
const customTech = document.getElementById('customTech');

// PROGRESS
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

// VALIDATION
form.addEventListener('submit', function(e) {
    e.preventDefault();

    let firstInvalid = null;

    inputs.forEach(input => {
        if (input.type === "file") return;

        if (input === customTech && customTech.style.display === "none") return;

        if (!input.value || input.value.trim() === "") {
            if (!firstInvalid) firstInvalid = input;
        }
    });

    if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.classList.add('border-danger');

        setTimeout(() => {
            firstInvalid.classList.remove('border-danger');
        }, 2000);

        return;
    }

    form.submit();
});

// TECH HANDLER
techSelect.addEventListener('change', function() {
    if (this.value === 'other') {
        customTech.style.display = 'block';
    } else {
        customTech.style.display = 'none';
        customTech.value = '';
    }
});

// LISTENERS
inputs.forEach(input => {
    input.addEventListener('input', updateProgress);
    input.addEventListener('change', updateProgress);
});

updateProgress();
</script>

@endsection