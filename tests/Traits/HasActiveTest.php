<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\HasActive;

beforeEach(function () {
    // Create a test table
    Schema::create('test_active_models', function (Blueprint $table) {
        $table->id();
        $table->boolean('is_active')->default(true);
        $table->string('name');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use HasActive;

        protected $table = 'test_active_models';
        protected $fillable = ['name'];
    };

    // Create test data
    $this->modelClass::create(['is_active' => true, 'name' => 'Active Item 1']);
    $this->modelClass::create(['is_active' => true, 'name' => 'Active Item 2']);
    $this->modelClass::create(['is_active' => false, 'name' => 'Inactive Item 1']);
    $this->modelClass::create(['is_active' => false, 'name' => 'Inactive Item 2']);
});

afterEach(function () {
    Schema::dropIfExists('test_active_models');
});

test('initializeHasActive adds is_active to fillable', function () {
    $model = $this->modelClass::create(['is_active' => true, 'name' => 'Test']);

    expect($model->getFillable())->toContain('is_active');
});

test('initializeHasActive adds is_active to casts as boolean', function () {
    $model = $this->modelClass::create(['is_active' => true, 'name' => 'Test']);

    expect($model->getCasts())->toHaveKey('is_active')
        ->and($model->getCasts()['is_active'])->toBe('boolean');
});

test('scopeActive returns only active items', function () {
    $results = $this->modelClass::active()->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn($item) => $item->is_active === true))->toBeTrue();
});

test('scopeInactive returns only inactive items', function () {
    $results = $this->modelClass::inactive()->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn($item) => $item->is_active === false))->toBeTrue();
});

test('scopeStatus filters by status true', function () {
    $results = $this->modelClass::status(true)->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn($item) => $item->is_active === true))->toBeTrue();
});

test('scopeStatus filters by status false', function () {
    $results = $this->modelClass::status(false)->get();

    expect($results)->toHaveCount(2)
        ->and($results->every(fn($item) => $item->is_active === false))->toBeTrue();
});

test('is_active field is properly cast to boolean', function () {
    $model = $this->modelClass::create(['is_active' => 1, 'name' => 'Test']);

    expect($model->is_active)->toBeTrue()
        ->and($model->is_active)->toBeBool();

    $model = $this->modelClass::create(['is_active' => 0, 'name' => 'Test 2']);

    expect($model->is_active)->toBeFalse()
        ->and($model->is_active)->toBeBool();
});

test('scopeActive respects custom is_active field from config', function () {
    Config::set('has_active.is_active', 'status');

    Schema::create('test_custom_active_models', function (Blueprint $table) {
        $table->id();
        $table->boolean('status')->default(true);
        $table->string('name');
        $table->timestamps();
    });

    $customModel = new class extends Model {
        use HasActive;

        protected $table = 'test_custom_active_models';
        protected $fillable = ['name'];
    };

    $customModel::create(['status' => true, 'name' => 'Active']);
    $customModel::create(['status' => false, 'name' => 'Inactive']);

    $results = $customModel::active()->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->status)->toBeTrue();

    Schema::dropIfExists('test_custom_active_models');
    Config::set('has_active.is_active', 'is_active');
});

test('scopes can be chained with other query methods', function () {
    $this->modelClass::create(['is_active' => true, 'name' => 'Active Special']);

    $results = $this->modelClass::active()
        ->where('name', 'like', '%Special%')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Active Special');
});
