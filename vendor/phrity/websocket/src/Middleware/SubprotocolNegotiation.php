<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 *
 * This file is part of Websocket PHP and is free software under the ISC License.
 * License text: https://raw.githubusercontent.com/sirn-se/websocket-php/master/COPYING.md
 */

namespace WebSocket\Middleware;

use Psr\Http\Message\{
    MessageInterface,
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
};
use Psr\Log\{
    LoggerInterface,
    LoggerAwareInterface,
};
use Stringable;
use WebSocket\{
    Configuration,
    Connection,
};
use WebSocket\Exception\HandshakeException;
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\CloseHandler class.
 * Handles close procedure.
 */
class SubprotocolNegotiation implements
    LoggerAwareInterface,
    ProcessHttpOutgoingInterface,
    ProcessHttpIncomingInterface,
    Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    private const SCOPE = 'subprotocol-negotiation';

    /** @var array<string> $subprotocols */
    private array $subprotocols;
    private bool $require;

    /** @param array<string> $subprotocols */
    public function __construct(array $subprotocols, bool $require = false)
    {
        $this->subprotocols = $subprotocols;
        $this->require = $require;
        $this->initConfiguration();
    }

    /**
     * Set logger.
     * @param LoggerInterface $logger
     * @deprecated Will be removed in future version, retrieved from Configuration instead
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->configuration->setLogger($logger);
    }

    public function processHttpOutgoing(
        ProcessHttpStack $stack,
        Connection $connection,
        MessageInterface $message
    ): MessageInterface {
        if ($message instanceof RequestInterface) {
            // Outgoing requests on Client
            foreach ($this->subprotocols as $subprotocol) {
                $message = $message->withAddedHeader('Sec-WebSocket-Protocol', $subprotocol);
            }
            if ($requested = implode(', ', $this->subprotocols)) {
                $this->configuration->getLogger()->debug("[{scope}] Requested subprotocols: {requested}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'requested' => $requested,
                ]);
            }
        } elseif ($message instanceof ResponseInterface) {
            // Outgoing Response on Server
            if ($selected = $connection->getMeta('subprotocolNegotiation.selected')) {
                $message = $message->withHeader('Sec-WebSocket-Protocol', $selected);
                $this->configuration->getLogger()->info("[{scope}] Selected subprotocol: {selected}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'selected' => $selected,
                ]);
            } elseif ($this->require) {
                // No matching subprotocol, fail handshake
                $message = $message->withStatus(426);
            }
        }
        return $stack->handleHttpOutgoing($message);
    }

    /**
     * @throws HandshakeException
     */
    public function processHttpIncoming(ProcessHttpStack $stack, Connection $connection): MessageInterface
    {
        $connection->setMeta('subprotocolNegotiation.selected', null);
        $message = $stack->handleHttpIncoming();

        if ($message instanceof ServerRequestInterface) {
            // Incoming requests on Server
            if ($requested = $message->getHeaderLine('Sec-WebSocket-Protocol')) {
                $this->configuration->getLogger()->debug("[{scope}] Requested subprotocols: {requested}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'requested' => $requested,
                ]);
            }
            if ($supported = implode(', ', $this->subprotocols)) {
                $this->configuration->getLogger()->debug("[{scope}] Supported subprotocols: {supported}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'supported' => $supported,
                ]);
            }
            foreach ($message->getHeader('Sec-WebSocket-Protocol') as $subprotocol) {
                if (in_array($subprotocol, $this->subprotocols)) {
                    $connection->setMeta('subprotocolNegotiation.selected', $subprotocol);
                    return $message;
                }
            }
        } elseif ($message instanceof ResponseInterface) {
            // Incoming Response on Client
            if ($selected = $message->getHeaderLine('Sec-WebSocket-Protocol')) {
                $connection->setMeta('subprotocolNegotiation.selected', $selected);
                $this->configuration->getLogger()->info("[{scope}] Selected subprotocol: {selected}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'selected' => $selected,
                ]);
            } elseif ($this->require) {
                // No matching subprotocol, close and fail
                $connection->close();
                throw new HandshakeException("Could not resolve subprotocol.", $message);
            }
        }
        return $message;
    }
}
