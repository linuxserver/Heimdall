# Changelog

All notable changes to Tokenizer are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [2.0.1] - 2025-12-08

### Fixed

- Removed the custom token `T_AMPERSAND` as PHP 8.1+ provides tokens for it already and our 2.0.0 release overwrote it. See [#44](https://github.com/theseer/tokenizer/issues/44) for details. 


## [2.0.0] - 2025-12-06

This release bumps the minimum version required to PHP 8.1.

### Added

- New API `XMLSerializer::appendToWriter` to allow easier integration and better performance when writing fragments (Thanks @staabm)

### Changed

- Internal change: Now uses `PHPToken::tokenize` in favor of `token_get_all` (requires PHP 8+) (Thanks @staabm)

## [1.3.1] - 2025-111-17

### Fixed

* [#37](https://github.com/theseer/tokenizer/issues/37): v1.3.0 introduced a breaking change on the token collection (ArrayAccess interface removed)

## [1.3.0] - 2025-11-13

### Changed

* Require at least PHP 7.3 for building, code should still be PHP 7.2 compliant
* Merge various performance improvements provided by @staabm
* Merge some code cleanups provided by @staabm

## [1.2.3] - 2024-03-03

### Changed

* Do not use implicitly nullable parameters

## [1.2.2] - 2023-11-20

### Fixed

* [#18](https://github.com/theseer/tokenizer/issues/18): Tokenizer fails on protobuf metadata files


## [1.2.1] - 2021-07-28

### Fixed

* [#13](https://github.com/theseer/tokenizer/issues/13): Fatal error when tokenizing files that contain only a single empty line


## [1.2.0] - 2020-07-13

This release is now PHP 8.0 compliant.

### Fixed

* Whitespace handling in general (only noticeable in the intermediate `TokenCollection`) is now consistent  

### Changed

* Updated `Tokenizer` to deal with changed whitespace handling in PHP 8.0
  The XMLSerializer was unaffected.


## [1.1.3] - 2019-06-14

### Changed

* Ensure XMLSerializer can deal with empty token collections

### Fixed

* [#2](https://github.com/theseer/tokenizer/issues/2): Fatal error in infection / phpunit


## [1.1.2] - 2019-04-04

### Changed

* Reverted PHPUnit 8 test update to stay PHP 7.0 compliant


## [1.1.1] - 2019-04-03

### Fixed

* [#1](https://github.com/theseer/tokenizer/issues/1): Empty file causes invalid array read 

### Changed

* Tests should now be PHPUnit 8 compliant


## [1.1.0] - 2017-04-07

### Added

* Allow use of custom namespace for XML serialization


## [1.0.0] - 2017-04-05

Initial Release

[1.3.1]: https://github.com/theseer/tokenizer/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/theseer/tokenizer/compare/1.2.3...1.3.0
[1.2.3]: https://github.com/theseer/tokenizer/compare/1.2.2...1.2.3
[1.2.2]: https://github.com/theseer/tokenizer/compare/1.2.1...1.2.2
[1.2.1]: https://github.com/theseer/tokenizer/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/theseer/tokenizer/compare/1.1.3...1.2.0
[1.1.3]: https://github.com/theseer/tokenizer/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/theseer/tokenizer/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/theseer/tokenizer/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/theseer/tokenizer/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/theseer/tokenizer/compare/b2493e57de80c1b7414219b28503fa5c6b4d0a98...1.0.0
