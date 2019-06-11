<?php

namespace Http\Discovery\Strategy;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Http\Client\HttpAsyncClient;
use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Http\Message\StreamFactory;
use Http\Message\StreamFactory\GuzzleStreamFactory;
use Http\Message\UriFactory;
use Http\Message\UriFactory\GuzzleUriFactory;
use Http\Message\MessageFactory\DiactorosMessageFactory;
use Http\Message\StreamFactory\DiactorosStreamFactory;
use Http\Message\UriFactory\DiactorosUriFactory;
use Psr\Http\Client\ClientInterface as Psr18Client;
use Zend\Diactoros\Request as DiactorosRequest;
use Http\Message\MessageFactory\SlimMessageFactory;
use Http\Message\StreamFactory\SlimStreamFactory;
use Http\Message\UriFactory\SlimUriFactory;
use Slim\Http\Request as SlimRequest;
use Http\Adapter\Guzzle6\Client as Guzzle6;
use Http\Adapter\Guzzle5\Client as Guzzle5;
use Http\Client\Curl\Client as Curl;
use Http\Client\Socket\Client as Socket;
use Http\Adapter\React\Client as React;
use Http\Adapter\Buzz\Client as Buzz;
use Http\Adapter\Cake\Client as Cake;
use Http\Adapter\Zend\Client as Zend;
use Http\Adapter\Artax\Client as Artax;
use Nyholm\Psr7\Factory\HttplugFactory as NyholmHttplugFactory;

/**
 * @internal
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class CommonClassesStrategy implements DiscoveryStrategy
{
    /**
     * @var array
     */
    private static $classes = [
        MessageFactory::class => [
            ['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]],
            ['class' => GuzzleMessageFactory::class, 'condition' => [GuzzleRequest::class, GuzzleMessageFactory::class]],
            ['class' => DiactorosMessageFactory::class, 'condition' => [DiactorosRequest::class, DiactorosMessageFactory::class]],
            ['class' => SlimMessageFactory::class, 'condition' => [SlimRequest::class, SlimMessageFactory::class]],
        ],
        StreamFactory::class => [
            ['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]],
            ['class' => GuzzleStreamFactory::class, 'condition' => [GuzzleRequest::class, GuzzleStreamFactory::class]],
            ['class' => DiactorosStreamFactory::class, 'condition' => [DiactorosRequest::class, DiactorosStreamFactory::class]],
            ['class' => SlimStreamFactory::class, 'condition' => [SlimRequest::class, SlimStreamFactory::class]],
        ],
        UriFactory::class => [
            ['class' => NyholmHttplugFactory::class, 'condition' => [NyholmHttplugFactory::class]],
            ['class' => GuzzleUriFactory::class, 'condition' => [GuzzleRequest::class, GuzzleUriFactory::class]],
            ['class' => DiactorosUriFactory::class, 'condition' => [DiactorosRequest::class, DiactorosUriFactory::class]],
            ['class' => SlimUriFactory::class, 'condition' => [SlimRequest::class, SlimUriFactory::class]],
        ],
        HttpAsyncClient::class => [
            ['class' => Guzzle6::class, 'condition' => Guzzle6::class],
            ['class' => Curl::class, 'condition' => Curl::class],
            ['class' => React::class, 'condition' => React::class],
        ],
        HttpClient::class => [
            ['class' => Guzzle6::class, 'condition' => Guzzle6::class],
            ['class' => Guzzle5::class, 'condition' => Guzzle5::class],
            ['class' => Curl::class, 'condition' => Curl::class],
            ['class' => Socket::class, 'condition' => Socket::class],
            ['class' => Buzz::class, 'condition' => Buzz::class],
            ['class' => React::class, 'condition' => React::class],
            ['class' => Cake::class, 'condition' => Cake::class],
            ['class' => Zend::class, 'condition' => Zend::class],
            ['class' => Artax::class, 'condition' => Artax::class],
            [
                'class' => [self::class, 'buzzInstantiate'],
                'condition' => [\Buzz\Client\FileGetContents::class, \Buzz\Message\ResponseBuilder::class],
            ],
        ],
        Psr18Client::class => [
            [
                'class' => [self::class, 'buzzInstantiate'],
                'condition' => [\Buzz\Client\FileGetContents::class, \Buzz\Message\ResponseBuilder::class],
            ],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public static function getCandidates($type)
    {
        if (Psr18Client::class === $type) {
            $candidates = self::$classes[PSR18Client::class];

            // HTTPlug 2.0 clients implements PSR18Client too.
            foreach (self::$classes[HttpClient::class] as $c) {
                if (is_subclass_of($c['class'], Psr18Client::class)) {
                    $candidates[] = $c;
                }
            }

            return $candidates;
        }

        if (isset(self::$classes[$type])) {
            return self::$classes[$type];
        }

        return [];
    }

    public static function buzzInstantiate()
    {
        return new \Buzz\Client\FileGetContents(MessageFactoryDiscovery::find());
    }
}
