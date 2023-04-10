<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Builder;

trait HasActive
{
    public function initializeHasActive(): void
    {
        // append the is_active column to the fillable array
        $this->fillable[] = Config::get('has_active.is_active', 'is_active');

        // add the is_active column to the casts array
        $this->casts[Config::get('has_active.is_active', 'is_active')] = 'boolean';
    }

    public function scopeStatus(mixed $query, bool $status): Builder
    {
        return $query->where(Config::get('has_active.is_active', 'is_active'), $status);
    }

    public function scopeActive(mixed $query): Builder
    {
        return $query->where(Config::get('has_active.is_active', 'is_active'), true);
    }

    public function scopeInactive(mixed $query): Builder
    {
        return $query->where(Config::get('has_active.is_active', 'is_active'), false);
    }
}
