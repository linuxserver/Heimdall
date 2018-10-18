<?php

namespace Http\Discovery;

use Http\Discovery\Exception\DiscoveryFailedException;
use Http\Message\MessageFactory;

/**
 * Finds a Message Factory.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
final class MessageFactoryDiscovery extends ClassDiscovery
{
    /**
     * Finds a Message Factory.
     *
     * @return MessageFactory
     *
     * @throws Exception\NotFoundException
     */
    public static function find()
    {
        try {
            $messageFactory = static::findOneByType(MessageFactory::class);
        } catch (DiscoveryFailedException $e) {
            throw new NotFoundException(
                'No message factories found. To use Guzzle, Diactoros or Slim Framework factories install php-http/message and the chosen message implementation.',
                0,
                $e
            );
        }

        return static::instantiateClass($messageFactory);
    }
}
