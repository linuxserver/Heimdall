# ChangeLog

All notable changes are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.

## [7.0.3] - 2026-05-20

### Fixed

* [#90](https://github.com/sebastianbergmann/exporter/issues/90): `Exporter::toArray()` silently drops a private property that is redeclared in a derived class

## [7.0.2] - 2025-09-24

### Changed

* Suppress `unexpected NAN value was coerced to string` warning triggered on PHP 8.5

## [7.0.1] - 2025-09-22

### Changed

* Suppress `not representable as an int, cast occurred` warning triggered on PHP 8.5

## [7.0.0] - 2025-02-07

### Removed

* This component is no longer supported on PHP 8.2

[7.0.3]: https://github.com/sebastianbergmann/exporter/compare/7.0.2...7.0.3
[7.0.2]: https://github.com/sebastianbergmann/exporter/compare/7.0.1...7.0.2
[7.0.1]: https://github.com/sebastianbergmann/exporter/compare/7.0.0...7.0.1
[7.0.0]: https://github.com/sebastianbergmann/exporter/compare/6.3...7.0.0
