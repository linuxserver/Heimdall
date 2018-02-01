# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.1.3] - 2018-02-01

### Changed

* This component is now compatible with version 3 of `sebastian/diff`

## [2.1.2] - 2018-01-12

### Fixed

* Fix comparison of DateTimeImmutable objects

## [2.1.1] - 2017-12-22

### Fixed

* Fixed [phpunit/#2923](https://github.com/sebastianbergmann/phpunit/issues/2923): Unexpected failed date matching

## [2.1.0] - 2017-11-03

### Added

* Added `SebastianBergmann\Comparator\Factory::reset()` to unregister all non-default comparators
* Added support for `phpunit/phpunit-mock-objects` version `^5.0`

[2.1.3]: https://github.com/sebastianbergmann/comparator/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/sebastianbergmann/comparator/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/sebastianbergmann/comparator/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/sebastianbergmann/comparator/compare/2.0.2...2.1.0
