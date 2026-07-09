<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware;

use Psr\Log\{
    LoggerInterface,
    LoggerAwareInterface,
};
use Stringable;
use WebSocket\{
    Configuration,
    Connection,
};
use WebSocket\Message\{
    Close,
    Message
};
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\CloseHandler class.
 * Handles close procedure.
 */
class CloseHandler implements LoggerAwareInterface, ProcessIncomingInterface, ProcessOutgoingInterface, Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    private const SCOPE = 'close-handler';

    public function __construct()
    {
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

    public function processIncoming(ProcessStack $stack, Connection $connection): Message
    {
        $message = $stack->handleIncoming(); // Proceed before logic
        if (!$message instanceof Close) {
            return $message;
        }
        if ($connection->isWritable()) {
            // Remote sent Close; acknowledge and close for further reading
            $this->configuration->getLogger()->debug("[{scope}] Received 'close', status: {status}", [
                'scope' => self::SCOPE,
                'connection' => $connection->getIdentity(),
                'status' => $message->getCloseStatus(),
            ]);
            $ack =  "Close acknowledged: {$message->getCloseStatus()}";
            $connection->closeRead();
            $connection->send(new Close($message->getCloseStatus(), $ack));
        } else {
            // Remote sent Close/Ack: disconnect
            $this->configuration->getLogger()->debug("[{scope}] Received 'close' acknowledge, disconnecting", [
                'scope' => self::SCOPE,
                'connection' => $connection->getIdentity(),
                'status' => $message->getCloseStatus(),
            ]);
            $connection->disconnect();
        }
        return $message;
    }

    public function processOutgoing(ProcessStack $stack, Connection $connection, Message $message): Message
    {
        $message = $stack->handleOutgoing($message); // Proceed before logic
        if (!$message instanceof Close) {
            return $message;
        }
        if ($connection->isReadable()) {
            // Local sent Close: close for further writing, expect remote acknowledge
            $this->configuration->getLogger()->debug("[{scope}] Sent 'close', status: {status}", [
                'scope' => self::SCOPE,
                'connection' => $connection->getIdentity(),
                'status' => $message->getCloseStatus(),
            ]);
            $connection->closeWrite();
        } else {
            // Local sent Close/Ack: disconnect
            $this->configuration->getLogger()->debug("[{scope}] Sent 'close' acknowledge, disconnecting", [
                'scope' => self::SCOPE,
                'connection' => $connection->getIdentity(),
                'status' => $message->getCloseStatus(),
            ]);
            $connection->disconnect();
        }
        return $message;
    }
}
