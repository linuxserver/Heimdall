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

namespace League\Uri;

use BackedEnum;
use League\Uri\Contracts\Conditionable;
use League\Uri\Contracts\Transformable;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\UrlPattern\Component;
use League\Uri\UrlPattern\ComponentName;
use League\Uri\UrlPattern\MatchMode;
use League\Uri\UrlPattern\Parser;
use League\Uri\UrlPattern\PatternType;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;
use Throwable;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;

use function array_filter;
use function is_bool;
use function strrpos;
use function substr;

final class UrlPatternBuilder implements Conditionable, Transformable
{
    private ?Component $scheme = null;
    private ?Component $username = null;
    private ?Component $password = null;
    private ?Component $host = null;
    private ?Component $port = null;
    private ?Component $path = null;
    private ?Component $query = null;
    private ?Component $fragment = null;
    private Rfc3986Uri|WhatWgUrl|Psr7UriInterface|UriInterface|null $baseUrl = null;

    public function __construct(
        Component|BackedEnum|Stringable|string|null $scheme = null,
        Component|BackedEnum|Stringable|string|null $username = null,
        Component|BackedEnum|Stringable|string|null $password = null,
        Component|BackedEnum|Stringable|string|null $host = null,
        Component|BackedEnum|Stringable|string|null $port = null,
        Component|BackedEnum|Stringable|string|null $path = null,
        Component|BackedEnum|Stringable|string|null $query = null,
        Component|BackedEnum|Stringable|string|null $fragment = null,
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null
    ) {
        $this
            ->scheme($scheme)
            ->username($username)
            ->password($password)
            ->host($host)
            ->port($port)
            ->path($path)
            ->query($query)
            ->fragment($fragment)
            ->baseUrl($baseUrl);
    }

    public static function from(
        Stringable|string $pattern,
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null
    ): self {
        $components = (new Parser((string) $pattern))->components();

        return new self(
            scheme: $components[ComponentName::Scheme->value] ?? null,
            username: $components[ComponentName::Username->value] ?? null,
            password: $components[ComponentName::Password->value] ?? null,
            host: $components[ComponentName::Host->value] ?? null,
            port: $components[ComponentName::Port->value] ?? null,
            path: $components[ComponentName::Path->value] ?? null,
            query: $components[ComponentName::Query->value] ?? null,
            fragment: $components[ComponentName::Fragment->value] ?? null,
            baseUrl: $baseUrl,
        );
    }

