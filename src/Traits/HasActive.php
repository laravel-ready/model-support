<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasActive
{
    public function bootHasActive(): void
    {
        // append the is_active column to the fillable array
        $this->fillable[] = 'is_active';

        // add the is_active column to the casts array
        $this->casts['is_active'] = 'boolean';
    }

    public function scopeStatus(mixed $query, bool $status): Builder
    {
        return $query->where('is_active', $status);
    }

    public function scopeActive(mixed $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(mixed $query): Builder
    {
        return $query->where('is_active', false);
    }
}
