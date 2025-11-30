# ModelSupport

[![ModelSupport](https://preview.dragon-code.pro/LaravelReady/model-support.svg?brand=laravel)](https://github.com/laravel-ready/model-support)

[![Stable Version][badge_stable]][link_packagist]
[![Unstable Version][badge_unstable]][link_packagist]
[![Total Downloads][badge_downloads]][link_packagist]
[![License][badge_license]][link_license]

## 📂 About

Useful eloquent model support traits.

## 📦 Installation

Get via composer

```bash
composer require laravel-ready/model-support
```

## ⚙️ Configs

```bash
php artisan vendor:publish --tag=model-support-config
```

## Example Trait Usage

```php

use LaravelReady\ModelSupport\Traits\Sluggable;
use LaravelReady\ModelSupport\Traits\HasActive;
...

class Post extends Model
{
    use Sluggable, HasActive;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active'
    ];

    ...
}

```

## 📝 Usage

### HasLanguage

This trait allows you to get models by language.

> **Note**
> Field name is `lang` by default. You can change it in the config file.

```php

use LaravelReady\ModelSupport\Traits\HasLanguage;
...

$model->lang('en'); // will return $query->where('lang', $lang);
$model->langNot('en'); // will return $query->where('lang', '!=', $lang);
$model->langIn(['en', 'tr']); // will return $query->whereIn('lang', $langs);
$model->langNotIn(['en', 'tr']); // will return $query->whereNotIn('lang', $langs);

```

### Sluggable

This trait allows you to generate a slug from a string. When you create a new model, the slug will be generated automatically. If you change the title, the slug will also be updated. See [bootSluggable()](src/Traits/Sluggable.php#L10) method for more details.

> **Note**
> Field names are `slug` and `title` by default. You can change it in the config file.

```php

use LaravelReady\ModelSupport\Traits\Sluggable;
...

$model->slug('any-string'); // will return $query->where('slug', $slug);
$model->slugLike('any-string'); // will return $query->where('slug', 'like', $slug);
$model->slugNot('any-string'); // will return $query->where('slug', '!=', $slug);
$model->slugNotLike('any-string'); // will return $query->where('slug', 'not like', $slug);
$model->slugIn(['any-string', 'any-string']); // will return $query->whereIn('slug', $slug);
$model->slugNotIn(['any-string', 'any-string']); // will return $query->whereNotIn('slug', $slug);

```

### SluggableTitle

This trait allows you to generate a slug from a title field. Same as [Sluggable](#sluggable) trait but it only works with the title field.

> **Note**
> Field names are `slug` and `title` (hardcoded, not configurable).

```php

use LaravelReady\ModelSupport\Traits\SluggableTitle;
...

$model->slug('any-string'); // will return $query->where('slug', $slug);
$model->slugLike('any-string'); // will return $query->where('slug', 'like', $slug);
$model->slugNot('any-string'); // will return $query->where('slug', '!=', $slug);
$model->slugNotLike('any-string'); // will return $query->where('slug', 'not like', $slug);
$model->slugIn(['any-string', 'any-string']); // will return $query->whereIn('slug', $slug);
$model->slugNotIn(['any-string', 'any-string']); // will return $query->whereNotIn('slug', $slug);

```

### SluggableName

This trait allows you to generate a slug from a name field. Same as [Sluggable](#sluggable) trait but it only works with the name field.

> **Note**
> Field names are `slug` and `name` (hardcoded, not configurable).

```php

use LaravelReady\ModelSupport\Traits\SluggableName;
...

$model->slug('any-string'); // will return $query->where('slug', $slug);
$model->slugLike('any-string'); // will return $query->where('slug', 'like', $slug);
$model->slugNot('any-string'); // will return $query->where('slug', '!=', $slug);
$model->slugNotLike('any-string'); // will return $query->where('slug', 'not like', $slug);
$model->slugIn(['any-string', 'any-string']); // will return $query->whereIn('slug', $slug);
$model->slugNotIn(['any-string', 'any-string']); // will return $query->whereNotIn('slug', $slug);

```

### ParentChild

This trait allows you to work with parent-child relationships in self-referencing models.

> **Note**
> Field name is `parent_id` by default. You can change it in the config file.

> **Warning**
> It's only supports self-referencing models.

```php

use LaravelReady\ModelSupport\Traits\ParentChild;
...

$model->parent(); // will return parent model (BelongsTo relationship)
$model->children(); // will return children models (HasMany relationship)
$model->recursiveParent(); // will return parent with all recursive parents
$model->recursiveChildren(); // will return children with all recursive children
$model->recursiveParentAndChildren(); // will return parent and children recursively

```

### HasActive

This trait allows you to get active/inactive status models.

> **Note**
> Field name is `is_active` by default. You can change it in the config file.

> **Warning**
> This trait forces your models to fillable `is_active` field and adds `is_active` cast to `boolean`.

```php

use LaravelReady\ModelSupport\Traits\HasActive;
...

$model->status(true|false); // will return $query->where('is_active', $status);
$model->active(); // will return $query->where('is_active', true);
$model->inactive(); // will return $query->where('is_active', false);

```

## ⚓ Credits

- This project was generated by the **[packager](https://github.com/laravel-ready/packager)**.

[badge_downloads]: https://img.shields.io/packagist/dt/laravel-ready/model-support.svg?style=flat-square

[badge_license]: https://img.shields.io/packagist/l/laravel-ready/model-support.svg?style=flat-square

[badge_stable]: https://img.shields.io/github/v/release/laravel-ready/model-support?label=stable&style=flat-square

[badge_unstable]: https://img.shields.io/badge/unstable-dev--main-orange?style=flat-square

[link_license]: LICENSE

[link_packagist]: https://packagist.org/packages/laravel-ready/model-support
