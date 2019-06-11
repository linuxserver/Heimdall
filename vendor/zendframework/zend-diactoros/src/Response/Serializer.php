<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Zend\Diactoros\AbstractSerializer;
use Zend\Diactoros\Exception;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

use function preg_match;
use function sprintf;

final class Serializer extends AbstractSerializer
{
    /**
     * Deserialize a response string to a response instance.
     *
     * @throws Exception\SerializationException when errors occur parsing the message.
     */
    public static function fromString(string $message) : Response
    {
        $stream = new Stream('php://temp', 'wb+');
        $stream->write($message);
        return static::fromStream($stream);
    }

    /**
     * Parse a response from a stream.
     *
     * @throws Exception\InvalidArgumentException when the stream is not readable.
     * @throws Exception\SerializationException when errors occur parsing the message.
     */
    public static function fromStream(StreamInterface $stream) : Response
    {
        if (! $stream->isReadable() || ! $stream->isSeekable()) {
            throw new Exception\InvalidArgumentException('Message stream must be both readable and seekable');
        }

        $stream->rewind();

        [$version, $status, $reasonPhrase] = self::getStatusLine($stream);
        [$headers, $body]                  = self::splitStream($stream);

        return (new Response($body, $status, $headers))
            ->withProtocolVersion($version)
            ->withStatus((int) $status, $reasonPhrase);
    }

    /**
     * Create a string representation of a response.
     */
    public static function toString(ResponseInterface $response) : string
    {
        $reasonPhrase = $response->getReasonPhrase();
        $headers      = self::serializeHeaders($response->getHeaders());
        $body         = (string) $response->getBody();
        $format       = 'HTTP/%s %d%s%s%s';

        if (! empty($headers)) {
            $headers = "\r\n" . $headers;
        }

        $headers .= "\r\n\r\n";

        return sprintf(
            $format,
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reasonPhrase ? ' ' . $reasonPhrase : ''),
            $headers,
            $body
        );
    }

    /**
     * Retrieve the status line for the message.
     *
     * @return array Array with three elements: 0 => version, 1 => status, 2 => reason
     * @throws Exception\SerializationException if line is malformed
     */
    private static function getStatusLine(StreamInterface $stream) : array
    {
        $line = self::getLine($stream);

        if (! preg_match(
            '#^HTTP/(?P<version>[1-9]\d*\.\d) (?P<status>[1-5]\d{2})(\s+(?P<reason>.+))?$#',
            $line,
            $matches
        )) {
            throw Exception\SerializationException::forInvalidStatusLine();
        }

        return [$matches['version'], (int) $matches['status'], isset($matches['reason']) ? $matches['reason'] : ''];
    }
}
