<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait Sluggable
{

    public static function bootSluggable(): void
    {
        static::creating(function ($model) {
            $model->slug = $model->slug ?: Str::slug($model->title);
        });

        static::updating(function ($model) {
            $model->slug = $model->slug ?: Str::slug($model->title);
        });
    }

    public function scopeSlug(mixed $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    public function scopeSlugLike(mixed $query, string $slug): Builder
    {
        return $query->where('slug', 'like', "%{$slug}%");
    }

    public function scopeSlugNot(mixed $query, string $slug): Builder
    {
        return $query->where('slug', '!=', $slug);
    }

    public function scopeSlugNotLike(mixed $query, string $slug): Builder
    {
        return $query->where('slug', 'not like', "%{$slug}%");
    }

    public function scopeSlugIn(mixed $query, array $slugs): Builder
    {
        return $query->whereIn('slug', $slugs);
    }

    public function scopeSlugNotIn(mixed $query, array $slugs): Builder
    {
        return $query->whereNotIn('slug', $slugs);
    }
}
