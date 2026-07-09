<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Runtime;

use Closure;
use Phrity\Net\{
    Context,
    SocketServer,
    SocketStream,
    StreamCollection,
    StreamContainerInterface,
    StreamException,
    StreamFactory,
    StreamInterface,
    Uri
};
use WebSocket\{
    Server,
};
use WebSocket\Exception\{
    ExceptionInterface,
    RunnerException,
};

/**
 * WebSocket\Runtime\Runner class.
 * Stream select runner.
 * @phpstan-type Container object{
 *   container: StreamContainerInterface,
 *   stream: StreamInterface,
 *   onSelect: Closure
 * }
 */
class Runner
{
    private StreamFactory $streamFactory;
    private StreamCollection $streamCollection;
    /** @var array<string, Container> $containers */
    private array $containers = [];

    public function __construct(StreamFactory $streamFactory)
    {
        $this->streamFactory = $streamFactory;
        $this->streamCollection = $this->streamFactory->createStreamCollection();
    }

    public function attach(StreamContainerInterface $streamContainer, Closure $onSelect, string $identity): void
    {
        if (array_key_exists($identity, $this->containers)) {
            // On repeated identity, check if actually readable (detach if not)
            if ($this->containers[$identity]->stream->isReadable()) {
                throw new RunnerException("Stream container with identity {$identity} already attached");
            }
            $this->detach($identity);
        }
        $stream = $streamContainer->getStream();
        $this->streamCollection->attach($stream, $identity);
        $this->containers[$identity] = (object)[
            'container' => $streamContainer,
            'stream' => $stream,
            'onSelect' => $onSelect,
        ];
    }

    public function detach(string $identity): void
    {
        if (array_key_exists($identity, $this->containers)) {
            $this->streamCollection->detach($identity);
            unset($this->containers[$identity]);
        }
    }

    /**
     * @throws ExceptionInterface
     */
    public function handle(int|float $timeout): void
    {
        foreach ($this->select($timeout) as $identity => $stream) {
            $container = $this->containers[$identity];
            /** @throws ExceptionInterface */
            call_user_func($container->onSelect, $this, $container->container);
        }
    }

    public function select(int|float $timeout): StreamCollection
    {
        return $this->streamCollection->waitRead($timeout);
    }
}
