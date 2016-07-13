# Another Fractal Service Provider for Laravel 5 and Lumen

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vice/laravel-fractal.svg?style=flat-square)](https://packagist.org/packages/vice/laravel-fractal)
[![Build Status](https://travis-ci.org/VICEMedia/laravel-fractal.svg?branch=master)](https://travis-ci.org/VICEMedia/laravel-fractal)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Fractal lets you present API data in a consistent way, by acting as an anti-corruption layer between your frontend and backend.

[Read up on Fractal here.](http://fractal.thephpleague.com/)

## Installation

Require this package

```
composer require vice/laravel-fractal
```

And then add the following to the service providers in `app.php`

```
Vice\LaravelFractal\FractalServiceProvider::class,
```

## Usage

To send a JSON representation of a single entity to the frontend simply

```php
public function show($id)
{
    //...

    fractalResponse()->item($thing, new ThingTransformer);
}
```

To send a JSON representation of a collection of entities to the frontend simply

```php
public function index()
{
    //...

    fractalResponse()->collection($things, new ThingTransformer);
}
```

*The collection method also supports paginators, and will automatically append their state under a `meta` key*

If you need to transform data without immediately using it in a response you may inject `Vice\LaravelFractal\FractalService`
into your controller / class and use it as so:

```php
$json = $this->fractalService->item($thing, new ThingTransformer)->toJson();
```

## Contributing

Please open any issues or pull requests on GitHub. This package is maintained by max.brokman@vice.com

For PRs please run the style fixer (vendor/bin/php-cs-fixer fix) before submitting.
