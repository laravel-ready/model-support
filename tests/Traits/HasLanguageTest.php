<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\HasLanguage;

beforeEach(function () {
    // Create a test table
    Schema::create('test_models', function (Blueprint $table) {
        $table->id();
        $table->string('lang');
        $table->string('name');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use HasLanguage;

        protected $table = 'test_models';
        protected $guarded = [];
    };

    // Create test data
    $this->modelClass::create(['lang' => 'en', 'name' => 'English Item']);
    $this->modelClass::create(['lang' => 'tr', 'name' => 'Turkish Item']);
    $this->modelClass::create(['lang' => 'de', 'name' => 'German Item']);
    $this->modelClass::create(['lang' => 'fr', 'name' => 'French Item']);
});

afterEach(function () {
    Schema::dropIfExists('test_models');
});

test('scopeLang filters by language', function () {
    $results = $this->modelClass::lang('en')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->lang)->toBe('en')
        ->and($results->first()->name)->toBe('English Item');
});

test('scopeLangNot filters out specific language', function () {
    $results = $this->modelClass::langNot('en')->get();

    expect($results)->toHaveCount(3)
        ->and($results->pluck('lang')->toArray())->not->toContain('en');
});

test('scopeLangIn filters by multiple languages', function () {
    $results = $this->modelClass::langIn(['en', 'tr'])->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('lang')->toArray())->toBe(['en', 'tr']);
});

test('scopeLangNotIn filters out multiple languages', function () {
    $results = $this->modelClass::langNotIn(['en', 'tr'])->get();

    expect($results)->toHaveCount(2)
        ->and($results->pluck('lang')->toArray())->toBe(['de', 'fr']);
});

test('scopeLang respects custom language field from config', function () {
    Config::set('has_language.language_field', 'custom_lang');

    Schema::create('test_custom_models', function (Blueprint $table) {
        $table->id();
        $table->string('custom_lang');
        $table->string('name');
        $table->timestamps();
    });

    $customModel = new class extends Model {
        use HasLanguage;

        protected $table = 'test_custom_models';
        protected $guarded = [];
    };

    $customModel::create(['custom_lang' => 'en', 'name' => 'English']);
    $customModel::create(['custom_lang' => 'tr', 'name' => 'Turkish']);

    $results = $customModel::lang('en')->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->custom_lang)->toBe('en');

    Schema::dropIfExists('test_custom_models');
    Config::set('has_language.language_field', 'lang');
});

test('all scopes can be chained with other query methods', function () {
    $this->modelClass::create(['lang' => 'en', 'name' => 'Another English Item']);

    $results = $this->modelClass::lang('en')
        ->where('name', 'English Item')
        ->get();

    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('English Item');
});
