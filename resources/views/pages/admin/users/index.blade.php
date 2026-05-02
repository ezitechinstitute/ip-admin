<div class="card">
    <div class="card-header">
        <h5 class="card-title">All Users</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Assigned Modules</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-label-info">{{ $user->role }}</span></td>
                    <td>
                        @if($user->assigned_modules)
                            @foreach($user->assigned_modules as $module)
                                <span class="badge bg-label-secondary small">{{ $module }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No modules</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-warning">
                            Edit Permissions
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>