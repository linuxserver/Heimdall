<?php

namespace Http\Discovery\Strategy;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonPsr17ClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        RequestFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\RequestFactory',
            'Http\Factory\Diactoros\RequestFactory',
            'Http\Factory\Guzzle\RequestFactory',
            'Http\Factory\Slim\RequestFactory',
        ],
        ResponseFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\ResponseFactory',
            'Http\Factory\Diactoros\ResponseFactory',
            'Http\Factory\Guzzle\ResponseFactory',
            'Http\Factory\Slim\ResponseFactory',
        ],
        ServerRequestFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\ServerRequestFactory',
            'Http\Factory\Diactoros\ServerRequestFactory',
            'Http\Factory\Guzzle\ServerRequestFactory',
            'Http\Factory\Slim\ServerRequestFactory',
        ],
        StreamFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\StreamFactory',
            'Http\Factory\Diactoros\StreamFactory',
            'Http\Factory\Guzzle\StreamFactory',
            'Http\Factory\Slim\StreamFactory',
        ],
        UploadedFileFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\UploadedFileFactory',
            'Http\Factory\Diactoros\UploadedFileFactory',
            'Http\Factory\Guzzle\UploadedFileFactory',
            'Http\Factory\Slim\UploadedFileFactory',
        ],
        UriFactoryInterface::class => [
            'Nyholm\Psr7\Factory\Psr17Factory',
            'Zend\Diactoros\UriFactory',
            'Http\Factory\Diactoros\UriFactory',
            'Http\Factory\Guzzle\UriFactory',
            'Http\Factory\Slim\UriFactory',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        $candidates = [];
        if (isset(self::$classes[$type])) {
            foreach (self::$classes[$type] as $class) {
                $candidates[] = ['class' => $class, 'condition' => [$class]];
            }
        }

        return $candidates;
    }
}
