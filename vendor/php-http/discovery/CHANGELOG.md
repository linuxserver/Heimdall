# Change Log

## 1.4.0 - 2018-02-06

### Added

- Discovery support for nyholm/psr7

## 1.3.0 - 2017-08-03

### Added

- Discovery support for CakePHP adapter
- Discovery support for Zend adapter
- Discovery support for Artax adapter

## 1.2.1 - 2017-03-02

### Fixed

- Fixed minor issue with `MockClientStrategy`, also added more tests. 

## 1.2.0 - 2017-02-12

### Added

- MockClientStrategy class.

## 1.1.1 - 2016-11-27

### Changed

- Made exception messages clearer. `StrategyUnavailableException` is no longer the previous exception to `DiscoveryFailedException`.
- `CommonClassesStrategy` is using `self` instead of `static`. Using `static` makes no sense when `CommonClassesStrategy` is final.

## 1.1.0 - 2016-10-20

### Added

- Discovery support for Slim Framework factories

## 1.0.0 - 2016-07-18

### Added

- Added back `Http\Discovery\NotFoundException` to preserve BC with 0.8 version. You may upgrade from 0.8.x and 0.9.x to 1.0.0 without any BC breaks.
- Added interface `Http\Discovery\Exception` which is implemented by all our exceptions

### Changed

- Puli strategy renamed to Puli Beta strategy to prevent incompatibility with a future Puli stable

### Deprecated

- For BC reasons, the old `Http\Discovery\NotFoundException` (extending the new exception) will be thrown until version 2.0


## 0.9.1 - 2016-06-28

### Changed

- Dropping PHP 5.4 support because we use the ::class constant.


## 0.9.0 - 2016-06-25

### Added

- Discovery strategies to find classes

### Changed

- [Puli](http://puli.io) made optional
- Improved exceptions
- **[BC] `NotFoundException` moved to `Http\Discovery\Exception\NotFoundException`**


## 0.8.0 - 2016-02-11

### Changed

- Puli composer plugin must be installed separately


## 0.7.0 - 2016-01-15

### Added

- Temporary puli.phar (Beta 10) executable

### Changed

- Updated HTTPlug dependencies
- Updated Puli dependencies
- Local configuration to make tests passing

### Removed

- Puli CLI dependency


## 0.6.4 - 2016-01-07

### Fixed

- Puli [not working](https://twitter.com/PuliPHP/status/685132540588507137) with the latest json-schema


## 0.6.3 - 2016-01-04

### Changed

- Adjust Puli dependencies


## 0.6.2 - 2016-01-04

### Changed

- Make Puli CLI a requirement


## 0.6.1 - 2016-01-03

### Changed

- More flexible Puli requirement


## 0.6.0 - 2015-12-30

### Changed

- Use [Puli](http://puli.io) for discovery
- Improved exception messages


## 0.5.0 - 2015-12-25

### Changed

- Updated message factory dependency (php-http/message)


## 0.4.0 - 2015-12-17

### Added

- Array condition evaluation in the Class Discovery

### Removed

- Message factories (moved to php-http/utils)


## 0.3.0 - 2015-11-18

### Added

- HTTP Async Client Discovery
- Stream factories

### Changed

- Discoveries and Factories are final
- Message and Uri factories have the type in their names
- Diactoros Message factory uses Stream factory internally

### Fixed

- Improved docblocks for API documentation generation


## 0.2.0 - 2015-10-31

### Changed

- Renamed AdapterDiscovery to ClientDiscovery


## 0.1.1 - 2015-06-13

### Fixed

- Bad HTTP Adapter class name for Guzzle 5


## 0.1.0 - 2015-06-12

### Added

- Initial release
