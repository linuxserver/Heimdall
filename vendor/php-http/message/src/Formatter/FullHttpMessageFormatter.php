<?php

namespace Http\Message\Formatter;

use Http\Message\Formatter;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A formatter that prints the complete HTTP message.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class FullHttpMessageFormatter implements Formatter
{
    /**
     * The maximum length of the body.
     *
     * @var int
     */
    private $maxBodyLength;

    /**
     * @param int $maxBodyLength
     */
    public function __construct($maxBodyLength = 1000)
    {
        $this->maxBodyLength = $maxBodyLength;
    }

    /**
     * {@inheritdoc}
     */
    public function formatRequest(RequestInterface $request)
    {
        $message = sprintf(
            "%s %s HTTP/%s\n",
            $request->getMethod(),
            $request->getRequestTarget(),
            $request->getProtocolVersion()
        );

        foreach ($request->getHeaders() as $name => $values) {
            $message .= $name.': '.implode(', ', $values)."\n";
        }

        return $this->addBody($request, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function formatResponse(ResponseInterface $response)
    {
        $message = sprintf(
            "HTTP/%s %s %s\n",
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        foreach ($response->getHeaders() as $name => $values) {
            $message .= $name.': '.implode(', ', $values)."\n";
        }

        return $this->addBody($response, $message);
    }

    /**
     * Add the message body if the stream is seekable.
     *
     * @param MessageInterface $request
     * @param string           $message
     *
     * @return string
     */
    private function addBody(MessageInterface $request, $message)
    {
        $stream = $request->getBody();
        if (!$stream->isSeekable() || 0 === $this->maxBodyLength) {
            // Do not read the stream
            $message .= "\n";
        } else {
            $message .= "\n".mb_substr($stream->__toString(), 0, $this->maxBodyLength);
            $stream->rewind();
        }

        return $message;
    }
}
