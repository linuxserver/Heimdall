<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 *
 * This file is part of Websocket PHP and is free software under the ISC License.
 * License text: https://raw.githubusercontent.com/sirn-se/websocket-php/master/COPYING.md
 */

namespace WebSocket\Middleware\CompressionExtension;

use DeflateContext;
use InflateContext;
use RangeException;
use RuntimeException;
use stdClass;
use Stringable;
use WebSocket\Message\{
    Binary,
    Message,
    Text,
};
use WebSocket\Trait\StringableTrait;

/**
 * WebSocket\Middleware\PerMessageCompression\DeflateCompressor class.
 * Handles compression using permessage-deflate.
 * @see https://datatracker.ietf.org/doc/html/rfc7692
 * @phpstan-type Config stdClass&object{
 *   compressor: mixed,
 *   isServer: bool,
 *   deflator: DeflateContext|null,
 *   inflator: InflateContext|null,
 *   isServer: bool,
 *   serverNoContextTakeover: bool,
 *   clientNoContextTakeover: bool,
 *   serverMaxWindowBits: int<self::MIN_WINDOW_SIZE, self::MAX_WINDOW_SIZE>,
 *   clientMaxWindowBits: int<self::MIN_WINDOW_SIZE, self::MAX_WINDOW_SIZE>,
 * }
 */
class DeflateCompressor implements CompressorInterface, Stringable
{
    use StringableTrait;

    private const MIN_WINDOW_SIZE = 9;
    private const MAX_WINDOW_SIZE = 15;

    private bool $serverNoContextTakeover;
    private bool $clientNoContextTakeover;
    /** @var int<self::MIN_WINDOW_SIZE, self::MAX_WINDOW_SIZE> $serverMaxWindowBits */
    private int $serverMaxWindowBits;
    /** @var int<self::MIN_WINDOW_SIZE, self::MAX_WINDOW_SIZE> $clientMaxWindowBits */
    private int $clientMaxWindowBits;

    /**
     * @throws RuntimeException
     * @throws RangeException
     */
    public function __construct(
        bool $serverNoContextTakeover = false,
        bool $clientNoContextTakeover = false,
        int $serverMaxWindowBits = self::MAX_WINDOW_SIZE,
        int $clientMaxWindowBits = self::MAX_WINDOW_SIZE,
        string $extension = 'zlib',
    ) {
        if (!extension_loaded($extension)) {
            throw new RuntimeException("DeflateCompressor require {$extension} extension.");
        }
        if ($serverMaxWindowBits < self::MIN_WINDOW_SIZE || $serverMaxWindowBits > self::MAX_WINDOW_SIZE) {
            throw new RangeException("DeflateCompressor serverMaxWindowBits must be in range 9-15.");
        }
        if ($clientMaxWindowBits < self::MIN_WINDOW_SIZE || $clientMaxWindowBits > self::MAX_WINDOW_SIZE) {
            throw new RangeException("DeflateCompressor clientMaxWindowBits must be in range 9-15.");
        }
        $this->serverNoContextTakeover = $serverNoContextTakeover;
        $this->clientNoContextTakeover = $clientNoContextTakeover;
        $this->serverMaxWindowBits = $serverMaxWindowBits;
        $this->clientMaxWindowBits = $clientMaxWindowBits;
    }

    public function getRequestHeaderValue(): string
    {
        $header = "permessage-deflate";
        if ($this->serverNoContextTakeover) {
            $header .= "; server_no_context_takeover";
        }
        if ($this->clientNoContextTakeover) {
            $header .= "; client_no_context_takeover";
        }
        if ($this->serverMaxWindowBits != self::MAX_WINDOW_SIZE) {
            $header .= "; server_max_window_bits={$this->serverMaxWindowBits}";
        }
        if ($this->clientMaxWindowBits != self::MAX_WINDOW_SIZE) {
            $header .= "; client_max_window_bits={$this->clientMaxWindowBits}";
        }
        return $header;
    }

    /**
     * @param Config $configuration
     */
    public function getResponseHeaderValue(object $configuration): string
    {
        // @todo: throw HandshakeException or bad config
        $header = "permessage-deflate";
        if ($configuration->serverNoContextTakeover) {
            $header .= "; server_no_context_takeover";
        }
        if ($configuration->clientNoContextTakeover) {
            $header .= "; client_no_context_takeover";
        }
        $serverMaxWindowBits = min($configuration->serverMaxWindowBits, $this->serverMaxWindowBits);
        if ($serverMaxWindowBits != self::MAX_WINDOW_SIZE) {
            $header .= "; server_max_window_bits={$serverMaxWindowBits}";
        }
        $clientMaxWindowBits = min($configuration->clientMaxWindowBits, $this->clientMaxWindowBits);
        if ($clientMaxWindowBits != self::MAX_WINDOW_SIZE) {
            $header .= "; client_max_window_bits={$clientMaxWindowBits}";
        }
        return $header;
    }

