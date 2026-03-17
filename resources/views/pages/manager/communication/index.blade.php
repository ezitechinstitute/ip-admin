@extends('layouts.layoutMaster')

@section('content')

<h4 class="mb-4">Communication Center</h4>

<form method="POST" action="{{ route('manager.send.message') }}">
    @csrf

    <div class="mb-3">
        <label>Send To</label>
        <select name="target_type" class="form-control" required>
            <option value="all_interns">All Interns</option>
            <option value="technology">Technology Group</option>
            <option value="supervisor" disabled>Supervisor (Comming Soon)</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Select Technology</label>
        <select name="target_value" class="form-control">
            <option value="">-- Select Technology --</option>

            @foreach($technologies as $tech)
                <option value="{{ $tech->tech_id }}">
                    {{ $tech->technology }}
                </option>
            @endforeach

        </select>
    </div>

    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Message</label>
        <textarea name="message" class="form-control" rows="5" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">
        Send Message
    </button>

</form>

@endsection