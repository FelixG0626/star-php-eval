<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ArticleManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', Rule::exists(Category::class, 'id')],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('from'), 'after_or_equal:from')],
        ]);

        $title = $filters['title'] ?? null;

        $articles = Article::with('category')
            ->when($title !== null, fn ($query) => $query->whereRaw("LOWER(title) LIKE LOWER(?) ESCAPE '!'", ['%'.strtr($title, ['!' => '!!', '%' => '!%', '_' => '!_']).'%']))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category_id', $category))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('published_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('published_at', '<=', $to))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('articles.manage.index', compact('articles', 'categories', 'filters'));
    }

    public function create(): View
    {
        return $this->form(new Article);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->saveArticle($request, new Article);

        return to_route('manage.articles.index')->with('status', 'Article created.');
    }

    public function show(Article $article): View
    {
        $article->load('category');

        return view('articles.show', ['article' => $article, 'managementPreview' => true]);
    }

    public function edit(Article $article): View
    {
        return $this->form($article);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $this->saveArticle($request, $article);

        return to_route('manage.articles.index')->with('status', 'Article updated.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        $this->deleteUpload($article->photo_path);

        return to_route('manage.articles.index')->with('status', 'Article deleted.');
    }

    private function saveArticle(Request $request, Article $article): void
    {
        $attributes = $this->validated($request, $article);
        $oldPhoto = $article->photo_path;
        $upload = $request->file('photo');
        $newPhoto = null;

        if ($upload) {
            $newPhoto = $upload->store('article-photos', 'public');

            if ($newPhoto === false) {
                throw ValidationException::withMessages(['photo' => 'The photo could not be saved. Please try again.']);
            }
        }

        unset($attributes['photo'], $attributes['remove_photo']);
        $article->fill($attributes);
        $article->photo_path = $newPhoto ?? ($request->boolean('remove_photo') ? null : $oldPhoto);

        try {
            $article->save();
        } catch (Throwable $exception) {
            $this->deleteUpload($newPhoto);

            throw $exception;
        }

        if ($oldPhoto !== $article->photo_path) {
            $this->deleteUpload($oldPhoto);
        }
    }

    private function deleteUpload(?string $path): void
    {
        if ($path !== null && str_starts_with($path, 'article-photos/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function form(Article $article): View
    {
        $categories = Category::orderBy('name')->get();

        return view('articles.manage.form', compact('article', 'categories'));
    }

    private function validated(Request $request, Article $article): array
    {
        $permalink = Rule::unique(Article::class, 'permalink');

        if ($article->exists) {
            $permalink->ignore($article);
        }

        return $request->validate([
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['required', Rule::in([Article::ACTIVE, Article::INACTIVE])],
            'category_id' => ['required', 'integer', Rule::exists(Category::class, 'id')],
            'published_at' => ['nullable', 'date_format:Y-m-d\TH:i,Y-m-d\TH:i:s'],
            'permalink' => ['required', 'string', 'max:255', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $permalink],
        ]);
    }
}
