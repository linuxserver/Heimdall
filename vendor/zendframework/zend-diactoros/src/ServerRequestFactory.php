<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use InvalidArgumentException;
use Psr\Http\Message\UploadedFileInterface;
use stdClass;
use UnexpectedValueException;

use function array_change_key_case;
use function array_key_exists;
use function explode;
use function implode;
use function is_array;
use function is_callable;
use function strtolower;

use const CASE_LOWER;

/**
 * Class for marshaling a request object from the current PHP environment.
 *
 * Logic largely refactored from the ZF2 Zend\Http\PhpEnvironment\Request class.
 *
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
abstract class ServerRequestFactory
{
    /**
     * Function to use to get apache request headers; present only to simplify mocking.
     *
     * @var callable
     */
    private static $apacheRequestHeaders = 'apache_request_headers';

    /**
     * Create a request from the supplied superglobal values.
     *
     * If any argument is not supplied, the corresponding superglobal value will
     * be used.
     *
     * The ServerRequest created is then passed to the fromServer() method in
     * order to marshal the request URI and headers.
     *
     * @see fromServer()
     * @param array $server $_SERVER superglobal
     * @param array $query $_GET superglobal
     * @param array $body $_POST superglobal
     * @param array $cookies $_COOKIE superglobal
     * @param array $files $_FILES superglobal
     * @return ServerRequest
     * @throws InvalidArgumentException for invalid file values
     */
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null
    ) {
        $server = normalizeServer(
            $server ?: $_SERVER,
            is_callable(self::$apacheRequestHeaders) ? self::$apacheRequestHeaders : null
        );
        $files   = normalizeUploadedFiles($files ?: $_FILES);
        $headers = marshalHeadersFromSapi($server);

        if (null === $cookies && array_key_exists('cookie', $headers)) {
            $cookies = parseCookieHeader($headers['cookie']);
        }

        return new ServerRequest(
            $server,
            $files,
            marshalUriFromSapi($server, $headers),
            marshalMethodFromSapi($server),
            'php://input',
            $headers,
            $cookies ?: $_COOKIE,
            $query ?: $_GET,
            $body ?: $_POST,
            marshalProtocolVersionFromSapi($server)
        );
    }

    /**
     * Access a value in an array, returning a default value if not found
     *
     * @deprecated since 1.8.0; no longer used internally.
     * @param string $key
     * @param array $values
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, array $values, $default = null)
    {
        if (array_key_exists($key, $values)) {
            return $values[$key];
        }

        return $default;
    }

    /**
     * Search for a header value.
     *
     * Does a case-insensitive search for a matching header.
     *
     * If found, it is returned as a string, using comma concatenation.
     *
     * If not, the $default is returned.
     *
     * @deprecated since 1.8.0; no longer used internally.
     * @param string $header
     * @param array $headers
     * @param mixed $default
     * @return string
     */
    public static function getHeader($header, array $headers, $default = null)
    {
        $header  = strtolower($header);
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists($header, $headers)) {
            $value = is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
            return $value;
        }

        return $default;
    }

    /**
     * Marshal the $_SERVER array
     *
     * Pre-processes and returns the $_SERVER superglobal.
     *
     * @deprected since 1.8.0; use Zend\Diactoros\normalizeServer() instead.
     * @param array $server
     * @return array
     */
    public static function normalizeServer(array $server)
    {
        return normalizeServer(
            $server ?: $_SERVER,
            is_callable(self::$apacheRequestHeaders) ? self::$apacheRequestHeaders : null
        );
    }

    /**
     * Normalize uploaded files
     *
     * Transforms each value into an UploadedFileInterface instance, and ensures
     * that nested arrays are normalized.
     *
     * @deprecated since 1.8.0; use \Zend\Diactoros\normalizeUploadedFiles instead.
     * @param array $files
     * @return array
     * @throws InvalidArgumentException for unrecognized values
     */
    public static function normalizeFiles(array $files)
    {
        return normalizeUploadedFiles($files);
    }

    /**
     * Marshal headers from $_SERVER
     *
     * @deprecated since 1.8.0; use Zend\Diactoros\marshalHeadersFromSapi().
     * @param array $server
     * @return array
     */
    public static function marshalHeaders(array $server)
    {
        return marshalHeadersFromSapi($server);
    }

    /**
     * Marshal the URI from the $_SERVER array and headers
     *
     * @deprecated since 1.8.0; use Zend\Diactoros\marshalUriFromSapi() instead.
     * @param array $server
     * @param array $headers
     * @return Uri
     */
    public static function marshalUriFromServer(array $server, array $headers)
    {
        return marshalUriFromSapi($server, $headers);
    }

    /**
     * Marshal the host and port from HTTP headers and/or the PHP environment
     *
     * @deprecated since 1.8.0; use Zend\Diactoros\marshalUriFromSapi() instead,
     *     and pull the host and port from the Uri instance that function
     *     returns.
     * @param stdClass $accumulator
     * @param array $server
     * @param array $headers
     */
    public static function marshalHostAndPortFromHeaders(stdClass $accumulator, array $server, array $headers)
    {
        $uri = marshalUriFromSapi($server, $headers);
        $accumulator->host = $uri->getHost();
        $accumulator->port = $uri->getPort();
    }

    /**
     * Detect the base URI for the request
     *
     * Looks at a variety of criteria in order to attempt to autodetect a base
     * URI, including rewrite URIs, proxy URIs, etc.
     *
     * @deprecated since 1.8.0; use Zend\Diactoros\marshalUriFromSapi() instead,
     *     and pull the path from the Uri instance that function returns.
     * @param array $server
     * @return string
     */
    public static function marshalRequestUri(array $server)
    {
        $uri = marshalUriFromSapi($server, []);
        return $uri->getPath();
    }

    /**
     * Strip the query string from a path
     *
     * @deprecated since 1.8.0; no longer used internally.
     * @param mixed $path
     * @return string
     */
    public static function stripQueryString($path)
    {
        return explode('?', $path, 2)[0];
    }
}
