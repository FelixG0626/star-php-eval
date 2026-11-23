<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('photo_path')->nullable();
        });

        foreach (['local-news', 'business', 'sports', 'technology', 'lifestyle'] as $category) {
            $permalinks = array_map(fn (int $number) => "sample-{$category}-{$number}", range(1, 4));

            DB::table('articles')
                ->whereIn('permalink', $permalinks)
                ->update(['photo_path' => 'images/articles/newspaper.png']);
        }
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }
};
