<?php

namespace Aws\Api\Parser;

use \Iterator;
use Aws\Exception\EventStreamDataException;
use Aws\Api\Parser\Exception\ParserException;
use Aws\Api\StructureShape;
use Psr\Http\Message\StreamInterface;

/**
 * @internal Implements a decoder for a binary encoded event stream that will
 * decode, validate, and provide individual events from the stream.
 */
class EventParsingIterator implements Iterator
{
    /** @var StreamInterface */
    private $decodingIterator;

    /** @var StructureShape */
    private $shape;

    /** @var AbstractParser */
    private $parser;

    public function __construct(
        StreamInterface $stream,
        StructureShape $shape,
        AbstractParser $parser
    ) {
        $this->decodingIterator = new DecodingEventStreamIterator($stream);
        $this->shape = $shape;
        $this->parser = $parser;
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->parseEvent($this->decodingIterator->current());
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->decodingIterator->key();
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->decodingIterator->next();
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->decodingIterator->rewind();
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->decodingIterator->valid();
    }

    private function parseEvent(array $event)
    {
        if (!empty($event['headers'][':message-type'])) {
            if ($event['headers'][':message-type'] === 'error') {
                return $this->parseError($event);
            }

            if ($event['headers'][':message-type'] !== 'event') {
                throw new ParserException('Failed to parse unknown message type.');
            }
        }

        $eventType = $event['headers'][':event-type'] ?? null;
        if (empty($eventType)) {
            throw new ParserException('Failed to parse without event type.');
        }

        $eventShape = $this->shape->getMember($eventType);
        $eventPayload = $event['payload'];

        return [
            $eventType => array_merge(
                $this->parseEventHeaders($event['headers'], $eventShape),
                $this->parseEventPayload($eventPayload, $eventShape)
            )
        ];
    }

    /**
     * @param $headers
     * @param $eventShape
     *
     * @return array
     */
    private function parseEventHeaders($headers, $eventShape): array
    {
        $parsedHeaders = [];
        foreach ($eventShape->getMembers() as $memberName => $memberProps) {
            if (isset($memberProps['eventheader'])) {
                $parsedHeaders[$memberName] = $headers[$memberName];
            }
        }

        return $parsedHeaders;
    }

    /**
     * @param $payload
     * @param $eventShape
     *
     * @return array
     */
    private function parseEventPayload($payload, $eventShape): array
    {
        $parsedPayload = [];
        foreach ($eventShape->getMembers() as $memberName => $memberProps) {
            $memberShape = $eventShape->getMember($memberName);
            if (isset($memberProps['eventpayload'])) {
                if ($memberShape->getType() === 'blob') {
                    $parsedPayload[$memberName] = $payload;
                } else {
                    $parsedPayload[$memberName] = $this->parser->parseMemberFromStream(
                        $payload,
                        $memberShape,
                        null
                    );
                }

                break;
            }
        }

        if (empty($parsedPayload) && !empty($payload->getContents())) {
            /**
             * If we did not find a member with an eventpayload trait, then we should deserialize the payload
             * using the event's shape.
             */
            $parsedPayload = $this->parser->parseMemberFromStream($payload, $eventShape, null);
        }

        return $parsedPayload;
    }

    private function parseError(array $event)
    {
        throw new EventStreamDataException(
            $event['headers'][':error-code'],
            $event['headers'][':error-message']
        );
    }
}
