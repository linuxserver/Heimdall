<?php

namespace Github\Api;

use Psr\Http\Message\ResponseInterface;

/**
 * A trait to make sure we add accept headers on all requests.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
trait AcceptHeaderTrait
{
    /** @var string */
    protected $acceptHeaderValue;

    protected function get(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return parent::get($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    protected function head(string $path, array $parameters = [], array $requestHeaders = []): ResponseInterface
    {
        return parent::head($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    protected function post(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return parent::post($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    protected function postRaw(string $path, $body, array $requestHeaders = [])
    {
        return parent::postRaw($path, $body, $this->mergeHeaders($requestHeaders));
    }

    protected function patch(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return parent::patch($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    protected function put(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return parent::put($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    protected function delete(string $path, array $parameters = [], array $requestHeaders = [])
    {
        return parent::delete($path, $parameters, $this->mergeHeaders($requestHeaders));
    }

    /**
     * Append a new accept header on all requests.
     *
     * @return array
     */
    private function mergeHeaders(array $headers = []): array
    {
        $default = [];
        if ($this->acceptHeaderValue) {
            $default = ['Accept' => $this->acceptHeaderValue];
        }

        return array_merge($default, $headers);
    }
}
