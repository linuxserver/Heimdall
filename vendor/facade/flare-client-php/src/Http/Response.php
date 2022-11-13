<?php

namespace Facade\FlareClient\Http;

class Response
{
    private $headers;

    private $body;

    private $error;

    public function __construct($headers, $body, $error)
    {
        $this->headers = $headers;

        $this->body = $body;

        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return bool
     */
    public function hasBody()
    {
        return $this->body != false;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return null|int
     */
    public function getHttpResponseCode()
    {
        if (! isset($this->headers['http_code'])) {
            return;
        }

        return (int) $this->headers['http_code'];
    }
}
