# Change Log

## 1.3.1 - 2024-03-15

- Made nullable parameter types explicit (PHP 8.4 compatibility)

## 1.3.0 - 2024-01-04

### Fixed

- Reverted generic annotations on promise - as `then` returns another promise, there seems no way to properly document this.

## 1.2.1 - 2023-11-08

### Added

- Fixed PHPDoc for `wait()` and `then()`'s `onRejected` callable

## 1.2.0 - 2023-10-24

### Added

- Generic annotations

## 1.1.0 - 2020-07-07

### Added

- Test with PHP 7.1, 7.2, 7.3, 7.4 and 8.0

### Removed

- PHP 5 and 7.0 support

### Fixed

- Fixed PHPDoc for `Promise::then`

## 1.0.0 - 2016-01-26

### Removed

- PSR-7 dependency


## 1.0.0-RC1 - 2016-01-12

### Added

- Tests for full coverage

## Changed

- Updated package files
- Clarified wait method behavior
- Contributing guide moved to the documentation


## 0.1.1 - 2015-12-24

## Added

- Fulfilled and Rejected promise implementations


## 0.1.0 - 2015-12-13

## Added

- Promise interface
