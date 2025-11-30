<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\SluggableName;

beforeEach(function () {
    // Create a test table
    Schema::create('test_sluggable_name_models', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use SluggableName;

        protected $table = 'test_sluggable_name_models';
        protected $guarded = [];
    };
});

afterEach(function () {
    Schema::dropIfExists('test_sluggable_name_models');
});

test('slug is automatically generated from name when creating a model', function () {
    $model = $this->modelClass::create(['name' => 'John Doe']);

    expect($model->slug)->toBe('john-doe');
});

test('slug is automatically updated from name when updating a model', function () {
    $model = $this->modelClass::create(['name' => 'John Doe']);
    expect($model->slug)->toBe('john-doe');

    $model->update(['name' => 'Jane Smith']);
    expect($model->fresh()->slug)->toBe('jane-smith');
});

test('slug handles special characters in name correctly', function () {
    $model = $this->modelClass::create(['name' => 'John O\'Brien & Associates']);

    expect($model->slug)->toBe('john-obrien-associates');
});

test('slug handles unicode characters in name', function () {
    $model = $this->modelClass::create(['name' => 'Müller Schmidt']);

    expect($model->slug)->toBe('muller-schmidt');
});

test('scopeSlug filters by exact slug', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'Jane Smith']);

    $result = $this->modelClass::slug('john-doe')->first();

    expect($result)->not->toBeNull()
        ->and($result->name)->toBe('John Doe');
});

test('scopeSlugLike filters by partial slug', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'John Smith']);
    $this->modelClass::create(['name' => 'Jane Doe']);

    $results = $this->modelClass::slugLike('john')->get();

    expect($results)->toHaveCount(2);
});

test('scopeSlugNot filters out specific slug', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'Jane Smith']);

    $results = $this->modelClass::slugNot('john-doe')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('jane-smith');
});

test('scopeSlugNotLike filters out partial slug matches', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'John Smith']);
    $this->modelClass::create(['name' => 'Jane Doe']);

    $results = $this->modelClass::slugNotLike('john')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('jane-doe');
});

test('scopeSlugIn filters by multiple slugs', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'Jane Smith']);
    $this->modelClass::create(['name' => 'Bob Johnson']);

    $results = $this->modelClass::slugIn(['john-doe', 'bob-johnson'])->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('slug')->toArray())->toBe(['john-doe', 'bob-johnson']);
});

test('scopeSlugNotIn filters out multiple slugs', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'Jane Smith']);
    $this->modelClass::create(['name' => 'Bob Johnson']);

    $results = $this->modelClass::slugNotIn(['john-doe', 'bob-johnson'])->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('jane-smith');
});

test('scopes can be chained with other query methods', function () {
    $this->modelClass::create(['name' => 'John Doe']);
    $this->modelClass::create(['name' => 'John Smith']);

    $results = $this->modelClass::slugLike('john')
        ->where('name', 'like', '%Doe%')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('John Doe');
});

test('empty name generates empty slug', function () {
    $model = $this->modelClass::create(['name' => '']);

    expect($model->slug)->toBe('');
});

test('slug with numbers is handled correctly', function () {
    $model = $this->modelClass::create(['name' => 'Product 123']);

    expect($model->slug)->toBe('product-123');
});
