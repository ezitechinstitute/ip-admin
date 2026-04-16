@extends('layouts.blankLayout')

@section('content')

<div class="container-fluid px-3">
    <div class="w-100" style="background-image: url('{{ asset('images/bg.jpg') }}'); background-size: cover; min-height: 100vh;">

        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card mt-4 mb-4">
                    <div class="card-body">

                        <!-- LOGO -->
                        <div class="text-center mb-3">
                            <img src="{{ asset('assets/img/branding/logo.png') }}"
                                 class="img-fluid"
                                 style="max-width: 140px;">
                        </div>

                        <!-- TITLE -->
                        <h4 class="text-center mb-1">Skill Assessment</h4>
                        <p class="text-center text-muted mb-4">
                            Answer the following questions so we can recommend the most suitable internship program.
                        </p>

                        <!-- PROGRESS BAR -->
                        <div class="mb-4">
                            <div class="progress" style="height: 18px;">
                                <div id="progressBar"
                                     class="progress-bar progress-bar-striped progress-bar-animated"
                                     style="width: 33%;">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-2 text-center">
                                <div class="w-100">
                                    <small class="text-success fw-bold">Registration</small>
                                </div>
                                <div class="w-100">
                                    <small class="fw-bold text-primary">Assessment</small>
                                </div>
                                <div class="w-100">
                                    <small>Choose Plan</small>
                                </div>
                            </div>
                        </div>

                        <!-- FORM -->
                        <form id="assessmentForm" action="{{ route('intern.register.step3') }}" method="POST">
                        @csrf

                        @php
                        $questions = [
                            "How would you describe your current skill level?" => [
                                "I am a complete beginner",
                                "I know basic development concepts",
                                "I can build small projects myself",
                                "I have built multiple projects"
                            ],
                            "Have you worked on any real projects before?" => [
                                "No",
                                "Only academic projects",
                                "Personal projects",
                                "Freelance or client projects"
                            ],
                            "How comfortable are you with solving development problems?" => [
                                "I struggle with most problems",
                                "I can solve simple problems",
                                "I can solve intermediate problems",
                                "I solve problems confidently"
                            ],
                            "What type of support do you expect during the internship?" => [
                                "I need a teacher and full guidance",
                                "I need some guidance but mostly practice",
                                "I prefer self-learning with project experience",
                                "I only need a professional work environment"
                            ],
                            "How many hours per week can you dedicate?" => [
                                "5 hours",
                                "10 hours",
                                "15 hours",
                                "20+ hours"
                            ],
                            "What is your main goal for joining this internship?" => [
                                "Learn development from scratch",
                                "Improve my development skills",
                                "Build projects for my portfolio",
                                "Gain real industry exposure"
                            ]
                        ];
                        @endphp

                        @foreach($questions as $q => $options)
                            <div class="mb-4 question-block">

                                <label class="fw-bold mb-3 d-block">
                                    {{ $loop->iteration }}. {{ $q }}
                                </label>

                                <div class="row">
                                    @foreach($options as $index => $option)
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 mb-3 option-box d-flex align-items-center">
                                                <input class="form-check-input mcq-radio me-3"
                                                       type="radio"
                                                       name="q{{ $loop->parent->iteration }}"
                                                       value="{{ $index }}">

                                                <label class="form-check-label w-100">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        @endforeach

                        <!-- BUTTONS -->
                        <div class="d-flex gap-2 mt-4">

                            <a href="{{ route('intern.register.step1') }}" class="btn btn-outline-secondary w-50">
                                Back
                            </a>

                            <button type="submit" class="btn btn-primary w-50" id="submitBtn">
                                Next → View Recommendation
                            </button>

                        </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- 🔥 FULL SCREEN LOADER -->
<div id="loaderOverlay" class="d-none">
    <div class="loader-content text-center">
        <div class="spinner-border text-primary mb-3"></div>
        <p>
            Analyzing your answers and preparing the best internship path for you...
        </p>
    </div>
</div>

<!-- STYLES -->
<style>
.option-box {
    cursor: pointer;
    transition: 0.2s ease;
    display: flex;
    align-items: center;
    gap: 22px;
}

.option-box:hover {
    transform: scale(1.02);
    border-color: #0d6efd;
}

.form-check-input {
    transform: scale(1.2);
    margin: 0;
    flex-shrink: 0;
}

/* 🔥 LOADER OVERLAY */
#loaderOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    backdrop-filter: blur(6px);
    background: rgba(0,0,0,0.4);

    display: flex;
    justify-content: center;
    align-items: center;

    z-index: 9999;
}

.loader-content {
    background: #fff;
    padding: 25px 35px;
    border-radius: 10px;
}
</style>

<!-- SCRIPT -->
<script>
const form = document.getElementById('assessmentForm');
const progressBar = document.getElementById('progressBar');
const submitBtn = document.getElementById('submitBtn');

const totalQuestions = 6;

// Progress
function updateProgress() {
    let answered = 0;

    for (let i = 1; i <= totalQuestions; i++) {
        if (document.querySelector(`input[name="q${i}"]:checked`)) {
            answered++;
        }
    }

    let progress = 33 + ((answered / totalQuestions) * 33);
    progressBar.style.width = progress + "%";
}

// Selection UI
document.querySelectorAll('.option-box').forEach(box => {
    const radio = box.querySelector('input');

    box.addEventListener('click', () => {
        const group = radio.name;

        document.querySelectorAll(`input[name="${group}"]`).forEach(r => {
            r.closest('.option-box').classList.remove('border-primary', 'bg-light');
        });

        radio.checked = true;
        box.classList.add('border-primary', 'bg-light');

        updateProgress();
    });
});

// Submit control
form.addEventListener('submit', function(e) {
    e.preventDefault();

    let firstUnanswered = null;

    for (let i = 1; i <= totalQuestions; i++) {
        let selected = document.querySelector(`input[name="q${i}"]:checked`);

        if (!selected) {
            firstUnanswered = document.querySelector(`input[name="q${i}"]`).closest('.question-block');
            break;
        }
    }

    if (firstUnanswered) {
        firstUnanswered.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        firstUnanswered.classList.add('border', 'border-danger', 'p-2');

        setTimeout(() => {
            firstUnanswered.classList.remove('border', 'border-danger', 'p-2');
        }, 2000);

        return;
    }

    // 🔥 SHOW OVERLAY
    document.getElementById('loaderOverlay').classList.remove('d-none');
    submitBtn.disabled = true;

    setTimeout(() => {
        form.submit();
    }, 2500);
});

updateProgress();
</script>

@endsection