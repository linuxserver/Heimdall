Changelog
=========

## UNRELEASED

## 1.4.0 (2018-12-25)

### Added

* added `Assert::ip()`
* added `Assert::ipv4()`
* added `Assert::ipv6()`
* added `Assert::notRegex()`
* added `Assert::interfaceExists()`
* added `Assert::isList()`
* added `Assert::isMap()`
* added polyfill for ctype

### Fixed

* Special case when comparing objects implementing `__toString()`

## 1.3.0 (2018-01-29)

### Added 

* added `Assert::minCount()`
* added `Assert::maxCount()`
* added `Assert::countBetween()`
* added `Assert::isCountable()`
* added `Assert::notWhitespaceOnly()`
* added `Assert::natural()`
* added `Assert::notContains()`
* added `Assert::isArrayAccessible()`
* added `Assert::isInstanceOfAny()`
* added `Assert::isIterable()`

### Fixed

* `stringNotEmpty` will no longer report "0" is an empty string

### Deprecation

* deprecated `Assert::isTraversable()` in favor of `Assert::isIterable()`

## 1.2.0 (2016-11-23)

 * added `Assert::throws()`
 * added `Assert::count()`
 * added extension point `Assert::reportInvalidArgument()` for custom subclasses

## 1.1.0 (2016-08-09)

 * added `Assert::object()`
 * added `Assert::propertyExists()`
 * added `Assert::propertyNotExists()`
 * added `Assert::methodExists()`
 * added `Assert::methodNotExists()`
 * added `Assert::uuid()`

## 1.0.2 (2015-08-24)

 * integrated Style CI
 * add tests for minimum package dependencies on Travis CI

## 1.0.1 (2015-05-12)

 * added support for PHP 5.3.3

## 1.0.0 (2015-05-12)

 * first stable release

## 1.0.0-beta (2015-03-19)

 * first beta release
