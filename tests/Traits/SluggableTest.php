<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\Sluggable;

beforeEach(function () {
    // Create a test table
    Schema::create('test_sluggable_models', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use Sluggable;

        protected $table = 'test_sluggable_models';
        protected $guarded = [];
    };
});

afterEach(function () {
    Schema::dropIfExists('test_sluggable_models');
});

test('slug is automatically generated when creating a model', function () {
    $model = $this->modelClass::create(['title' => 'Hello World']);

    expect($model->slug)->toBe('hello-world');
});

test('slug is automatically updated when updating a model', function () {
    $model = $this->modelClass::create(['title' => 'Hello World']);
    expect($model->slug)->toBe('hello-world');

    $model->update(['title' => 'Updated Title']);
    expect($model->fresh()->slug)->toBe('updated-title');
});

test('slug handles special characters correctly', function () {
    $model = $this->modelClass::create(['title' => 'Hello, World! #$%']);

    expect($model->slug)->toBe('hello-world');
});

test('slug handles unicode characters', function () {
    $model = $this->modelClass::create(['title' => 'Merhaba Dünya']);

    expect($model->slug)->toBe('merhaba-dunya');
});

test('scopeSlug filters by exact slug', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);

    $result = $this->modelClass::slug('first-post')->first();

    expect($result)->not->toBeNull()
        ->and($result->title)->toBe('First Post');
});

test('scopeSlugLike filters by partial slug', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);
    $this->modelClass::create(['title' => 'Third Article']);

    $results = $this->modelClass::slugLike('post')->get();

    expect($results)->toHaveCount(2);
});

test('scopeSlugNot filters out specific slug', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);

    $results = $this->modelClass::slugNot('first-post')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('second-post');
});

test('scopeSlugNotLike filters out partial slug matches', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);
    $this->modelClass::create(['title' => 'Third Article']);

    $results = $this->modelClass::slugNotLike('post')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('third-article');
});

test('scopeSlugIn filters by multiple slugs', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);
    $this->modelClass::create(['title' => 'Third Article']);

    $results = $this->modelClass::slugIn(['first-post', 'third-article'])->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('slug')->toArray())->toBe(['first-post', 'third-article']);
});

test('scopeSlugNotIn filters out multiple slugs', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'Second Post']);
    $this->modelClass::create(['title' => 'Third Article']);

    $results = $this->modelClass::slugNotIn(['first-post', 'third-article'])->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->slug)->toBe('second-post');
});

test('sluggable respects custom field names from config', function () {
    Config::set('sluggable_fields.slug', 'custom_slug');
    Config::set('sluggable_fields.title', 'custom_title');

    Schema::create('test_custom_sluggable_models', function (Blueprint $table) {
        $table->id();
        $table->string('custom_title');
        $table->string('custom_slug');
        $table->timestamps();
    });

    $customModel = new class extends Model {
        use Sluggable;

        protected $table = 'test_custom_sluggable_models';
        protected $guarded = [];
    };

    $model = $customModel::create(['custom_title' => 'Hello World']);

    expect($model->custom_slug)->toBe('hello-world');

    $result = $customModel::slug('hello-world')->first();
    expect($result)->not->toBeNull()
        ->and($result->custom_title)->toBe('Hello World');

    Schema::dropIfExists('test_custom_sluggable_models');
    Config::set('sluggable_fields.slug', 'slug');
    Config::set('sluggable_fields.title', 'title');
});

test('scopes can be chained with other query methods', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'First Article']);

    $results = $this->modelClass::slugLike('first')
        ->where('title', 'like', '%Post%')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->title)->toBe('First Post');
});
