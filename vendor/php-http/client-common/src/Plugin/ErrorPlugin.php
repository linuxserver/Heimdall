<?php

namespace Http\Client\Common\Plugin;

use Http\Client\Common\Exception\ClientErrorException;
use Http\Client\Common\Exception\ServerErrorException;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Throw exception when the response of a request is not acceptable.
 *
 * Status codes 400-499 lead to a ClientErrorException, status 500-599 to a ServerErrorException.
 *
 * @author Joel Wurtz <joel.wurtz@gmail.com>
 */
final class ErrorPlugin implements Plugin
{
    /**
     * @var bool Whether this plugin should only throw 5XX Exceptions (default to false).
     *
     * If set to true 4XX Responses code will never throw an exception
     */
    private $onlyServerException;

    /**
     * @param array $config {
     *
     *    @var bool only_server_exception Whether this plugin should only throw 5XX Exceptions (default to false).
     * }
     */
    public function __construct(array $config = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'only_server_exception' => false,
        ]);
        $resolver->setAllowedTypes('only_server_exception', 'bool');
        $options = $resolver->resolve($config);

        $this->onlyServerException = $options['only_server_exception'];
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $promise = $next($request);

        return $promise->then(function (ResponseInterface $response) use ($request) {
            return $this->transformResponseToException($request, $response);
        });
    }

    /**
     * Transform response to an error if possible.
     *
     * @param RequestInterface  $request  Request of the call
     * @param ResponseInterface $response Response of the call
     *
     * @throws ClientErrorException If response status code is a 4xx
     * @throws ServerErrorException If response status code is a 5xx
     *
     * @return ResponseInterface If status code is not in 4xx or 5xx return response
     */
    protected function transformResponseToException(RequestInterface $request, ResponseInterface $response)
    {
        if (!$this->onlyServerException && $response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            throw new ClientErrorException($response->getReasonPhrase(), $request, $response);
        }

        if ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            throw new ServerErrorException($response->getReasonPhrase(), $request, $response);
        }

        return $response;
    }
}
