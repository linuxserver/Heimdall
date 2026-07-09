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
use WebSocket\Message\{
    Binary,
    Message,
    Text,
};
use WebSocket\Middleware\CompressionExtension\CompressorInterface;
use WebSocket\Trait\{
    ConfigurationTrait,
    StringableTrait,
};

/**
 * WebSocket\Middleware\CompressionExtension class.
 * Wrapper for Per-Message Compression Extension (PMCE).
 * @see https://datatracker.ietf.org/doc/html/rfc7692
 */
class CompressionExtension implements
    LoggerAwareInterface,
    ProcessHttpOutgoingInterface,
    ProcessHttpIncomingInterface,
    ProcessIncomingInterface,
    ProcessOutgoingInterface,
    Stringable
{
    use ConfigurationTrait;
    use StringableTrait;

    private const SCOPE = 'permessage-compression';

    /** @var array<CompressorInterface> $compressors */
    private array $compressors = [];

    public function __construct(CompressorInterface ...$compressors)
    {
        $this->compressors = $compressors;
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
            $connection->setMeta('compressionExtension.compressor', null);
            $connection->setMeta('compressionExtension.configuration', null);
            $headerValues = [];
            foreach ($this->compressors as $compressor) {
                $headerValues[] = $compressor->getRequestHeaderValue();
            }
            $message = $message->withAddedHeader('Sec-WebSocket-Extensions', implode(', ', $headerValues));
        } elseif ($message instanceof ResponseInterface) {
            // Outgoing Response on Server
            if ($compressor = $connection->getMeta('compressionExtension.compressor')) {
                $configuration = $connection->getMeta('compressionExtension.configuration');
                $message = $message->withHeader(
                    'Sec-WebSocket-Extensions',
                    $compressor->getResponseHeaderValue($configuration)
                );
            }
        }
        return $stack->handleHttpOutgoing($message);
    }

    public function processHttpIncoming(ProcessHttpStack $stack, Connection $connection): MessageInterface
    {
        $message = $stack->handleHttpIncoming();
        if ($message instanceof ServerRequestInterface) {
            // Incoming requests on Server
            $connection->setMeta('compressionExtension.compressor', null);
            $connection->setMeta('compressionExtension.configuration', null);
            if ($preferred = $this->getPreferred($message)) {
                $connection->setMeta('compressionExtension.compressor', $preferred->compressor);
                $connection->setMeta('compressionExtension.configuration', $preferred->configuration);
                $this->configuration->getLogger()->debug("[{scope}] Using: {compressor}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'compressor' => $preferred->compressor,
                    'configuration' => (array)$preferred->configuration,
                ]);
            }
        } elseif ($message instanceof ResponseInterface) {
            // Incoming Response on Client
            if ($preferred = $this->getPreferred($message)) {
                $connection->setMeta('compressionExtension.compressor', $preferred->compressor);
                $connection->setMeta('compressionExtension.configuration', $preferred->configuration);
                $this->configuration->getLogger()->debug("[{scope}] Using: {compressor}", [
                    'scope' => self::SCOPE,
                    'connection' => $connection->getIdentity(),
                    'compressor' => $preferred->compressor,
                    'configuration' => (array)$preferred->configuration,
                ]);
            }
            // @todo: If not found?
        }
        return $message;
    }

    public function processIncoming(ProcessStack $stack, Connection $connection): Message
    {
        $message = $stack->handleIncoming();
        if (
            ($message instanceof Text || $message instanceof Binary)
            && $message->isCompressed()
            && $compressor = $connection->getMeta('compressionExtension.compressor')
        ) {
            $message = $compressor->decompress($message, $connection->getMeta('compressionExtension.configuration'));
        }
        return $message;
    }

    /**
     * @template T of Message
     * @param T $message
     * @return T|Text|Binary
     */
    public function processOutgoing(ProcessStack $stack, Connection $connection, Message $message): Message
    {
        if (
            ($message instanceof Text || $message instanceof Binary)
            && !$message->isCompressed()
            && $compressor = $connection->getMeta('compressionExtension.compressor')
        ) {
            /** @var Text|Binary $message */
            $message = $compressor->compress($message, $connection->getMeta('compressionExtension.configuration'));
        }
        return $stack->handleOutgoing($message);
    }

    /**
     * @return object{compressor: CompressorInterface, configuration: object}|null
     */
    protected function getPreferred(MessageInterface $request): object|null
    {
        $isServer = $request instanceof ServerRequestInterface;
        foreach ($request->getHeader('Sec-WebSocket-Extensions') as $header) {
            foreach (explode(',', $header) as $element) {
                foreach ($this->compressors as $compressor) {
                    $configuration = $compressor->getConfiguration(trim($element), $isServer);
                    if ($compressor->isEligable($configuration)) {
                        return (object)['compressor' => $compressor, 'configuration' => $configuration];
                    }
                }
            }
        }
        return null;
    }
}
