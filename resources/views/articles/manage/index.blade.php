@extends('articles.layout')

@section('title', 'Manage articles')

@section('content')
    <a class="link-secondary" href="{{ route('articles.index') }}">&larr; All articles</a>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 my-3">
        <h1 class="h2 mb-0">Manage articles</h1>
        <a class="btn btn-primary" href="{{ route('manage.articles.create') }}">New article</a>
    </div>

    @if (session('status'))
        <p class="alert alert-success" role="status">{{ session('status') }}</p>
    @endif

    <form class="mb-4" method="GET" action="{{ route('manage.articles.index') }}">
        @include('articles.filters')
        <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary" type="submit">Apply filters</button>
            <a class="btn btn-outline-secondary" href="{{ route('manage.articles.index') }}">Clear filters</a>
        </div>
    </form>

    @if ($errors->any())
        <ul class="alert alert-danger list-unstyled" role="alert">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if ($articles->isEmpty())
        <p class="text-body-secondary">{{ request()->anyFilled(['title', 'category', 'from', 'to']) ? 'No articles match your filters.' : 'No articles yet.' }}</p>
    @else
        <div class="table-responsive border rounded mb-3">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Category</th>
                        <th scope="col">Published</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($articles as $article)
                        <tr>
                            <td><a href="{{ route('manage.articles.show', $article) }}">{{ $article->title }}</a></td>
                            <td>{{ $article->category->name }}</td>
                            <td class="text-nowrap">{{ $article->published_at?->format('d M Y') ?? 'No date' }}</td>
                            <td><span class="badge {{ $article->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ucfirst($article->status) }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('manage.articles.edit', $article) }}" aria-label="Edit {{ $article->title }}">Edit</a>
                                    <form method="POST" action="{{ route('manage.articles.destroy', $article) }}" onsubmit="return window.confirm('Delete this article permanently?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $article->title }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $articles->links('pagination::bootstrap-5') }}
    @endif
@endsection
