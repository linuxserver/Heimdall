# Painless HTML generation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-html.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-html)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-html.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-html)

This package helps you generate HTML using a clean, simple and easy to read API. All elements can be dynamically generated and put together. The HTML builder helps you generate dynamically assigned form elements based on your selected model, the session or a default value.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-html.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-html)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Kruikstraat 22, 2018 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:

``` bash
composer require spatie/laravel-html
```

And optionally register an alias for the facade.

```php
// config/app.php
'aliases' => [
    ...
    'Html' => Spatie\Html\Facades\Html::class,
];
```

## Usage

### Concepts

Elements—classes under the `Spatie\Html\Elements` namespace—are generally created via a `Spatie\Html\Html` builder instance.

```php
html()->span()->text('Hello world!');
```

Element attributes and contents are modified via with fluent methods which return a new instance. This means element instances are immutable.

```php
$icon = html()->span()->class('fa');

$icon->class('fa-eye'); // '<span class="fa fa-eye"></span>'
$icon->class('fa-eye-slash'); // '<span class="fa fa-eye-slash"></span>'
```

Element classes don't have any knowledge of the outside world. Any coupling to other concepts, like requests and sessions, should happen in the builder class, not on the element classes.

By convention, we assume that builder methods will modify values to our advantage (like pulling old values from the session on a failed form request), and element methods will be deterministic.

```php
// This will try to resolve an initial value, and fall back to 'hello@example.com'
$email = html()->email('email', 'hello@example.com');

// This will always have 'hello@example.com' as it's value
$email = html()->email('email')->value('hello@example.com');
```

## Upgrading

### From v1 to v2

Version 2 was created because the typehints in version 1 was holding the package back in some cases (like multiple select which requires an array of values instead of a string which was assumed).

Luckily, bumping the version number in `composer.json` and running `composer update` should be non-breaking. Here are some caveats to look out for:

- The package now ships with a `html()` function by default, which returns an instance of the `Html` builder class. If you've defined your own method, you'll need to remove it.
- Various type hints have been removed throughout the package, if you've extended a class to override its methods, you'll need to update them accordingly (everything still behaves the same!)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail [security@spatie.be](mailto:security@spatie.be) instead of using the issue tracker.

## Credits

- [Sebastian De Deyne](https://github.com/sebastiandedeyne)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
