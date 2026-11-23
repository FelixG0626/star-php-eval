@extends('articles.layout')

@section('title', 'Articles')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="display-5 fw-bold mb-0">Articles</h1>
        <a class="btn btn-outline-secondary" href="{{ route('manage.articles.index') }}">Manage articles</a>
    </div>

    <div class="mb-4" id="article-search-controls" hidden>
        @include('articles.filters')
        <button class="btn btn-outline-secondary mt-3" type="button" id="article-clear-filters">Clear filters</button>
        <p class="visually-hidden" id="article-search-status" role="status"></p>
    </div>

    <div class="row g-3" id="article-list">
        @forelse ($articles as $article)
            <div class="col-md-6 col-lg-4" data-article data-category="{{ $article->category_id }}"
                 data-published="{{ $article->published_at?->toDateString() }}">
                <article class="card h-100">
                    @if ($photoUrl = $article->photoUrl())
                        <div class="ratio ratio-16x9">
                            <img class="card-img-top object-fit-cover" src="{{ $photoUrl }}" alt="" loading="lazy">
                        </div>
                    @endif
                    <div class="card-body">
                        <p class="small text-body-secondary mb-2">
                            {{ $article->category->name }}
                            @if ($article->published_at)
                                &middot; <time datetime="{{ $article->published_at->toAtomString() }}">{{ $article->published_at->format('d M Y') }}</time>
                            @endif
                        </p>
                        <h2 class="h5">
                            <a class="link-dark" href="{{ route('articles.show', ['article' => $article->permalink]) }}" data-article-title>{{ $article->title }}</a>
                        </h2>
                        <p class="card-text text-body-secondary">{{ $article->summary }}</p>
                    </div>
                </article>
            </div>
        @empty
            <p class="col-12 text-body-secondary">No articles yet.</p>
        @endforelse
    </div>

    <p id="article-no-results" class="text-body-secondary mt-3" hidden>No articles match your search.</p>
@endsection

@push('scripts')
    @unless ($articles->isEmpty())
        <script>
            const controls = document.querySelector('#article-search-controls');
            const search = document.querySelector('#article-search');
            const category = document.querySelector('#article-category');
            const from = document.querySelector('#article-from');
            const to = document.querySelector('#article-to');
            const searchStatus = document.querySelector('#article-search-status');
            const noResults = document.querySelector('#article-no-results');
            const articles = Array.from(document.querySelectorAll('[data-article]'), element => ({
                element,
                category: element.dataset.category,
                published: element.dataset.published,
                title: element.querySelector('[data-article-title]').textContent.trim().toLocaleLowerCase(),
            }));

            function filterArticles() {
                const query = search.value.trim().toLocaleLowerCase();
                let matches = 0;

                for (const article of articles) {
                    const matchesCategory = !category.value || article.category === category.value;
                    const matchesDate = (!from.value && !to.value) || (
                        article.published &&
                        (!from.value || article.published >= from.value) &&
                        (!to.value || article.published <= to.value)
                    );
                    const visible = article.title.includes(query) && matchesCategory && matchesDate;
                    article.element.hidden = !visible;
                    matches += Number(visible);
                }

                noResults.hidden = matches !== 0;
                searchStatus.textContent = matches === 1 ? '1 article found.' : `${matches} articles found.`;
            }

            controls.addEventListener('input', filterArticles);
            document.querySelector('#article-clear-filters').addEventListener('click', () => {
                for (const field of [search, category, from, to]) {
                    field.value = '';
                }
                from.dispatchEvent(new Event('input', { bubbles: true }));
            });
            filterArticles();
            controls.hidden = false;
        </script>
    @endunless
@endpush
