# Change Log


## Unreleased

## 1.7.0 - 2018-08-15

### Fixed

- Fix CurlCommandFormatter for binary request payloads
- Fix QueryParam authentication to assemble proper URL regardless of PHP `arg_separator.output` directive
- Do not pass `null` parameters to `Clue\StreamFilter\fun`

### Changed

- Dropped tests on HHVM

## 1.6.0 - 2017-07-05

### Added

- CookieUtil::parseDate to create a date from cookie date string

### Fixed

- Fix curl command of CurlFormatter when there is an user-agent header   

## 1.5.0 - 2017-02-14

### Added

- Check for empty string in Stream factories
- Cookie::createWithoutValidation Static constructor to create a cookie. Will not perform any attribute validation during instantiation.
- Cookie::isValid Method to check if cookie attributes are valid.

### Fixed

- FilteredStream::getSize returns null because the contents size is unknown.
- Stream factories does not rewinds streams. The previous behavior was not coherent between factories and inputs. 

### Deprecated

- FilteredStream::getReadFilter The read filter is internal and should never be used by consuming code.
- FilteredStream::getWriteFilter We did not implement writing to the streams at all. And if we do, the filter is an internal information and should not be used by consuming code.


## 1.4.1 - 2016-12-16

### Fixed

- Cookie::matchPath Cookie with root path (`/`) will not match sub path (e.g. `/cookie`).


## 1.4.0 - 2016-10-20

### Added

- Message, stream and URI factories for [Slim Framework](https://github.com/slimphp/Slim)
- BufferedStream that allow you to decorate a non-seekable stream with a seekable one.
- cUrlFormatter to be able to redo the request with a cURL command


## 1.3.1 - 2016-07-15

### Fixed

- FullHttpMessageFormatter will not read from streams that you cannot rewind (non-seekable)
- FullHttpMessageFormatter will not read from the stream if $maxBodyLength is zero
- FullHttpMessageFormatter rewinds streams after they are read


## 1.3.0 - 2016-07-14

### Added

- FullHttpMessageFormatter to include headers and body in the formatted message

### Fixed

- #41: Response builder broke header value


## 1.2.0 - 2016-03-29

### Added

- The RequestMatcher is built after the Symfony RequestMatcher and separates
   scheme, host and path expressions and provides an option to filter on the
   method
- New RequestConditional authentication method using request matchers
- Add automatic basic auth info detection based on the URL

### Changed

- Improved ResponseBuilder

### Deprecated

- RegexRequestMatcher, use RequestMatcher instead
- Matching authenitcation method, use RequestConditional instead


## 1.1.0 - 2016-02-25

### Added

 - Add a request matcher interface and regex implementation
 - Add a callback request matcher implementation
 - Add a ResponseBuilder, to create PSR7 Response from a string

### Fixed

 - Fix casting string on a FilteredStream not filtering the output


## 1.0.0 - 2016-01-27


## 0.2.0 - 2015-12-29

### Added

- Autoregistration of stream filters using Composer autoload
- Cookie
- [Apigen](http://www.apigen.org/) configuration


## 0.1.2 - 2015-12-26

### Added

- Request and response factory bindings

### Fixed

- Chunk filter namespace in Dechunk stream


## 0.1.1 - 2015-12-25

### Added

- Formatter


## 0.1.0 - 2015-12-24

### Added

- Authentication
- Encoding
- Message decorator
- Message factory (Guzzle, Diactoros)
