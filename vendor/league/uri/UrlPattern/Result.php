<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri\UrlPattern;

use BackedEnum;
use Stringable;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;

final class Result
{
    private readonly bool $hasValue;

    /**
     * @param list<string> $inputs
     */
    private function __construct(
        public readonly ComponentResult $scheme,
        public readonly ComponentResult $username,
        public readonly ComponentResult $password,
        public readonly ComponentResult $host,
        public readonly ComponentResult $port,
        public readonly ComponentResult $path,
        public readonly ComponentResult $query,
        public readonly ComponentResult $fragment,
        public readonly array $inputs,
    ) {
        $this->hasValue = $this->scheme->hasValue()
            || $this->username->hasValue()
            || $this->password->hasValue()
            || $this->host->hasValue()
            || $this->port->hasValue()
            || $this->path->hasValue()
            || $this->query->hasValue()
            || $this->fragment->hasValue();
    }

    /**
     * Tells whether the Resul contain any variable.
     */
    public function hasValue(): bool
    {
        return $this->hasValue;
    }

    /**
     * @param array<non-empty-string, ComponentResult> $extraction
     */
    public static function tryFrom(array $extraction, Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $input): ?self
    {
        foreach ($extraction as $name => $value) {
            if (!$value instanceof ComponentResult || null === ComponentName::tryFrom($name)) {
                return null;
            }
        }

        $empty = ComponentResult::empty();

        return new self(
            scheme: $extraction[ComponentName::Scheme->value] ?? $empty,
            username: $extraction[ComponentName::Username->value] ?? $empty,
            password: $extraction[ComponentName::Password->value] ?? $empty,
            host: $extraction[ComponentName::Host->value] ?? $empty,
            port: $extraction[ComponentName::Port->value] ?? $empty,
            path: $extraction[ComponentName::Path->value] ?? $empty,
            query: $extraction[ComponentName::Query->value] ?? $empty,
            fragment: $extraction[ComponentName::Fragment->value] ?? $empty,
            inputs: [match (true) {
                $input instanceof Rfc3986Uri => $input->toString(),
                $input instanceof WhatWgUrl => $input->toAsciiString(),
                $input instanceof BackedEnum => (string) $input->value,
                default => (string) $input,
            }],
        );
    }
}
