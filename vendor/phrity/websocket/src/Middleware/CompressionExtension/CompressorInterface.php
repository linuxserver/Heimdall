<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Middleware\CompressionExtension;

use Stringable;
use WebSocket\Message\{
    Binary,
    Text,
};

/**
 * WebSocket\Middleware\CompressionExtension\CompressorInterface interface.
 * Interface for Per-Message Compression algotitms.
 */
interface CompressorInterface extends Stringable
{
    // Return header value for Client request
    public function getRequestHeaderValue(): string;

    // Return header value for Server response
    public function getResponseHeaderValue(object $configuration): string;

    // If compressor is eligable for provided configuration
    public function isEligable(object $configuration): bool;

    // Get runtime configuration for provided header element
    public function getConfiguration(string $element, bool $isServer): object;

    // Compress message
    public function compress(Binary|Text $message, object $configuration): Binary|Text;

    // Decompress message
    public function decompress(Binary|Text $message, object $configuration): Binary|Text;
}
