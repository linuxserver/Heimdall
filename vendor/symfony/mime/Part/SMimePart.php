<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Part;

use Symfony\Component\Mime\Header\Headers;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SMimePart extends AbstractPart
{
    /** @internal */
    protected $_headers;

    private $body;
    private $type;
    private $subtype;
    private $parameters;

    /**
     * @param iterable|string $body
     */
    public function __construct($body, string $type, string $subtype, array $parameters)
    {
        unset($this->_headers);

        parent::__construct();

        if (!\is_string($body) && !is_iterable($body)) {
            throw new \TypeError(sprintf('The body of "%s" must be a string or a iterable (got "%s").', self::class, get_debug_type($body)));
        }

        $this->body = $body;
        $this->type = $type;
        $this->subtype = $subtype;
        $this->parameters = $parameters;
    }

    public function getMediaType(): string
    {
        return $this->type;
    }

    public function getMediaSubtype(): string
    {
        return $this->subtype;
    }

    public function bodyToString(): string
    {
        if (\is_string($this->body)) {
            return $this->body;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
        }
        $this->body = $body;

        return $body;
    }

    public function bodyToIterable(): iterable
    {
        if (\is_string($this->body)) {
            yield $this->body;

            return;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
            yield $chunk;
        }
        $this->body = $body;
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = clone parent::getHeaders();

        $headers->setHeaderBody('Parameterized', 'Content-Type', $this->getMediaType().'/'.$this->getMediaSubtype());

        foreach ($this->parameters as $name => $value) {
            $headers->setHeaderParameter('Content-Type', $name, $value);
        }

        return $headers;
    }

    public function __sleep(): array
    {
        // convert iterables to strings for serialization
        if (is_iterable($this->body)) {
            $this->body = $this->bodyToString();
        }

        $this->_headers = $this->getHeaders();

        return ['_headers', 'body', 'type', 'subtype', 'parameters'];
    }

    public function __wakeup(): void
    {
        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setAccessible(true);
        $r->setValue($this, $this->_headers);
        unset($this->_headers);
    }
}
