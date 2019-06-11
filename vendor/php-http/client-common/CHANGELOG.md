# Change Log

## 1.9.1 - 2019-02-02

### Added

- Updated type hints in doc blocks.

## 1.9.0 - 2019-01-03

### Added

- Support for PSR-18 clients
- Added traits `VersionBridgePlugin` and `VersionBridgeClient` to help plugins and clients to support both
  1.x and 2.x version of `php-http/client-common` and `php-http/httplug`.

### Changed

- [RetryPlugin] Renamed the configuration options for the exception retry callback from `decider` to `exception_decider`
  and `delay` to `exception_delay`. The old names still work but are deprecated.

## 1.8.2 - 2018-12-14

### Changed

- When multiple cookies exist, a single header with all cookies is sent as per RFC 6265 Section 5.4
- AddPathPlugin will now trim of ending slashes in paths

## 1.8.1 - 2018-10-09

### Fixed

- Reverted change to RetryPlugin so it again waits when retrying to avoid "can only throw objects" error.

## 1.8.0 - 2018-09-21

### Added

 - Add an option on ErrorPlugin to only throw exception on response with 5XX status code.

### Changed

- AddPathPlugin no longer add prefix multiple times if a request is restarted - it now only adds the prefix if that request chain has not yet passed through the AddPathPlugin
- RetryPlugin no longer wait for retried requests and use a deferred promise instead

### Fixed

- Decoder plugin will now remove header when there is no more encoding, instead of setting to an empty array


## 1.7.0 - 2017-11-30

### Added 

- Symfony 4 support

### Changed

- Strict comparison in DecoderPlugin

## 1.6.0 - 2017-10-16

### Added

- Add HttpClientPool client to leverage load balancing and fallback mechanism [see the documentation](http://docs.php-http.org/en/latest/components/client-common.html) for more details.
- `PluginClientFactory` to create `PluginClient` instances.
- Added new option 'delay' for `RetryPlugin`.
- Added new option 'decider' for `RetryPlugin`.
- Supports more cookie date formats in the Cookie Plugin

### Changed

- The `RetryPlugin` does now wait between retries. To disable/change this feature you must write something like: 
 
```php
$plugin = new RetryPlugin(['delay' => function(RequestInterface $request, Exception $e, $retries) { 
  return 0; 
}); 
```

### Deprecated

- The `debug_plugins` option for `PluginClient` is deprecated and will be removed in 2.0. Use the decorator design pattern instead like in [ProfilePlugin](https://github.com/php-http/HttplugBundle/blob/de33f9c14252f22093a5ec7d84f17535ab31a384/Collector/ProfilePlugin.php).

## 1.5.0 - 2017-03-30

### Added

- `QueryDefaultsPlugin` to add default query parameters.

## 1.4.2 - 2017-03-18

### Deprecated

- `DecoderPlugin` does not longer claim to support `compress` content encoding

### Fixed

- `CookiePlugin` allows main domain cookies to be sent/stored for subdomains
- `DecoderPlugin` uses the right `FilteredStream` to handle `deflate` content encoding


## 1.4.1 - 2017-02-20

### Fixed

- Cast return value of `StreamInterface::getSize` to string in `ContentLengthPlugin`


## 1.4.0 - 2016-11-04

### Added

- Add Path plugin
- Base URI plugin that combines Add Host and Add Path plugins


## 1.3.0 - 2016-10-16

### Changed

- Fix Emulated Trait to use Http based promise which respect the HttpAsyncClient interface
- Require Httplug 1.1 where we use HTTP specific promises.
- RedirectPlugin: use the full URL instead of the URI to properly keep track of redirects
- Add AddPathPlugin for API URLs with base path
- Add BaseUriPlugin that combines AddHostPlugin and AddPathPlugin


## 1.2.1 - 2016-07-26

### Changed

- AddHostPlugin also sets the port if specified


## 1.2.0 - 2016-07-14

### Added

- Suggest separate plugins in composer.json
- Introduced `debug_plugins` option for `PluginClient`


## 1.1.0 - 2016-05-04

### Added

- Add a flexible http client providing both contract, and only emulating what's necessary
- HTTP Client Router: route requests to underlying clients
- Plugin client and core plugins moved here from `php-http/plugins`

### Deprecated

- Extending client classes, they will be made final in version 2.0


## 1.0.0 - 2016-01-27

### Changed

- Remove useless interface in BatchException


## 0.2.0 - 2016-01-12

### Changed

- Updated package files
- Updated HTTPlug to RC1


## 0.1.1 - 2015-12-26

### Added

- Emulated clients


## 0.1.0 - 2015-12-25

### Added

- Batch client from utils
- Methods client from utils
- Emulators and decorators from client-tools
