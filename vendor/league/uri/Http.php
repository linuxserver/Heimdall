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

use Deprecated;
use JsonSerializable;
use League\Uri\Contracts\UriException;
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\UriTemplate\TemplateCanNotBeExpanded;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;

/**
 * @phpstan-import-type InputComponentMap from UriString
 */
final class Http implements Stringable, Psr7UriInterface, JsonSerializable
{
    private readonly UriInterface $uri;

    private function __construct(UriInterface $uri)
    {
        if (null === $uri->getScheme() && '' === $uri->getHost()) {
            throw new SyntaxError('An URI without scheme cannot contain an empty host string according to PSR-7: '.$uri);
        }

        $port = $uri->getPort();
        if (null !== $port && ($port < 0 || $port > 65535)) {
            throw new SyntaxError('The URI port is outside the established TCP and UDP port ranges: '.$uri);
        }

        $this->uri = $this->normalizePsr7Uri($uri);
    }

    /**
     * PSR-7 UriInterface makes the following normalization.
     *
     * Safely stringify input when possible for League UriInterface compatibility.
     *
     * Query, Fragment and User Info when undefined are normalized to the empty string
     */
    private function normalizePsr7Uri(UriInterface $uri): UriInterface
    {
        $components = [];
        if ('' === $uri->getFragment()) {
            $components['fragment'] = null;
        }

        if ('' === $uri->getQuery()) {
            $components['query'] = null;
        }

        if ('' === $uri->getUserInfo()) {
            $components['user'] = null;
            $components['pass'] = null;
        }

        return match ($components) {
            [] => $uri,
            default => Uri::fromComponents([...$uri->toComponents(), ...$components]),
        };
    }

    /**
     * Create a new instance from a string or a stringable object.
     */
    public static function new(Stringable|string $uri = ''): self
    {
        return self::fromComponents(UriString::parse($uri));
    }

    /**
     * Create a new instance from a hash of parse_url parts.
     *
     * @param InputComponentMap $components a hash representation of the URI similar
     *                                      to PHP parse_url function result
     */
    public static function fromComponents(array $components): self
    {
        $components += [
            'scheme' => null, 'user' => null, 'pass' => null, 'host' => null,
            'port' => null, 'path' => '', 'query' => null, 'fragment' => null,
        ];

        if ('' === $components['user']) {
            $components['user'] = null;
        }

        if ('' === $components['pass']) {
            $components['pass'] = null;
        }

        if ('' === $components['query']) {
            $components['query'] = null;
        }

        if ('' === $components['fragment']) {
            $components['fragment'] = null;
        }

        return new self(Uri::fromComponents($components));
    }

    /**
     * Create a new instance from the environment.
     */
    public static function fromServer(array $server): self
    {
        return new self(Uri::fromServer($server));
    }

    /**
     * Create a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    public static function fromBaseUri(Stringable|string $uri, Stringable|string|null $baseUri = null): self
    {
        return new self(Uri::fromBaseUri($uri, $baseUri));
    }

    /**
     * Creates a new instance from a template.
     *
     * @throws TemplateCanNotBeExpanded if the variables are invalid or missing
     * @throws UriException if the variables are invalid or missing
     */
    public static function fromTemplate(Stringable|string $template, iterable $variables = []): self
    {
        return new self(Uri::fromTemplate($template, $variables));
    }

    public function getScheme(): string
    {
        return $this->uri->getScheme() ?? '';
    }

    public function getAuthority(): string
    {
        return $this->uri->getAuthority() ?? '';
    }

    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo() ?? '';
    }

    public function getHost(): string
    {
        return $this->uri->getHost() ?? '';
    }

    public function getPort(): ?int
    {
        return $this->uri->getPort();
    }

    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    public function getQuery(): string
    {
        return $this->uri->getQuery() ?? '';
    }

    public function getFragment(): string
    {
        return $this->uri->getFragment() ?? '';
    }

    public function __toString(): string
    {
        return $this->uri->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->uri->toString();
    }

    /**
     * Safely stringify input when possible for League UriInterface compatibility.
     */
    private function filterInput(string $str): ?string
    {
        return match ('') {
            $str => null,
            default => $str,
        };
    }

    private function newInstance(UriInterface $uri): self
    {
        return match ($this->uri->toString()) {
            $uri->toString() => $this,
            default => new self($uri),
        };
    }

    public function withScheme(string $scheme): self
    {
        return $this->newInstance($this->uri->withScheme($this->filterInput($scheme)));
    }

    public function withUserInfo(string $user, ?string $password = null): self
    {
        return $this->newInstance($this->uri->withUserInfo($this->filterInput($user), $password));
    }

    public function withHost(string $host): self
    {
        return $this->newInstance($this->uri->withHost($this->filterInput($host)));
    }

    public function withPort(?int $port): self
    {
        return $this->newInstance($this->uri->withPort($port));
    }

    public function withPath(string $path): self
    {
        return $this->newInstance($this->uri->withPath($path));
    }

    public function withQuery(string $query): self
    {
        return $this->newInstance($this->uri->withQuery($this->filterInput($query)));
    }

    public function withFragment(string $fragment): self
    {
        return $this->newInstance($this->uri->withFragment($this->filterInput($fragment)));
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::new()
     *
     * Create a new instance from a string.
     */
    #[Deprecated(message:'use League\Uri\Http::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $uri = ''): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromComponents()
     *
     * Create a new instance from a hash of parse_url parts.
     *
     * @param InputComponentMap $components a hash representation of the URI similar
     *                                      to PHP parse_url function result
     */
    #[Deprecated(message:'use League\Uri\Http::fromComponents() instead', since:'league/uri:7.0.0')]
    public static function createFromComponents(array $components): self
    {
        return self::fromComponents($components);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromServer()
     *
     * Create a new instance from the environment.
     */
    #[Deprecated(message:'use League\Uri\Http::fromServer() instead', since:'league/uri:7.0.0')]
    public static function createFromServer(array $server): self
    {
        return self::fromServer($server);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::new()
     *
     * Create a new instance from a URI object.
     */
    #[Deprecated(message:'use League\Uri\Http::new() instead', since:'league/uri:7.0.0')]
    public static function createFromUri(Psr7UriInterface|UriInterface $uri): self
    {
        return self::new($uri);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Http::fromBaseUri()
     *
     * Create a new instance from a URI and a Base URI.
     *
     * The returned URI must be absolute.
     */
    #[Deprecated(message:'use League\Uri\Http::fromBaseUri() instead', since:'league/uri:7.0.0')]
    public static function createFromBaseUri(Stringable|string $uri, Stringable|string|null $baseUri = null): self
    {
        return self::fromBaseUri($uri, $baseUri);
    }
}
