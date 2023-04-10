<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Builder;

trait Sluggable
{
    public function initializeSluggable(): void
    {
        $slugFieldName = Config::get('sluggable_fields.slug', 'slug');
        $titleFieldName = Config::get('sluggable_fields.title', 'title');

        static::creating(function ($model) use ($slugFieldName, $titleFieldName) {
            $model->slug = $model->$slugFieldName ?: Str::slug($model->$titleFieldName);
        });

        static::updating(function ($model) use ($slugFieldName, $titleFieldName) {
            $model->slug = $model->$slugFieldName ?: Str::slug($model->$titleFieldName);
        });
    }

    public function scopeSlug(mixed $query, string $slug): Builder
    {
        return $query->where(Config::get('sluggable_fields.slug', 'slug'), $slug);
    }

    public function scopeSlugLike(mixed $query, string $slug): Builder
    {
        return $query->where(Config::get('sluggable_fields.slug', 'slug'), 'like', "%{$slug}%");
    }

    public function scopeSlugNot(mixed $query, string $slug): Builder
    {
        return $query->where(Config::get('sluggable_fields.slug', 'slug'), '!=', $slug);
    }

    public function scopeSlugNotLike(mixed $query, string $slug): Builder
    {
        return $query->where(Config::get('sluggable_fields.slug', 'slug'), 'not like', "%{$slug}%");
    }

    public function scopeSlugIn(mixed $query, array $slugs): Builder
    {
        return $query->whereIn(Config::get('sluggable_fields.slug', 'slug'), $slugs);
    }

    public function scopeSlugNotIn(mixed $query, array $slugs): Builder
    {
        return $query->whereNotIn(Config::get('sluggable_fields.slug', 'slug'), $slugs);
    }
}
