<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PHOTO = 'images/articles/newspaper.png';

    private const CATEGORIES = ['local-news', 'business', 'sports', 'technology', 'lifestyle'];

    public function up(): void
    {
        $previousPhotos = array_map(fn (string $category) => "images/articles/{$category}.webp", self::CATEGORIES);

        DB::table('articles')->whereIn('photo_path', $previousPhotos)->update(['photo_path' => self::PHOTO]);
    }

    public function down(): void
    {
        // Retired stock files are no longer shipped. Keep the replacement image.
    }
};
