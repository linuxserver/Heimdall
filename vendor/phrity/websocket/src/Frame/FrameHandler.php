<?php

/**
 * Copyright (C) 2014-2026 Textalk and contributors.
 * This file is part of Websocket PHP and is free software under the ISC License.
 */

namespace WebSocket\Frame;

use Phrity\Net\SocketStream;
use Psr\Log\{
    LoggerAwareInterface,
    LoggerInterface,
};
use RuntimeException;
use Stringable;
use WebSocket\Configuration;
use WebSocket\Exception\CloseException;
use WebSocket\Trait\{
    ConfigurationTrait,
    OpcodeTrait,
    StringableTrait
};

/**
 * WebSocket\Frame\FrameHandler class.
 * Reads and writes Frames on stream.
 */
class FrameHandler implements LoggerAwareInterface, Stringable
{
    use ConfigurationTrait;
    use OpcodeTrait;
    use StringableTrait;

    private const SCOPE = 'frame-handler';

    private SocketStream $stream;
    private bool $pushMasked;
    private bool $pullMaskedRequired;

    public function __construct(
        SocketStream $stream,
        bool $pushMasked,
        bool $pullMaskedRequired,
        Configuration|null $configuration = null,
    ) {
        $this->stream = $stream;
        $this->pushMasked = $pushMasked;
        $this->pullMaskedRequired = $pullMaskedRequired;
        $this->initConfiguration($configuration);
    }

    /**
     * Set logger.
     * @param LoggerInterface $logger Logger implementation
     * @deprecated Will be removed in future version, set on Configuration instead
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->configuration->setLogger($logger);
    }

    /**
     * Pull frame from stream
     * @throws CloseException
     */
    public function pull(): Frame
    {
        // Read the frame "header" first, two bytes.
        $data = $this->read(2);
        list ($byte1, $byte2) = array_values($this->unpack('C*', $data));
        $final = (bool)($byte1 & 0b10000000); // Final fragment marker.
        $rsv1 = (bool)($byte1 & 0b01000000);
        $rsv2 = (bool)($byte1 & 0b00100000);
        $rsv3 = (bool)($byte1 & 0b00010000);

        // Parse opcode
        $opcodeInt = $byte1 & 0b00001111;
        $opcodeInts = array_flip(self::$opcodes);
        $opcode = array_key_exists($opcodeInt, $opcodeInts) ? $opcodeInts[$opcodeInt] : strval($opcodeInt);

        // Masking bit
        $masked = (bool)($byte2 & 0b10000000);

        $payload = '';

        // Payload length
        $payloadLength = $byte2 & 0b01111111;

        if ($payloadLength > 125) {
            if ($payloadLength === 126) {
                $data = $this->read(2); // 126: Payload length is a 16-bit unsigned int
                $payloadLength = current($this->unpack('n', $data));
            } else {
                $data = $this->read(8); // 127: Payload length is a 64-bit unsigned int
                $payloadLength = current($this->unpack('J', $data));
            }
        }

        // Get masking key.
        if ($masked) {
            $maskingKey = $this->read(4);
        }

        // Get the actual payload, if any (might not be for e.g. close frames).
        if ($payloadLength > 0) {
            $data = $this->read($payloadLength);
            if ($masked) {
                // Unmask payload.
                for ($i = 0; $i < $payloadLength; $i++) {
                    $payload .= ($data[$i] ^ $maskingKey[$i % 4]);
                }
            } else {
                $payload = $data;
            }
        }

        $frame = new Frame($opcode, $payload, $final, $rsv1, $rsv2, $rsv3);
        $this->configuration->getLogger()->debug("[{scope}] Pulled '{opcode}' frame", [
            'scope' => self::SCOPE,
            'opcode' => $frame->getOpcode(),
            'final' => $frame->isFinal(),
            'content-length' => $frame->getPayloadLength(),
        ]);
        if ($this->pullMaskedRequired && !$masked) {
            $this->configuration->getLogger()->error("[{scope}] Masking required, but frame was unmasked", [
                'scope' => self::SCOPE,
                'opcode' => $frame->getOpcode(),
            ]);
            throw new CloseException(1002, 'Masking required');
        }

        return $frame;
    }

    // Push frame to stream
    public function push(Frame $frame): int
    {
        $payload = $frame->getPayload();
        $payloadLength = $frame->getPayloadLength();

        $data = '';
        $byte1 = $frame->isFinal() ? 0b10000000 : 0b00000000; // Final fragment marker.
        $byte1 |= $frame->getRsv1() ? 0b01000000 : 0b00000000; // RSV1 bit.
        $byte1 |= $frame->getRsv2() ? 0b00100000 : 0b00000000; // RSV2 bit.
        $byte1 |= $frame->getRsv3() ? 0b00010000 : 0b00000000; // RSV3 bit.
        $byte1 |= self::$opcodes[$frame->getOpcode()]; // Set opcode.
        $data .= pack('C', $byte1);

        $byte2 = $this->pushMasked ? 0b10000000 : 0b00000000; // Masking bit marker.

        // 7 bits of payload length
        if ($payloadLength > 65535) {
            $data .= pack('C', $byte2 | 0b01111111);
            $data .= pack('J', $payloadLength);
        } elseif ($payloadLength > 125) {
            $data .= pack('C', $byte2 | 0b01111110);
            $data .= pack('n', $payloadLength);
        } else {
            $data .= pack('C', $byte2 | $payloadLength);
        }

        // Handle masking.
        if ($this->pushMasked) {
            // Generate a random mask.
            $mask = '';
            for ($i = 0; $i < 4; $i++) {
                $mask .= chr(rand(0, 255));
            }
            $data .= $mask;

            // Append masked payload to frame.
            for ($i = 0; $i < $payloadLength; $i++) {
                $data .= $payload[$i] ^ $mask[$i % 4];
            }
        } else {
            // Append payload as-is to frame.
            $data .= $payload;
        }

        // Write to stream.
        $written = $this->write($data);

        $this->configuration->getLogger()->debug("[{scope}] Pushed '{opcode}' frame", [
            'scope' => self::SCOPE,
            'opcode' => $frame->getOpcode(),
            'final' => $frame->isFinal(),
            'content-length' => $frame->getPayloadLength(),
        ]);
        return $written;
    }

    /**
     * Secured read op
     * @param int<1, max> $length
     * @throws RuntimeException
     */
    private function read(int $length): string
    {
        $data = '';
        $read = 0;
        while ($read < $length) {
            /** @var int<1, max> $readLength */
            $readLength = $length - $read;
            $got = $this->stream->read($readLength);
            if (empty($got)) {
                throw new RuntimeException('Empty read; connection dead?');
            }
            $data .= $got;
            $read = strlen($data);
        }
        return $data;
    }

    /**
     * Secured write op
     * @throws RuntimeException
     */
    private function write(string $data): int
    {
        $length = strlen($data);
        $written = $this->stream->write($data);
        if ($written < $length) {
            throw new RuntimeException("Could only write {$written} out of {$length} bytes.");
        }
        return $written;
    }

    /** @return array<int> */
    private function unpack(string $format, string $string): array
    {
        /** @var array<int> $result */
        $result = unpack($format, $string);
        return $result;
    }
}
