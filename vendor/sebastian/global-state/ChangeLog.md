# Changes in sebastian/global-state

All notable changes in `sebastian/global-state` are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [5.0.5] - 2022-02-14

### Fixed

* [#34](https://github.com/sebastianbergmann/global-state/pull/34): Uninitialised typed static properties are not handled correctly

## [5.0.4] - 2022-02-10

### Fixed

* The `$includeTraits` parameter of `SebastianBergmann\GlobalState\Snapshot::__construct()` is not respected

## [5.0.3] - 2021-06-11

### Changed

* `SebastianBergmann\GlobalState\CodeExporter::globalVariables()` now generates code that is compatible with PHP 8.1

## [5.0.2] - 2020-10-26

### Fixed

* `SebastianBergmann\GlobalState\Exception` now correctly extends `\Throwable`

## [5.0.1] - 2020-09-28

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3 || ^8.0` to `>=7.3`

## [5.0.0] - 2020-08-07

### Changed

* The `SebastianBergmann\GlobalState\Blacklist` class has been renamed to `SebastianBergmann\GlobalState\ExcludeList`

## [4.0.0] - 2020-02-07

### Removed

* This component is no longer supported on PHP 7.2

## [3.0.2] - 2022-02-10

### Fixed

* The `$includeTraits` parameter of `SebastianBergmann\GlobalState\Snapshot::__construct()` is not respected

## [3.0.1] - 2020-11-30

### Changed

* Changed PHP version constraint in `composer.json` from `^7.2` to `>=7.2`

## [3.0.0] - 2019-02-01

### Changed

* `Snapshot::canBeSerialized()` now recursively checks arrays and object graphs for variables that cannot be serialized

### Removed

* This component is no longer supported on PHP 7.0 and PHP 7.1

[5.0.5]: https://github.com/sebastianbergmann/global-state/compare/5.0.4...5.0.5
[5.0.4]: https://github.com/sebastianbergmann/global-state/compare/5.0.3...5.0.4
[5.0.3]: https://github.com/sebastianbergmann/global-state/compare/5.0.2...5.0.3
[5.0.2]: https://github.com/sebastianbergmann/global-state/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/global-state/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/global-state/compare/4.0.0...5.0.0
[4.0.0]: https://github.com/sebastianbergmann/global-state/compare/3.0.2...4.0.0
[3.0.2]: https://github.com/sebastianbergmann/phpunit/compare/3.0.1...3.0.2
[3.0.1]: https://github.com/sebastianbergmann/phpunit/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpunit/compare/2.0.0...3.0.0

