# Change Log

## 1.1.0 - 2016-08-31

- Added HttpFulfilledPromise and HttpRejectedPromise which respect the HttpAsyncClient interface

## 1.0.0 - 2016-01-26

### Removed

- Stability configuration from composer


## 1.0.0-RC1 - 2016-01-12

### Changed

- Updated package files
- Updated promise dependency to RC1


## 1.0.0-beta - 2015-12-17

### Added

- Puli configuration and binding types

### Changed

- Exception concept


## 1.0.0-alpha3 - 2015-12-13

### Changed

- Async client does not throw exceptions

### Removed

- Promise interface moved to its own repository: [php-http/promise](https://github.com/php-http/promise)


## 1.0.0-alpha2 - 2015-11-16

### Added

- Async client and Promise interface


## 1.0.0-alpha - 2015-10-26

### Added

- Better domain exceptions.

### Changed

- Purpose of the library: general HTTP CLient abstraction.

### Removed

- Request options: they should be configured at construction time.
- Multiple request sending: should be done asynchronously using Async Client.
- `getName` method


## 0.1.0 - 2015-06-03

### Added

- Initial release
