@extends('layouts/layoutMaster')

@section('title', 'Add Curriculum')

@section('content')
<div class="container-fluid px-4 py-3">
    <h4 class="mb-4">Add Technology Curriculum</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('manager.curriculum.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="tech_id" class="form-label">Technology</label>
                    <select name="tech_id" id="tech_id" class="form-select" required>
                        <option value="">Select technology</option>
                        @foreach($technologies as $technology)
                            <option value="{{ $technology->tech_id }}" {{ old('tech_id') == $technology->tech_id ? 'selected' : '' }}>
                                {{ $technology->technology }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="curriculum_name">Curriculum Name</label>
                    <input type="text" name="curriculum_name" value="{{ old('curriculum_name') }}" class="form-control" id="curriculum_name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_projects">Total Projects</label>
                        <input type="number" name="total_projects" value="{{ old('total_projects', 0) }}" class="form-control" id="total_projects" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_duration_weeks">Duration (weeks)</label>
                        <input type="number" name="total_duration_weeks" value="{{ old('total_duration_weeks', 0) }}" class="form-control" id="total_duration_weeks" min="0" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('manager.curriculum.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Curriculum</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
