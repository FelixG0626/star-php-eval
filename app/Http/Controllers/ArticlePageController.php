<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArticlePageController extends Controller
{
    public function index(): View
    {
        $articles = Article::active()->with('category')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function photo(string $filename): StreamedResponse
    {
        $disk = Storage::disk('public');
        $path = 'article-photos/'.$filename;

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, headers: ['X-Content-Type-Options' => 'nosniff']);
    }

    public function show(Article $article): View
    {
        abort_unless($article->isActive(), 404);

        $article->load('category');

        return view('articles.show', compact('article'));
    }
}
