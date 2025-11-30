<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use LaravelReady\ModelSupport\Traits\ParentChild;

beforeEach(function () {
    // Create a test table
    Schema::create('test_parent_child_models', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parent_id')->nullable();
        $table->string('name');
        $table->timestamps();
    });

    // Create test model class
    $this->modelClass = new class extends Model {
        use ParentChild;

        protected $table = 'test_parent_child_models';
        protected $guarded = [];
    };
});

afterEach(function () {
    Schema::dropIfExists('test_parent_child_models');
});

test('parent relationship returns BelongsTo instance', function () {
    $model = new $this->modelClass();
    $relation = $model->parent();

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('children relationship returns HasMany instance', function () {
    $model = new $this->modelClass();
    $relation = $model->children();

    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('parent relationship retrieves parent model', function () {
    $parent = $this->modelClass::create(['name' => 'Parent']);
    $child = $this->modelClass::create(['name' => 'Child', 'parent_id' => $parent->id]);

    $retrievedParent = $child->parent;

    expect($retrievedParent)->not->toBeNull()
        ->and($retrievedParent->id)->toBe($parent->id)
        ->and($retrievedParent->name)->toBe('Parent');
});

test('children relationship retrieves child models', function () {
    $parent = $this->modelClass::create(['name' => 'Parent']);
    $child1 = $this->modelClass::create(['name' => 'Child 1', 'parent_id' => $parent->id]);
    $child2 = $this->modelClass::create(['name' => 'Child 2', 'parent_id' => $parent->id]);

    $children = $parent->children;

    expect($children)->toHaveCount(2)
        ->and($children->pluck('name')->toArray())->toBe(['Child 1', 'Child 2']);
});

test('model without parent has null parent', function () {
    $model = $this->modelClass::create(['name' => 'Orphan']);

    expect($model->parent)->toBeNull();
});

test('model without children has empty children collection', function () {
    $model = $this->modelClass::create(['name' => 'Childless']);

    expect($model->children)->toHaveCount(0);
});

test('recursiveParent includes parent with nested recursiveParent', function () {
    $grandparent = $this->modelClass::create(['name' => 'Grandparent']);
    $parent = $this->modelClass::create(['name' => 'Parent', 'parent_id' => $grandparent->id]);
    $child = $this->modelClass::create(['name' => 'Child', 'parent_id' => $parent->id]);

    $result = $child->recursiveParent()->first();

    expect($result)->not->toBeNull()
        ->and($result->name)->toBe('Parent')
        ->and($result->recursiveParent)->not->toBeNull()
        ->and($result->recursiveParent->name)->toBe('Grandparent');
});

test('recursiveChildren includes children with nested recursiveChildren', function () {
    $parent = $this->modelClass::create(['name' => 'Parent']);
    $child = $this->modelClass::create(['name' => 'Child', 'parent_id' => $parent->id]);
    $grandchild = $this->modelClass::create(['name' => 'Grandchild', 'parent_id' => $child->id]);

    $result = $parent->recursiveChildren()->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->name)->toBe('Child')
        ->and($result->first()->recursiveChildren)->toHaveCount(1)
        ->and($result->first()->recursiveChildren->first()->name)->toBe('Grandchild');
});

test('recursiveParentAndChildren includes both parent and children hierarchies', function () {
    $grandparent = $this->modelClass::create(['name' => 'Grandparent']);
    $parent = $this->modelClass::create(['name' => 'Parent', 'parent_id' => $grandparent->id]);
    $sibling = $this->modelClass::create(['name' => 'Sibling', 'parent_id' => $parent->id]);
    $child = $this->modelClass::create(['name' => 'Child', 'parent_id' => $sibling->id]);

    $result = $sibling->recursiveParentAndChildren()->first();

    expect($result)->not->toBeNull()
        ->and($result->name)->toBe('Parent')
        ->and($result->recursiveChildren)->toHaveCount(1);
});

test('multiple levels of children are accessible', function () {
    $root = $this->modelClass::create(['name' => 'Root']);
    $level1 = $this->modelClass::create(['name' => 'Level 1', 'parent_id' => $root->id]);
    $level2 = $this->modelClass::create(['name' => 'Level 2', 'parent_id' => $level1->id]);
    $level3 = $this->modelClass::create(['name' => 'Level 3', 'parent_id' => $level2->id]);

    $result = $root->recursiveChildren()->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->name)->toBe('Level 1')
        ->and($result->first()->recursiveChildren)->toHaveCount(1)
        ->and($result->first()->recursiveChildren->first()->name)->toBe('Level 2')
        ->and($result->first()->recursiveChildren->first()->recursiveChildren)->toHaveCount(1)
        ->and($result->first()->recursiveChildren->first()->recursiveChildren->first()->name)->toBe('Level 3');
});

test('custom parent_id field from config is respected', function () {
    Config::set('has_active.parent_id', 'custom_parent_id');

    Schema::create('test_custom_parent_child_models', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('custom_parent_id')->nullable();
        $table->string('name');
        $table->timestamps();
    });

    $customModel = new class extends Model {
        use ParentChild;

        protected $table = 'test_custom_parent_child_models';
        protected $guarded = [];
    };

    $parent = $customModel::create(['name' => 'Parent']);
    $child = $customModel::create(['name' => 'Child', 'custom_parent_id' => $parent->id]);

    $retrievedParent = $child->parent;

    expect($retrievedParent)->not->toBeNull()
        ->and($retrievedParent->id)->toBe($parent->id);

    Schema::dropIfExists('test_custom_parent_child_models');
    Config::set('has_active.parent_id', 'parent_id');
});
