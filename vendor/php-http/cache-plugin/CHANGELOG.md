# Change Log

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
