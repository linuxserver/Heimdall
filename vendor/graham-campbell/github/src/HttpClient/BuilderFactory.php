<?php

declare(strict_types=1);

/*
 * This file is part of Laravel GitHub.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\GitHub\HttpClient;

use Github\HttpClient\Builder;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * This is the http client builder factory class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class BuilderFactory
{
    /**
     * The http client instance.
     *
     * @var \Psr\Http\Client\ClientInterface
     */
    private ClientInterface $httpClient;

    /**
     * The request factory instance.
     *
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    private RequestFactoryInterface $requestFactory;

    /**
     * The stream factory instance.
     *
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    private StreamFactoryInterface $streamFactory;

    /**
     * Create a new connection factory instance.
     *
     * @param \Psr\Http\Client\ClientInterface          $httpClient
     * @param \Psr\Http\Message\RequestFactoryInterface $requestFactory
     * @param \Psr\Http\Message\StreamFactoryInterface  $streamFactory
     *
     * @return void
     */
    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Return a new http client builder.
     *
     * @return \Github\HttpClient\Builder
     */
    public function make(): Builder
    {
        return new Builder($this->httpClient, $this->requestFactory, $this->streamFactory);
    }
}