    /**
     * Tells whether the URLPattern can be built with the current Builder state.
     */
    public function validate(?PatternType $type = null): bool
    {
        try {
            $this->build(type: $type);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws SyntaxError if the URLPattern can not be build with the current Builder state
     */
    public function guard(?PatternType $type = null): self
    {
        try {
            $this->build(type: $type);

            return $this;
        } catch (Throwable $exception) {
            throw new SyntaxError('The current builder cannot generate a valid URL Pattern.', previous: $exception);
        }
    }

    /**
     * @throws SyntaxError
     */
    public function build(
        MatchMode $matchMode = MatchMode::CaseSensitive,
        ?PatternType $type = null
    ): UrlPattern {
        $components = array_filter(
            $this->applyBaseUrl([
                ComponentName::Scheme->value => $this->scheme ?? null,
                ComponentName::Username->value => $this->username ?? null,
                ComponentName::Password->value => $this->password ?? null,
                ComponentName::Host->value => $this->host ?? null,
                ComponentName::Port->value => $this->port ?? null,
                ComponentName::Path->value => $this->path ?? null,
                ComponentName::Query->value => $this->query ?? null,
                ComponentName::Fragment->value => $this->fragment ?? null,
            ]),
            static fn (?Component $component): bool => $component instanceof Component
        );

        return match ($type) {
            null => new UrlPattern($components, $matchMode),
            PatternType::Relative => isset($components[ComponentName::Scheme->value])
                ? throw new SyntaxError('An relative URL pattern must not contain a scheme component.')
                : new UrlPattern($components, $matchMode),
            default => isset($components[ComponentName::Scheme->value])
                ? new UrlPattern($components, $matchMode)
                : throw new SyntaxError('An absolute URL pattern must contain a scheme component.'),
        };
    }

    /**
     * @return array<non-empty-string, Component>
     */
    private function applyBaseUrl(array $components): array
    {
        if (null === $this->baseUrl) {
            return $components;
        }

        $hasScheme = isset($components[ComponentName::Scheme->value]);
        $hasHost = isset($components[ComponentName::Host->value]);
        $hasPort = isset($components[ComponentName::Port->value]);

        if (! $hasScheme) {
            $baseScheme = (string) $this->baseUrl->getScheme();
            $components[ComponentName::Scheme->value] = '' !== $baseScheme
                ? Component::fromPattern($baseScheme)
                : Component::fromAsterisk();
        }

        if (! $hasScheme && ! $hasHost) {
            $baseHost = (string) ($this->baseUrl instanceof WhatWgUrl ? $this->baseUrl->getAsciiHost() : $this->baseUrl->getHost());
            $components[ComponentName::Host->value] = '' !== $baseHost
                ? Component::fromPattern($baseHost)
                : Component::fromAsterisk();
        }

        if (! $hasPort && ! $hasScheme && ! $hasHost) {
            $basePort = $this->baseUrl->getPort();
            $components[ComponentName::Port->value] = null !== $basePort
                ? Component::fromPattern((string) $basePort)
                : Component::fromEmpty();
        }

        $basePath = $this->baseUrl->getPath();
        $components[ComponentName::Path->value] = match (true) {
            !isset($components[ComponentName::Path->value]) => '' !== $basePath ? Component::fromPattern($basePath) : Component::fromAsterisk(),
            default => self::resolvePathname($components[ComponentName::Path->value], $basePath),
        };

        return $components;
    }

    private static function resolvePathname(Component $pathname, string $basePathname): Component
    {
        if (str_starts_with($pathname->pattern, '/')) {
            return $pathname;
        }

        $slash = strrpos($basePathname, '/');

        $pattern = false === $slash
            ? $pathname->pattern
            : substr($basePathname, 0, $slash + 1).$pathname->pattern;

        return Component::fromPattern($pattern);
    }

    private static function filter(Component|BackedEnum|Stringable|string|null $component): ?Component
    {
        return match (true) {
            $component instanceof Component => $component,
            null === $component => null,
            $component instanceof BackedEnum => Component::fromPattern((string) $component->value),
            default => Component::fromPattern((string) $component),
        };
    }

    public function scheme(Component|BackedEnum|Stringable|string|null $scheme): self
    {
        $this->scheme = self::filter($scheme);

        return $this;
    }

    public function username(Component|BackedEnum|Stringable|string|null $username): self
    {
        $this->username = self::filter($username);

        return $this;
    }

    public function password(Component|BackedEnum|Stringable|string|null $password): self
    {
        $this->password = self::filter($password);

        return $this;
    }

    public function host(Component|BackedEnum|Stringable|string|null $host): self
    {
        $this->host = self::filter($host);

        return $this;
    }

    public function port(Component|BackedEnum|Stringable|string|null $port): self
    {
        $this->port = self::filter($port);

        return $this;
    }

    public function path(Component|BackedEnum|Stringable|string|null $path): self
    {
        $this->path = self::filter($path);

        return $this;
    }

    public function query(Component|BackedEnum|Stringable|string|null $query): self
    {
        $this->query = self::filter($query);

        return $this;
    }

    public function fragment(Component|BackedEnum|Stringable|string|null $fragment): self
    {
        $this->fragment = self::filter($fragment);

        return $this;
    }

    public function baseUrl(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null): self
    {
        if (null === $baseUrl) {
            $this->baseUrl = null;

            return $this;
        }

        $baseUrl = match (true) {
            $baseUrl instanceof Rfc3986Uri,
            $baseUrl instanceof WhatWgUrl,
            $baseUrl instanceof UriInterface,
            $baseUrl instanceof Psr7UriInterface => $baseUrl,
            default => Uri::new($baseUrl),
        };

        '' !== (string) $baseUrl->getScheme() ||  throw new SyntaxError('The BaseUrl must represent an absolute URI.');

        $this->baseUrl = $baseUrl;

        return $this;
    }

    public function reset(): self
    {
        $this->scheme = null;
        $this->username = null;
        $this->password = null;
        $this->host = null;
        $this->port = null;
        $this->path = null;
        $this->query = null;
        $this->fragment = null;
        $this->baseUrl = null;

        return $this;
    }

    /**
     * Executes the given callback with the current instance
     * and returns the current instance.
     *
     * @param callable(self): self $callback
     */
    public function transform(callable $callback): static
    {
        return $callback($this);
    }

    public function when(callable|bool $condition, callable $onSuccess, ?callable $onFail = null): static
    {
        if (!is_bool($condition)) {
            $condition = $condition($this);
        }

        return match (true) {
            $condition => $onSuccess($this),
            null !== $onFail => $onFail($this),
            default => $this,
        } ?? $this;
    }
}
