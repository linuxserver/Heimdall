# Changelog

All notable changes to `uri-template` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## v2.0.1 - 2026-08-24

### Added

- Added support for PHP 8.6

## v2.0.0 - 2026-07-20

### Changed

- Invalid URI template syntax now throws `InvalidArgumentException` instead of expanding partially
- Literal template text is now validated and valid non-ASCII literals are pct-encoded
- URI template variable names and modifiers are now validated according to RFC 6570
- Prefix modifiers are now rejected for list and map values
- Referenced variable values are now validated before expansion
- Dense zero-indexed arrays are expanded as lists and sparse or mixed-key arrays as maps
- `UriTemplate` is now non-instantiable; use `UriTemplate::expand()` statically
- Nested null values in lists and maps are now treated as undefined members and omitted
- Variable values must now be valid UTF-8; invalid byte sequences throw `InvalidArgumentException`
- Booleans now expand as `1` and `0` at every nesting level
- Non-finite floats now throw `InvalidArgumentException` instead of expanding as `INF` or `NAN`
- Non-empty lists whose members are all null are now treated as defined variables
- Variable specifier whitespace padding is now detected with the real whitespace set
- `UriTemplate` now rejects native PHP serialization and unserialization
- Referenced variable values are now detached and formed before expansion begins
- Template syntax is now validated for every expression before variable values are formed
- Stringable objects are now converted to strings once per value position while values are formed
- Invalid UTF-8 in variable values and map keys is now rejected while values are formed
- Finite floats are now converted to their expansion strings while values are formed
- Exceptions thrown by `__toString()` now surface while values are formed, before any output exists

### Fixed

- Fixed invalid template expressions being accepted as query and path-style parameter names
- Fixed prefix modifiers counting bytes instead of Unicode code points and pct-encoded triplets
- Fixed unsupported variable shapes producing warnings, conversion errors, or `Array` output
- Fixed path-style parameter explode rendering empty members as `name=` instead of bare `name`
- Fixed map keys not using the operator's allow set under reserved and fragment expansion
- Fixed map explode rendering empty members as `name=` instead of bare `name` for unnamed operators
- Fixed the documented variables array type rejecting integer keys for numeric variable names
- Fixed all-null maps throwing with prefix modifiers instead of being skipped as undefined
- Fixed prefix modifiers splitting Unicode code points encoded as multiple pct-encoded triplets
- Fixed path-style composites rendering bare `name` instead of `name=` for empty joined members
- Fixed null nested query array members being rejected for their keys instead of being omitted
- Fixed float values expanding with the locale decimal separator on PHP versions before 8.0
- Fixed PCRE engine failures on very long variable names being reported as invalid template syntax
- Fixed invalid UTF-8 errors for list and map members not reporting the member path
- Fixed exception messages embedding raw controls or malformed UTF-8 from expressions and map keys
- Fixed invalid UTF-8 in literal text reported at the segment offset, not the first invalid byte
- Fixed PCRE engine failures during literal text validation being reported as invalid UTF-8
- Fixed PCRE engine failures during variable UTF-8 validation not being reported as runtime errors
- Fixed PCRE engine failures during invalid UTF-8 offset recovery being reported as invalid UTF-8

### Removed

- Dropped support for PHP 7.2 and 7.3

## v1.0.10 - 2026-07-17

### Fixed

- Fixed prefix modifiers counting Unicode code points and pct-encoded characters instead of bytes

## v1.0.9 - 2026-07-08

### Changed

- Pass explicit trim characters ahead of the PHP 8.6 trim default change

## v1.0.8 - 2026-06-23

### Fixed

- Report PCRE errors when URI template value encoding fails

## v1.0.7 - 2026-06-12

### Fixed

- Fixed the operator's leading character being omitted when defined variables expand to empty strings
- Fixed non-finite float values emitting coercion warnings on PHP 8.5

## v1.0.6 - 2026-05-23

### Fixed

- Fixed empty nested arrays adding empty components to exploded query expansions
- Fixed nested query array keys being double-encoded during exploded query expansion
- Fixed reserved and fragment expansion preserving existing pct-encoded triplets in variable values

## v1.0.5 - 2025-08-22

### Changed

- Officially support PHP 8.5

## v1.0.4 - 2025-02-03

### Changed

- Officially support PHP 8.4

## v1.0.3 - 2023-12-03

### Changed

- Updated link to RFC 6570

## v1.0.2 - 2023-08-27

### Changed

- Officially support PHP 8.2 and 8.3

### Fixed

- Fixed using `0` as an expanded value

## v1.0.1 - 2021-10-07

### Changed

- Officially support PHP 8.1

## v1.0.0 - 2021-08-14

### Changed

- Dropped support for PHP 7.1

## v0.2.0 - 2020-07-21

### Added

- Support PHP 7.1 and 8.0

### Changed

- Renamed `GuzzleHttp\Utility\` to `GuzzleHttp\UriTemplate\`

### Fixed

- Delegate RFC 3986 query string encoding to PHP
- Fixed some bugs when parts ofs values are not strings

## v0.1.1 - 2020-06-30

### Fixed

- Fixed an error due to strict_types [d47d1b0a8e78a3fac1cd0f69d675fc9e06771ac8](https://github.com/guzzle/uri-template/commit/d47d1b0a8e78a3fac1cd0f69d675fc9e06771ac8)

## v0.1.0 - 2020-06-30

### Added
- Moved the `UriTemplate` class in this package
