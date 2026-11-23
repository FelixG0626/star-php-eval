<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    protected $attributes = ['status' => self::ACTIVE];

    protected $fillable = [
        'title',
        'summary',
        'body',
        'published_at',
        'permalink',
        'category_id',
        'status',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        return $this->hasStockPhoto()
            ? asset($this->photo_path)
            : route('articles.photo', ['filename' => basename($this->photo_path)]);
    }

    public function hasStockPhoto(): bool
    {
        return str_starts_with($this->photo_path ?? '', 'images/articles/');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
