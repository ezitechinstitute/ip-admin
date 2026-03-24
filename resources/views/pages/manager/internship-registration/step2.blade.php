@extends('layouts.app')
@section('content')
<div class="container">
    <div class="progress-bar mb-4">
        <div class="progress-step completed">Step 1</div>
        <div class="progress-step active">Step 2</div>
        <div class="progress-step">Step 3</div>
    </div>
    <h2>Skill Assessment</h2>
    <p>Answer the following questions so we can recommend the most suitable internship program.</p>
    <form method="POST" action="{{ route('internship.step3') }}">
        @csrf
        <div class="form-group">
            <label>How would you describe your current skill level?</label>
            <select name="skill_level" class="form-control" required>
                <option value="0">I am a complete beginner</option>
                <option value="1">I know basic development concepts</option>
                <option value="2">I can build small projects myself</option>
                <option value="3">I have built multiple projects</option>
            </select>
        </div>
        <div class="form-group">
            <label>Have you worked on any real projects before?</label>
            <select name="real_projects" class="form-control" required>
                <option value="0">No</option>
                <option value="1">Only academic projects</option>
                <option value="2">Personal projects</option>
                <option value="3">Freelance or client projects</option>
            </select>
        </div>
        <div class="form-group">
            <label>How comfortable are you with solving development problems?</label>
            <select name="problem_solving" class="form-control" required>
                <option value="0">I struggle with most problems</option>
                <option value="1">I can solve simple problems</option>
                <option value="2">I can solve intermediate problems</option>
                <option value="3">I solve problems confidently</option>
            </select>
        </div>
        <div class="form-group">
            <label>What type of support do you expect during the internship?</label>
            <select name="support_expectation" class="form-control" required>
                <option value="0">I need a teacher and full guidance</option>
                <option value="1">I need some guidance but mostly practice</option>
                <option value="2">I prefer self-learning with project experience</option>
                <option value="3">I only need a professional work environment</option>
            </select>
        </div>
        <div class="form-group">
            <label>How many hours per week can you dedicate to this internship?</label>
            <select name="hours_per_week" class="form-control" required>
                <option value="0">5 hours</option>
                <option value="1">10 hours</option>
                <option value="2">15 hours</option>
                <option value="3">20+ hours</option>
            </select>
        </div>
        <div class="form-group">
            <label>What is your main goal for joining this internship?</label>
            <select name="main_goal" class="form-control" required>
                <option value="0">Learn development from scratch</option>
                <option value="1">Improve my development skills</option>
                <option value="2">Build projects for my portfolio</option>
                <option value="3">Gain real industry exposure</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Next → View Recommendation</button>
    </form>
</div>
@endsection