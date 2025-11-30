<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\SluggableTitle;

beforeEach(function () {
    // Create a test table
    Schema::create('test_sluggable_title_models', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('slug');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use SluggableTitle;

        protected $table = 'test_sluggable_title_models';
        protected $guarded = [];
    };
});

afterEach(function () {
    Schema::dropIfExists('test_sluggable_title_models');
});

test('slug is automatically generated from title when creating a model', function () {
    $model = $this->modelClass::create(['title' => 'My Blog Post']);

    expect($model->slug)->toBe('my-blog-post');
});

test('slug is automatically updated from title when updating a model', function () {
    $model = $this->modelClass::create(['title' => 'My Blog Post']);
    expect($model->slug)->toBe('my-blog-post');

    $model->update(['title' => 'Updated Blog Post']);
    expect($model->fresh()->slug)->toBe('updated-blog-post');
});

test('slug handles special characters in title correctly', function () {
    $model = $this->modelClass::create(['title' => 'Hello, World! How are you?']);

    expect($model->slug)->toBe('hello-world-how-are-you');
});

test('slug handles unicode characters in title', function () {
    $model = $this->modelClass::create(['title' => 'Çok Güzel Bir Başlık']);

    expect($model->slug)->toBe('cok-guzel-bir-baslik');
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
    $this->modelClass::create(['title' => 'First Article']);

    $results = $this->modelClass::slugLike('first')->get();

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

test('scopes can be chained with other query methods', function () {
    $this->modelClass::create(['title' => 'First Post']);
    $this->modelClass::create(['title' => 'First Article']);

    $results = $this->modelClass::slugLike('first')
        ->where('title', 'like', '%Post%')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->title)->toBe('First Post');
});

test('empty title generates empty slug', function () {
    $model = $this->modelClass::create(['title' => '']);

    expect($model->slug)->toBe('');
});

test('slug with numbers is handled correctly', function () {
    $model = $this->modelClass::create(['title' => '10 Things You Should Know']);

    expect($model->slug)->toBe('10-things-you-should-know');
});

test('slug with multiple spaces is normalized', function () {
    $model = $this->modelClass::create(['title' => 'Multiple    Spaces    Here']);

    expect($model->slug)->toBe('multiple-spaces-here');
});
