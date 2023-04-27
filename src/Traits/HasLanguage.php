<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Builder;

trait HasLanguage
{
    public function scopeLang(mixed $query, string $lang): Builder
    {
        return $query->where(Config::get('has_language.language_field', 'lang'), $lang);
    }

    public function scopeLangNot(mixed $query, string $lang): Builder
    {
        return $query->where(Config::get('has_language.language_field', 'lang'), '!=', $lang);
    }

    public function scopeLangIn(mixed $query, array $langs): Builder
    {
        return $query->whereIn(Config::get('has_language.language_field', 'lang'), $langs);
    }

    public function scopeLangNotIn(mixed $query, array $langs): Builder
    {
        return $query->whereNotIn(Config::get('has_language.language_field', 'lang'), $langs);
    }
}
