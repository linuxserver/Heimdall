<?php
/**
 * @see       http://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2017-2018 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Response;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use Zend\Diactoros\Exception;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

use function sprintf;

/**
 * Serialize or deserialize response messages to/from arrays.
 *
 * This class provides functionality for serializing a ResponseInterface instance
 * to an array, as well as the reverse operation of creating a Response instance
 * from an array representing a message.
 */
final class ArraySerializer
{
    /**
     * Serialize a response message to an array.
     */
    public static function toArray(ResponseInterface $response) : array
    {
        return [
            'status_code'      => $response->getStatusCode(),
            'reason_phrase'    => $response->getReasonPhrase(),
            'protocol_version' => $response->getProtocolVersion(),
            'headers'          => $response->getHeaders(),
            'body'             => (string) $response->getBody(),
        ];
    }

    /**
     * Deserialize a response array to a response instance.
     *
     * @throws Exception\DeserializationException when cannot deserialize response
     */
    public static function fromArray(array $serializedResponse) : Response
    {
        try {
            $body = new Stream('php://memory', 'wb+');
            $body->write(self::getValueFromKey($serializedResponse, 'body'));

            $statusCode      = self::getValueFromKey($serializedResponse, 'status_code');
            $headers         = self::getValueFromKey($serializedResponse, 'headers');
            $protocolVersion = self::getValueFromKey($serializedResponse, 'protocol_version');
            $reasonPhrase    = self::getValueFromKey($serializedResponse, 'reason_phrase');

            return (new Response($body, $statusCode, $headers))
                ->withProtocolVersion($protocolVersion)
                ->withStatus($statusCode, $reasonPhrase);
        } catch (Throwable $exception) {
            throw Exception\DeserializationException::forResponseFromArray($exception);
        }
    }

    /**
     * @param array $data
     * @param string $key
     * @param string $message
     * @return mixed
     * @throws UnexpectedValueException
     */
    private static function getValueFromKey(array $data, string $key, string $message = null)
    {
        if (isset($data[$key])) {
            return $data[$key];
        }
        if ($message === null) {
            $message = sprintf('Missing "%s" key in serialized response', $key);
        }
        throw new Exception\DeserializationException($message);
    }
}
