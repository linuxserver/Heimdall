# Changes in sebastianbergmann/environment

All notable changes in `sebastianbergmann/environment` are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.1.0] - 2017-07-01

### Added

* Implemented [#21](https://github.com/sebastianbergmann/environment/issues/21): Equivalent of `PHP_OS_FAMILY` (for PHP < 7.2) 

## [3.0.4] - 2017-06-20

### Fixed

* Fixed [#20](https://github.com/sebastianbergmann/environment/pull/20): PHP 7 mode of HHVM not forced

## [3.0.3] - 2017-05-18

### Fixed

* Fixed [#18](https://github.com/sebastianbergmann/environment/issues/18): `Uncaught TypeError: preg_match() expects parameter 2 to be string, null given`

## [3.0.2] - 2017-04-21

### Fixed

* Fixed [#17](https://github.com/sebastianbergmann/environment/issues/17): `Uncaught TypeError: trim() expects parameter 1 to be string, boolean given`

## [3.0.1] - 2017-04-21

### Fixed

* Fixed inverted logic in `Runtime::discardsComments()`

## [3.0.0] - 2017-04-21

### Added

* Implemented `Runtime::discardsComments()` for querying whether the PHP runtime discards annotations

### Removed

* This component is no longer supported on PHP 5.6

[3.1.0]: https://github.com/sebastianbergmann/phpunit/compare/3.0...3.1.0
[3.0.4]: https://github.com/sebastianbergmann/phpunit/compare/3.0.3...3.0.4
[3.0.3]: https://github.com/sebastianbergmann/phpunit/compare/3.0.2...3.0.3
[3.0.2]: https://github.com/sebastianbergmann/phpunit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/phpunit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpunit/compare/2.0...3.0.0

