<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-body">
            <h4>Edit Permissions: {{ $user->name }}</h4>
            <p>Role: <strong class="text-primary">{{ ucfirst($user->role) }}</strong></p>

            <div class="row">
                @foreach($modules as $module)
                    {{-- Only show modules that belong to this user's role --}}
                    @if($module->role_access == $user->role)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="modules[]" 
                                       value="{{ $module->slug }}" id="mod_{{ $module->id }}"
                                       {{ in_array($module->slug, $user->assigned_modules ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mod_{{ $module->id }}">
                                    {{ $module->name }}
                                </label>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <hr>
            <button type="submit" class="btn btn-success">Update Permissions</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary">Cancel</a>
        </div>
    </div>
</form>