<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use Closure;
use Psr\Http\Message\MessageInterface;
use Psr\Log\{
    LoggerInterface,
    LoggerAwareInterface,
};
use Stringable;
use WebSocket\{
    Configuration,
    Connection,
};
use WebSocket\Http\HttpHandler;
use WebSocket\Message\{
    Message,
    MessageHandler
};
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\MiddlewareHandler class.
 * Middleware handling.
 */
class MiddlewareHandler implements LoggerAwareInterface, Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    private const SCOPE = 'middleware-handler';

    // Processor collections
    /** @var array<MiddlewareInterface> */
    private array $middlewares = [];
    /** @var array<ProcessIncomingInterface> */
    private array $incoming = [];
    /** @var array<ProcessOutgoingInterface> */
    private array $outgoing = [];
    /** @var array<ProcessHttpIncomingInterface> */
    private array $httpIncoming = [];
    /** @var array<ProcessHttpOutgoingInterface> */
    private array $httpOutgoing = [];
    /** @var array<ProcessTickInterface> */
    private array $tick = [];

    // Handlers
    private HttpHandler $httpHandler;
    private MessageHandler $messageHandler;

    /**
     * Create MiddlewareHandler.
     * @param MessageHandler $messageHandler
     * @param HttpHandler $httpHandler
     */
    public function __construct(
        MessageHandler $messageHandler,
        HttpHandler $httpHandler,
        Configuration|null $configuration = null,
    ) {
        $this->messageHandler = $messageHandler;
        $this->httpHandler = $httpHandler;
        $this->initConfiguration($configuration);
    }

    /**
     * Set logger on MiddlewareHandler and all LoggerAware middlewares.
     * @param LoggerInterface $logger
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->configuration->setLogger($logger);
        foreach ($this->middlewares as $middleware) {
            if ($middleware instanceof LoggerAwareInterface) {
                $middleware->setLogger($logger);
            }
        }
    }

    /**
     * Add a middleware.
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $context = [
            'scope' => self::SCOPE,
            'middleware' => $middleware,
        ];
        if ($middleware instanceof ProcessIncomingInterface) {
            $this->configuration->getLogger()->info("[{scope}] Added incoming: {middleware}", $context);
            $this->incoming[] = $middleware;
        }
        if ($middleware instanceof ProcessOutgoingInterface) {
            $this->configuration->getLogger()->info("[{scope}] Added outgoing: {middleware}", $context);
            $this->outgoing[] = $middleware;
        }
        if ($middleware instanceof ProcessHttpIncomingInterface) {
            $this->configuration->getLogger()->info("[{scope}] Added http incoming: {middleware}", $context);
            $this->httpIncoming[] = $middleware;
        }
        if ($middleware instanceof ProcessHttpOutgoingInterface) {
            $this->configuration->getLogger()->info("[{scope}] Added http outgoing: {middleware}", $context);
            $this->httpOutgoing[] = $middleware;
        }
        if ($middleware instanceof ProcessTickInterface) {
            $this->configuration->getLogger()->info("[{scope}] Added tick: {middleware}", $context);
            $this->tick[] = $middleware;
        }
        if ($middleware instanceof LoggerAwareInterface) {
            $middleware->setLogger($this->configuration->getLogger());
        }
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Process middlewares for incoming messages.
     * @param Connection $connection
     * @return Message
     */
    public function processIncoming(Connection $connection): Message
    {
        $this->configuration->getLogger()->info("[{scope}] Processing incoming", [
            'scope' => self::SCOPE,
            'connection' => $connection->getIdentity(),
        ]);
        $stack = new ProcessStack($connection, $this->messageHandler, $this->incoming);
        return $stack->handleIncoming();
    }

    /**
     * Process middlewares for outgoing messages.
     * @template T of Message
     * @param Connection $connection
     * @param T $message
     * @return T
     */
    public function processOutgoing(Connection $connection, Message $message): Message
    {
        $this->configuration->getLogger()->info("[{scope}] Processing outgoing", [
            'scope' => self::SCOPE,
            'connection' => $connection->getIdentity(),
        ]);
        $stack = new ProcessStack($connection, $this->messageHandler, $this->outgoing);
        return $stack->handleOutgoing($message);
    }

    /**
     * Process middlewares for http requests.
     * @param Connection $connection
     * @return MessageInterface
     */
    public function processHttpIncoming(Connection $connection): MessageInterface
    {
        $this->configuration->getLogger()->info("[{scope}] Processing http incoming", [
            'scope' => self::SCOPE,
            'connection' => $connection->getIdentity(),
        ]);
        $stack = new ProcessHttpStack($connection, $this->httpHandler, $this->httpIncoming);
        return $stack->handleHttpIncoming();
    }

    /**
     * Process middlewares for http requests.
     * @param Connection $connection
     * @param MessageInterface $message
     * @return MessageInterface
     */
    public function processHttpOutgoing(Connection $connection, MessageInterface $message): MessageInterface
    {
        $this->configuration->getLogger()->info("[{scope}] Processing http outgoing", [
            'scope' => self::SCOPE,
            'connection' => $connection->getIdentity(),
        ]);
        $stack = new ProcessHttpStack($connection, $this->httpHandler, $this->httpOutgoing);
        return $stack->handleHttpOutgoing($message);
    }

    /**
     * Process middlewares for tick.
     * @param Connection $connection
     */
    public function processTick(Connection $connection): void
    {
        $this->configuration->getLogger()->info("[{scope}] Processing tick", [
            'scope' => self::SCOPE,
            'connection' => $connection->getIdentity(),
        ]);
        $stack = new ProcessTickStack($connection, $this->tick);
        $stack->handleTick();
    }
}
