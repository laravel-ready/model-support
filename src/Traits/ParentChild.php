<?php

namespace LaravelReady\ModelSupport\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ParentChild
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, Config::get('has_active.parent_id', 'parent_id'));
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, Config::get('has_active.parent_id', 'parent_id'));
    }

    public function recursiveParent(): mixed
    {
        return $this->parent()->with('recursiveParent');
    }

    public function recursiveChildren(): mixed
    {
        return $this->children()->with('recursiveChildren');
    }

    public function recursiveParentAndChildren(): mixed
    {
        return $this->recursiveParent()->with('recursiveChildren');
    }
}
