@extends('layouts/layoutMaster')

@section('title', 'Knowledge Base')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Knowledge Base</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Knowledge Base Articles</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
@forelse($articles as $index => $article)
  <tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $article->title }}</td>
    <td>{{ $article->category }}</td>
    <td>{{ $article->description }}</td>
  </tr>
@empty
  <tr>
    <td colspan="4" class="text-center">No knowledge base articles found</td>
  </tr>
@endforelse
</tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection