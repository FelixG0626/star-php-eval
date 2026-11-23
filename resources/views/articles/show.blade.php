@extends('articles.layout')

@section('title', $article->title)

@section('content')
    @if ($managementPreview ?? false)
        <div class="d-flex justify-content-between align-items-center gap-3">
            <a class="link-secondary" href="{{ route('manage.articles.index') }}">&larr; Manage articles</a>
            <a class="btn btn-outline-secondary" href="{{ route('manage.articles.edit', $article) }}">Edit article</a>
        </div>
    @else
        <a class="link-secondary" href="{{ route('articles.index') }}">&larr; All articles</a>
    @endif

    <article class="card mt-3">
        <div class="card-body p-4 p-md-5">
            <p class="text-body-secondary mb-2">{{ $article->category->name }}</p>
            <h1 class="display-6 fw-bold">{{ $article->title }}</h1>
            <p class="text-body-secondary">
                @if ($article->published_at)
                    Published <time datetime="{{ $article->published_at->toAtomString() }}">{{ $article->published_at->format('d M Y') }}</time>
                @else
                    Publication date unavailable
                @endif
            </p>

            @if ($photoUrl = $article->photoUrl())
                <figure class="mt-4 mb-0">
                    <img class="img-fluid rounded" src="{{ $photoUrl }}" alt="">
                    @if ($article->hasStockPhoto())
                        <figcaption class="small text-body-secondary mt-1">Stock photo for illustration</figcaption>
                    @endif
                </figure>
            @endif

            <div class="mt-4 lh-lg" style="white-space: pre-line">{{ $article->body }}</div>
        </div>
    </article>
@endsection
