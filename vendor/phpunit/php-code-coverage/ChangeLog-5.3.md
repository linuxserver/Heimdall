# Changes in PHP_CodeCoverage 5.3

All notable changes of the PHP_CodeCoverage 5.3 release series are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [5.3.2] - 2018-04-06

### Fixed

* Fixed [#602](https://github.com/sebastianbergmann/php-code-coverage/pull/602): Regression introduced in version 5.3.1

## [5.3.1] - 2018-04-06

### Changed

* `Clover`, `Crap4j`, and `PHP` report writers now raise an exception when their call to `file_put_contents()` fails

### Fixed

* Fixed [#559](https://github.com/sebastianbergmann/php-code-coverage/issues/559): Ignored classes and methods are reported as 100% covered

## [5.3.0] - 2017-12-06

### Added

* Added option to ignore the `forceCoversAnnotation="true"` setting for a single test

### Fixed

* Fixed [#564](https://github.com/sebastianbergmann/php-code-coverage/issues/564): `setDisableIgnoredLines(true)` disables more than it should

[5.3.2]: https://github.com/sebastianbergmann/php-code-coverage/compare/5.3.1...5.3.2
[5.3.1]: https://github.com/sebastianbergmann/php-code-coverage/compare/5.3.0...5.3.1
[5.3.0]: https://github.com/sebastianbergmann/php-code-coverage/compare/5.2...5.3.0

