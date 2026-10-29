# Guzzle URI Template

`guzzlehttp/uri-template` expands
[RFC 6570](https://datatracker.ietf.org/doc/html/rfc6570) URI templates in PHP.
It turns templates such as `/users/{id}{?tab}` into concrete URI strings using
values from an array.

Use this package when an API describes paths or links with URI templates. If you
only need to send HTTP requests, start with
[`guzzlehttp/guzzle`](https://github.com/guzzle/guzzle/blob/8.0/README.md);
install this package directly when your application needs URI template
expansion.

## Installation

```bash
composer require guzzlehttp/uri-template
```

## Version Guidance

| Version | Status       | PHP Version  |
|---------|--------------|--------------|
| 2.0     | Latest       | >=7.4,<8.7   |
| 1.0     | Maintenance  | >=7.2.5,<8.7 |

## Quick Start

```php
use GuzzleHttp\UriTemplate\UriTemplate;

$uri = UriTemplate::expand('/users/{id}{?tab}', [
    'id' => 123,
    'tab' => 'settings',
]);

echo $uri;
// /users/123?tab=settings
```

The first argument is an RFC 6570 URI template. The second argument is an array
of variables. Variable names in the array must match the names used by the
template.

## Documentation

- [URI Template Usage](docs/uri-template-usage.md)
- [Input Contract](docs/input-contract.md)
- [Upgrade Guide](UPGRADING.md)
- [Changelog](CHANGELOG.md)

## Testing

```bash
make test
```

## Security

If you discover a security vulnerability within this package, please send an
email to security@tidelift.com. All security vulnerabilities will be promptly
addressed. Please do not disclose security-related issues publicly until a fix
has been announced. Please see
[Security Policy](https://github.com/guzzle/uri-template/security/policy) for
more information.

## License

Guzzle URI Template is made available under the MIT License (MIT). Please see
[License File](LICENSE) for more information.

## For Enterprise

Available as part of the Tidelift Subscription

The maintainers of Guzzle and thousands of other packages are working with
Tidelift to deliver commercial support and maintenance for the open source
dependencies you use to build your applications. Save time, reduce risk, and
improve code health, while paying the maintainers of the exact dependencies you
use.
[Learn more.](https://tidelift.com/subscription/pkg/packagist-guzzlehttp-uri-template?utm_source=packagist-guzzlehttp-uri-template&utm_medium=referral&utm_campaign=enterprise&utm_term=repo)
