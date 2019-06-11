# Change Log

## [2.3.0] - 2019-03-30
### Added
- Added support for DateTimeImmutable via DateTimeInterface
- Added support for PHP 7.3
- Started listing projects that use the library
### Changed
- Errors should now report a human readable position in the cron expression, instead of starting at 0
### Fixed
- N/A

## [2.2.0] - 2018-06-05
### Added
- Added support for steps larger than field ranges (#6)
## Changed
- N/A
### Fixed
- Fixed validation for numbers with leading 0s (#12)

## [2.1.0] - 2018-04-06
### Added
- N/A
### Changed
- Upgraded to PHPUnit 6 (#2)
### Fixed
- Refactored timezones to deal with some inconsistent behavior (#3)
- Allow ranges and lists in same expression (#5)
- Fixed regression where literals were not converted to their numerical counterpart (#)

## [2.0.0] - 2017-10-12
### Added
- N/A

### Changed
- Dropped support for PHP 5.x
- Dropped support for the YEAR field, as it was not part of the cron standard

### Fixed
- Reworked validation for all the field types
- Stepping should now work for 1-indexed fields like Month (#153)

## [1.2.0] - 2017-01-22
### Added
- Added IDE, CodeSniffer, and StyleCI.IO support

### Changed
- Switched to PSR-4 Autoloading

### Fixed
- 0 step expressions are handled better
- Fixed `DayOfMonth` validation to be more strict
- Typos

## [1.1.0] - 2016-01-26
### Added
- Support for non-hourly offset timezones 
- Checks for valid expressions

### Changed
- Max Iterations no longer hardcoded for `getRunDate()`
- Supports DateTimeImmutable for newer PHP verions

### Fixed
- Fixed looping bug for PHP 7 when determining the last specified weekday of a month

## [1.0.3] - 2013-11-23
### Added
- Now supports expressions with any number of extra spaces, tabs, or newlines

### Changed
- Using static instead of self in `CronExpression::factory`

### Fixed
- Fixes issue [#28](https://github.com/mtdowling/cron-expression/issues/28) where PHP increments of ranges were failing due to PHP casting hyphens to 0
- Only set default timezone if the given $currentTime is not a DateTime instance ([#34](https://github.com/mtdowling/cron-expression/issues/34))
