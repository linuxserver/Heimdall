# Change Log

## 1.2.0 - 2021-05-21

- Refactored MultipartStreamBuilder to clean up and allow injecting data without a filename
- Dynamically use memory or temp file to buffer the stream content.

## 1.1.2 - 2020-07-13

- Support PHP 8.0

## 1.1.1 - 2020-07-04

- Fixed mistake in PHPDoc type.

## 1.1.0 - 2019-08-22

- Added support for PSR-17 factories.
- Dropped support for PHP < 7.1

## 1.0.0 - 2017-05-21

No changes from 0.2.0.

## 0.2.0 - 2017-02-20

You may do a BC update to version 0.2.0 if you are sure that you are not adding
multiple resources with the same name to the Builder.

### Fixed

- Make sure one can add resources with same name without overwrite.

## 0.1.6 - 2017-02-16

### Fixed

- Performance improvements by avoid using `uniqid()`.

## 0.1.5 - 2017-02-14

### Fixed

- Support for non-readable streams. This fix was needed because flaws in Guzzle, Zend and Slims implementations of PSR-7.

## 0.1.4 - 2016-12-31

### Added

- Added support for resetting the builder

## 0.1.3 - 2016-12-22

### Added

- Added `CustomMimetypeHelper` to allow you to configure custom mimetypes.

### Changed

- Using regular expression instead of `basename($filename)` because basename is depending on locale.

## 0.1.2 - 2016-08-31

### Added

- Support for Outlook msg files.

## 0.1.1 - 2016-08-10

### Added

- Support for Apple passbook.

## 0.1.0 - 2016-07-19

### Added

- Initial release
