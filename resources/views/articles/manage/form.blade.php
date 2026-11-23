@extends('articles.layout')

@section('title', $article->exists ? 'Edit article' : 'New article')

@section('content')
    <a class="link-secondary" href="{{ route('manage.articles.index') }}">&larr; Manage articles</a>
    <h1 class="h2 my-3">@yield('title')</h1>

    <form method="POST" enctype="multipart/form-data" class="card shadow-sm" action="{{ $article->exists ? route('manage.articles.update', $article) : route('manage.articles.store') }}">
        @csrf
        @if ($article->exists)
            @method('PUT')
        @endif

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach (['title' => 'Title', 'permalink' => 'Permalink', 'summary' => 'Summary'] as $field => $label)
                <div class="mb-3">
                    <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                    <input class="form-control" id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $article->{$field}) }}" maxlength="255" required
                        @if ($field === 'permalink') aria-describedby="permalink-help" pattern="[a-z0-9]+(?:\x2d[a-z0-9]+)*" @endif>
                    @if ($field === 'permalink')
                        <div class="form-text" id="permalink-help">Use lowercase letters, numbers, and hyphens. Changing this field changes the public URL.</div>
                    @endif
                </div>
            @endforeach

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label" for="category_id">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Choose a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $article->category_id) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @if ($categories->isEmpty())
                        <div class="form-text">A category is required. Run the database seeder to add sample categories.</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="published_at">Publication date (optional)</label>
                    <input class="form-control" type="datetime-local" step="1" id="published_at" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i:s')) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" aria-describedby="status-help" required>
                    <option value="active" @selected(old('status', $article->status) === 'active')>Active</option>
                    <option value="inactive" @selected(old('status', $article->status) === 'inactive')>Inactive</option>
                </select>
                <div class="form-text" id="status-help">Inactive articles are hidden from the public site and API.</div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="body">Body</label>
                <textarea class="form-control" id="body" name="body" rows="10" required>{{ old('body', $article->body) }}</textarea>
            </div>

            <div class="mb-4">
                @if ($photoUrl = $article->photoUrl())
                    <img class="d-block rounded mb-2 object-fit-cover" src="{{ $photoUrl }}" alt="Current article photo" width="240" height="135">
                    @if ($article->hasStockPhoto())
                        <p class="small text-body-secondary mb-2">Stock photo for illustration</p>
                    @endif
                @endif

                <label class="form-label" for="photo">{{ $photoUrl ? 'Replace photo' : 'Upload photo' }}</label>
                <input class="form-control" type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" aria-describedby="photo-help">
                <div class="form-text" id="photo-help">Optional. JPEG, PNG, or WebP up to 2 MB.</div>

                @if ($photoUrl)
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo" value="1" @checked(old('remove_photo'))>
                        <label class="form-check-label" for="remove_photo">Remove current photo</label>
                    </div>
                @endif
            </div>

            <button class="btn btn-primary" type="submit" @disabled($categories->isEmpty())>{{ $article->exists ? 'Save changes' : 'Create article' }}</button>
            <a class="btn btn-link" href="{{ route('manage.articles.index') }}">Cancel</a>
        </div>
    </form>
@endsection
