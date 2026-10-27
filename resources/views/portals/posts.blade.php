@extends('layouts.portal')

@section('title', 'Post review')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Community post review</h1>
    <p class="text-secondary mb-0">Inspect posts and hide or republish content. Hidden posts no longer appear in the public community feed.</p>
</div>
<div class="portal-module">
    <div class="table-responsive">
        <table class="table portal-table table-hover align-middle mb-0">
            <thead><tr><th>Post</th><th>Author</th><th>Type</th><th>Visibility</th><th>Created</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($posts as $post)
                <tr>
                    <td style="min-width: 260px; max-width: 560px">{{ \Illuminate\Support\Str::limit($post->content, 220) }}</td>
                    <td>{{ $post->user?->name ?? 'Account removed' }}</td>
                    <td>{{ ucfirst($post->type) }}</td>
                    <td><span class="badge {{ $post->moderation_status === 'hidden' ? 'text-bg-secondary' : 'text-bg-success' }}">{{ ucfirst($post->moderation_status) }}</span></td>
                    <td>{{ $post->created_at->format('M j, Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('developer.posts.visibility', $post) }}">
                            @csrf @method('PUT')
                            <input type="hidden" name="moderation_status" value="{{ $post->moderation_status === 'hidden' ? 'published' : 'hidden' }}">
                            <button class="btn btn-sm {{ $post->moderation_status === 'hidden' ? 'btn-success' : 'btn-outline-danger' }}">
                                {{ $post->moderation_status === 'hidden' ? 'Republish' : 'Hide' }}
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-5">No community posts found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">{{ $posts->links() }}</div>
</div>
@endsection
