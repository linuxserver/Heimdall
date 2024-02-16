# Change Log

# Version 1

## 1.8.1 - 2023-11-21

- Allow installation with Symfony 7.

## 1.8.0 - 2023-04-28

- Avoid PHP warning about serializing resources when serializing the response by detaching the stream.

## 1.7.6 - 2023-04-28

- Test with PHP 8.1 and 8.2
- Made phpspec tests compatible with PSR-7 2.0 strict typing
- Detect `null` and use 0 explicitly to calculate expiration

## 1.7.5 - 2022-01-18

- Allow installation with psr/cache 3.0 (1.0 and 2.0 are still allowed too)

## 1.7.4 - 2021-11-30

### Added

- Allow installation with Symfony 6

## 1.7.3 - 2021-11-03

### Changed

- Be more defensive about cache hits. A cache entry can technically contain `null`.

## 1.7.2 - 2021-04-14

### Added

- Allow installation with psr/cache 2.0 (1.0 still allowed too)

## 1.7.1 - 2020-07-13

### Added

- Support for PHP 8

## 1.7.0 - 2019-12-17

### Added

* Support for Symfony 5.
* Support for PSR-17 `StreamFactoryInterface`.
* Added `blacklisted_paths` option, which takes an array of `strings` (regular expressions) and allows to define paths, that shall not be cached in any case.

## 1.6.0 - 2019-01-23

### Added

* Support for HTTPlug 2 / PSR-18
* Added `cache_listeners` option, which takes an array of `CacheListener`s, who get notified and can optionally act on a Response based on a cache hit or miss event. An implementation, `AddHeaderCacheListener`, is provided which will add an `X-Cache` header to the response with this information.

## 1.5.0 - 2017-11-29

### Added

* Support for Symfony 4

### Changed

* Removed check if etag is a string. Etag can never be a string, it is always an array.

## 1.4.0 - 2017-04-05

### Added

- `CacheKeyGenerator` interface that allow you to configure how the PSR-6 cache key is created. There are two implementations
of this interface: `SimpleGenerator` (default) and `HeaderCacheKeyGenerator`.

### Fixed

- Issue where deprecation warning always was triggered. Not it is just triggered if `respect_cache_headers` is used.

## 1.3.0 - 2017-03-28

### Added

- New `methods` option which allows to configure the request methods which can be cached.
- New `respect_response_cache_directives` option to define specific cache directives to respect when handling responses.
- Introduced `CachePlugin::clientCache` and `CachePlugin::serverCache` factory methods to easily setup the plugin with
  the correct config settigns for each usecase.

### Changed

- The `no-cache` directive is now respected by the plugin and will not cache the response. If you need the previous behaviour, configure `respect_response_cache_directives`.
- We always rewind the stream after loading response from cache.

### Deprecated

- The `respect_cache_headers` option is deprecated and will be removed in 2.0. This option is replaced by the new `respect_response_cache_directives` option.
  If you had set `respect_cache_headers` to `false`, set the directives to `[]` to ignore all directives.


## 1.2.0 - 2016-08-16

### Changed

- The default value for `default_ttl` is changed from `null` to `0`.

### Fixed

- Issue when you use `respect_cache_headers=>false` in combination with `default_ttl=>null`.
- We allow `cache_lifetime` to be set to `null`.


## 1.1.0 - 2016-08-04

### Added

- Support for cache validation with ETag and Last-Modified headers. (Enabled automatically when the server sends the relevant headers.)
- `hash_algo` config option used for cache key generation (defaults to **sha1**).

### Changed

- Default hash algo used for cache generation (from **md5** to **sha1**).

### Fixed

- Cast max age header to integer in order to get valid expiration value.


## 1.0.0 - 2016-05-05

- Initial release
