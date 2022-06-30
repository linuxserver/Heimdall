<p align="center">
    <img src="https://raw.githubusercontent.com/nunomaduro/collision/stable/docs/logo.png" alt="Collision logo" width="480">
    <br>
    <img src="https://raw.githubusercontent.com/nunomaduro/collision/stable/docs/example.png" alt="Collision code example" height="300">
</p>

<p align="center">
  <a href="https://github.com/nunomaduro/collision/actions"><img src="https://img.shields.io/github/workflow/status/nunomaduro/collision/Tests.svg" alt="Build Status"></img></a>
  <a href="https://scrutinizer-ci.com/g/nunomaduro/collision"><img src="https://img.shields.io/scrutinizer/g/nunomaduro/collision.svg" alt="Quality Score"></img></a>
  <a href="https://packagist.org/packages/nunomaduro/collision"><img src="https://poser.pugx.org/nunomaduro/collision/d/total.svg" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/nunomaduro/collision"><img src="https://poser.pugx.org/nunomaduro/collision/v/stable.svg" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/nunomaduro/collision"><img src="https://poser.pugx.org/nunomaduro/collision/license.svg" alt="License"></a>
</p>

---

Collision was created by, and is maintained by **[Nuno Maduro](https://github.com/nunomaduro)**, and is a package designed to give you beautiful error reporting when interacting with your app through the command line.

* It's included on **[Laravel](https://laravel.com)**, the most popular free, open-source PHP framework in the world.
* Built on top of the **[Whoops](https://github.com/filp/whoops)** error handler.
* Supports [Laravel](https://github.com/laravel/laravel), [Symfony](https://symfony.com), [PHPUnit](https://github.com/sebastianbergmann/phpunit), and many other frameworks.

## Installation & Usage

> **Requires [PHP 7.3+](https://php.net/releases/)**

Require Collision using [Composer](https://getcomposer.org):

```bash
composer require nunomaduro/collision --dev
```

## Laravel Version Compatibility

 Laravel  | Collision
:---------|:----------
 6.x      | 3.x
 7.x      | 4.x
 8.x      | 5.x

As an example, here is how to require Collision on Laravel 6.x:

```bash
composer require nunomaduro/collision:^3.0 --dev
```

## Phpunit adapter

Phpunit must be 9.0 or higher.

Add the Collision `printerClass` to your `phpunit.xml` in the `phpunit` section:

```xml
<phpunit
        printerClass="NunoMaduro\Collision\Adapters\Phpunit\Printer">
```

## No adapter

You need to register the handler in your code:

```php
(new \NunoMaduro\Collision\Provider)->register();
```

## Contributing

Thank you for considering to contribute to Collision. All the contribution guidelines are mentioned [here](CONTRIBUTING.md).

You can have a look at the [CHANGELOG](CHANGELOG.md) for constant updates & detailed information about the changes. You can also follow the twitter account for latest announcements or just come say hi!: [@enunomaduro](https://twitter.com/enunomaduro)

## Support the development
**Do you like this project? Support it by donating**

- PayPal: [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=66BYDWAT92N6L)
- Patreon: [Donate](https://www.patreon.com/nunomaduro)

## License

Collision is an open-sourced software licensed under the [MIT license](LICENSE.md).

Logo by [Caneco](https://twitter.com/caneco).