    /**
     * @param Config $configuration
     */
    public function isEligable(object $configuration): bool
    {
        return
            $configuration->compressor == 'permessage-deflate'
            && $configuration->serverMaxWindowBits <= $this->serverMaxWindowBits
            && $configuration->clientMaxWindowBits <= $this->clientMaxWindowBits
            ;
    }

    /**
     * @return Config
     */
    public function getConfiguration(string $element, bool $isServer): object
    {
        $configuration = (object)[
            'compressor' => null,
            'isServer' => $isServer,
            'serverNoContextTakeover' => $this->serverNoContextTakeover,
            'clientNoContextTakeover' => $this->clientNoContextTakeover,
            'serverMaxWindowBits' => $this->serverMaxWindowBits,
            'clientMaxWindowBits' => $this->clientMaxWindowBits,
            'deflator' => null,
            'inflator' => null,
        ];
        foreach (explode(';', $element) as $parameter) {
            $parts = explode('=', $parameter);
            $key = trim(array_shift($parts));
            $value = array_shift($parts);
            // @todo: Error handling when parsing
            switch ($key) {
                case 'permessage-deflate':
                    $configuration->compressor = $key;
                    break;
                case 'server_no_context_takeover':
                    $configuration->serverNoContextTakeover = true;
                    break;
                case 'client_no_context_takeover':
                    $configuration->clientNoContextTakeover = true;
                    break;
                case 'server_max_window_bits':
                    $bits = $this->intVal($value);
                    $configuration->serverMaxWindowBits = min($bits, $this->serverMaxWindowBits);
                    break;
                case 'client_max_window_bits':
                    $bits = $this->intVal($value);
                    $configuration->clientMaxWindowBits = min($bits, $this->clientMaxWindowBits);
                    break;
            }
        }
        return $configuration;
    }

    /**
     * @template T of Binary|Text
     * @param T $message
     * @param Config $configuration
     * @return T
     */
    public function compress(Binary|Text $message, object $configuration): Binary|Text
    {
        $windowBits = $configuration->isServer
            ? $configuration->serverMaxWindowBits
            : $configuration->clientMaxWindowBits;
        $noContextTakeover = $configuration->isServer
            ? $configuration->serverNoContextTakeover
            : $configuration->clientNoContextTakeover;

        if (is_null($configuration->deflator) || $noContextTakeover) {
            $configuration->deflator = deflate_init(ZLIB_ENCODING_RAW, [
                'level' => -1,
                'window' => $windowBits,
                'strategy' => ZLIB_DEFAULT_STRATEGY
            ]) ?: null;
        }
        /** @var DeflateContext $deflator */
        $deflator = $configuration->deflator;
        /** @var string $deflated */
        $deflated = deflate_add($deflator, $message->getPayload(), ZLIB_SYNC_FLUSH);
        $deflated = substr($deflated, 0, -4); // Remove 4 last chars
        $message->setCompress(true);
        $message->setPayload($deflated);
        return $message;
    }

    /**
     * @param Config $configuration
     */
    public function decompress(Binary|Text $message, object $configuration): Binary|Text
    {
        $windowBits = $configuration->isServer
            ? $configuration->clientMaxWindowBits
            : $configuration->serverMaxWindowBits;
        $noContextTakeover = $configuration->isServer
            ? $configuration->clientNoContextTakeover
            : $configuration->serverNoContextTakeover;

        if (is_null($configuration->inflator) || $noContextTakeover) {
            $configuration->inflator = inflate_init(ZLIB_ENCODING_RAW, [
                'level' => -1,
                'window' => $windowBits,
                'strategy' => ZLIB_DEFAULT_STRATEGY
            ]) ?: null;
        }
        /** @var InflateContext $inflator */
        $inflator = $configuration->inflator;
        /** @var string $inflated */
        $inflated = inflate_add($inflator, $message->getPayload() . "\x00\x00\xff\xff");
        $message->setCompress(false);
        $message->setPayload($inflated);
        return $message;
    }

    private function intVal(string|null $input): int
    {
        if (is_null($input)) {
            return self::MAX_WINDOW_SIZE;
        }
        preg_match('/([1-9][0-9]*)/', $input, $matches);
        if (empty($matches)) {
            return self::MAX_WINDOW_SIZE;
        }
        $value = (int)array_shift($matches);
        return min(max($value, self::MIN_WINDOW_SIZE), self::MAX_WINDOW_SIZE);
    }
}
