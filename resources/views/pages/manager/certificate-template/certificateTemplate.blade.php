@extends('layouts/layoutMaster')

@section('title', 'Certificate Templates')

@section('content')
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Certificate Templates</h4>
</div>

@if($errors->any())
@foreach($errors->all() as $error)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ $error }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endforeach
@endif
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div><strong>Active Templates</strong></div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTemplateModal">+ Create Template</button>
    </div>

    <div class="table-responsive" style="max-height:500px;">
      <table class="table">
        <thead>
          <tr><th>#</th><th>Title</th><th>Type</th><th>Status</th><th>Created At</th><th>Action</th></tr>
        </thead>
        <tbody>
        @forelse ($templates as $key => $template)
          <tr>
            <td>{{ $templates->firstItem() + $key }}</td>
            <td>{{ $template->title }}</td>
            <td>{{ $template->certificate_type == 'internship' ? 'Internship' : 'Course Completion' }}</td>
            <td><span class="badge {{ $template->status ? 'bg-success' : 'bg-danger' }}">{{ $template->status ? 'Active' : 'Inactive' }}</span></td>
            <td>{{ $template->created_at }}</td>
            <td>
              <a class="btn btn-sm btn-info" href="{{ route('manager.certificate.template.preview', $template->id) }}" target="_blank">Preview PDF</a>
              <button class="btn btn-sm btn-secondary edit-btn" data-id="{{ $template->id }}" data-title="{{ $template->title }}" data-type="{{ $template->certificate_type }}" data-status="{{ $template->status }}" data-content="{{ htmlentities($template->content) }}">Edit</button>
              <form action="{{ route('manager.certificate.template.delete', $template->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center">No templates found.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">{{ $templates->links() }}</div>
</div>

<div class="modal fade" id="createTemplateModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>Create Template</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form method="POST" action="{{ route('manager.certificate.template.create') }}">@csrf
<div class="mb-3"><label>Title</label><input name="title" required class="form-control"></div>
<div class="mb-3"><label>Type</label><select name="certificate_type" class="form-control"><option value="internship">Internship</option><option value="course_completion">Course Completion</option></select></div>
<div class="mb-3"><label>Content (HTML placeholders: @{{name}}, @{{email}}, @{{certificate_type}}, @{{date}})</label><textarea name="content" rows="6" class="form-control" required></textarea></div>
<div class="mb-3"><label>Status</label><select name="status" class="form-control"><option value="1">Active</option><option value="0">Inactive</option></select></div>
<div class="text-end"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button> <button class="btn btn-primary" type="submit">Create</button></div></form></div></div></div></div>

<div class="modal fade" id="editTemplateModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5>Edit Template</h5><button class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><form id="editTemplateForm" method="POST" action=""><input type="hidden" name="_method" value="PUT">@csrf
<div class="mb-3"><label>Title</label><input name="title" id="edit-title" class="form-control" required></div>
<div class="mb-3"><label>Type</label><select id="edit-type" name="certificate_type" class="form-control"><option value="internship">Internship</option><option value="course_completion">Course Completion</option></select></div>
<div class="mb-3"><label>Content</label><textarea id="edit-content" name="content" class="form-control" rows="6" required></textarea></div>
<div class="mb-3"><label>Status</label><select id="edit-status" name="status" class="form-control"><option value="1">Active</option><option value="0">Inactive</option></select></div>
<div class="text-end"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button> <button class="btn btn-primary" type="submit">Save</button></div></form></div></div></div></div>

@push('scripts')
<script>
document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    const form = document.getElementById('editTemplateForm');
    form.action = '/manager/certificate-templates/update/' + id;
    document.getElementById('edit-title').value = btn.dataset.title;
    document.getElementById('edit-type').value = btn.dataset.type;
    document.getElementById('edit-status').value = btn.dataset.status;
    document.getElementById('edit-content').value = decodeURIComponent(btn.dataset.content || '');
    new bootstrap.Modal(document.getElementById('editTemplateModal')).show();
  });
});
</script>
@endpush
@endsection