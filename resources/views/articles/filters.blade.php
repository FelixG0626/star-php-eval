<div class="row g-3 align-items-end">
    <div class="col-sm-6 col-lg-3">
        <label class="form-label" for="article-search">Search by title</label>
        <input class="form-control" type="search" id="article-search" name="title" maxlength="255" value="{{ $filters['title'] ?? '' }}">
    </div>
    <div class="col-sm-6 col-lg-3">
        <label class="form-label" for="article-category">Category</label>
        <select class="form-select" id="article-category" name="category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-12 col-lg-6">
        @include('articles.date-range')
    </div>
</div>
