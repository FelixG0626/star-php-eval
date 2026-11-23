<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            User::firstOrCreate(
                ['email' => 'test@example.com'],
                User::factory()->raw(['name' => 'Test User', 'email' => 'test@example.com']),
            );

            $stories = json_decode(file_get_contents(__DIR__.'/news-articles.json'), true, flags: JSON_THROW_ON_ERROR);

            foreach ($stories as $name => $articles) {
                $category = Category::firstOrCreate(['name' => $name]);

                foreach ($articles as $index => $story) {
                    $permalink = 'sample-'.Str::slug($name).'-'.($index + 1);
                    $article = Article::firstOrNew(['permalink' => $permalink]);

                    if (! $article->exists) {
                        $article->photo_path = 'images/articles/newspaper.png';
                        $article->fill($story + ['category_id' => $category->id]);
                        $article->save();
                    }
                }
            }
        });
    }
}
