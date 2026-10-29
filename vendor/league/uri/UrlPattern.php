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
use League\Uri\Contracts\UriInterface;
use League\Uri\Exceptions\SyntaxError;
use League\Uri\UrlPattern\Component;
use League\Uri\UrlPattern\ComponentName;
use League\Uri\UrlPattern\ComponentResult;
use League\Uri\UrlPattern\MatchMode;
use League\Uri\UrlPattern\PatternType;
use League\Uri\UrlPattern\Result;
use Psr\Http\Message\UriInterface as Psr7UriInterface;
use Stringable;
use Throwable;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;
use ValueError;

use function array_key_exists;
use function array_map;
use function get_debug_type;

/**
 * @property-read ?string $scheme
 * @property-read ?string $username
 * @property-read ?string $password
 * @property-read ?string $host
 * @property-read ?string $port
 * @property-read ?string $path
 * @property-read ?string $query
 * @property-read ?string $fragment
 */
final class UrlPattern
{
    /** @var array<value-of<ComponentName>, Component> $components */
    private readonly array $components;
    public readonly MatchMode $matchMode;
    public readonly bool $hasRegexpGroup;
    private readonly bool $hasVariable;
    public readonly PatternType $type;

    /**
     * @param array<non-empty-string, mixed> $patternComponents
     */
    public function __construct(array $patternComponents, MatchMode $matchMode = MatchMode::CaseSensitive)
    {
        $hasRegexpGroup = false;
        $hasVariable = false;
        $components = [];
        foreach (ComponentName::cases() as $name) {
            if (!array_key_exists($name->value, $patternComponents)) {
                if (ComponentName::Port !== $name) {
                    $components[$name->value] = Component::fromAsterisk();
                    continue;
                }

                $components[ComponentName::Port->value] = array_key_exists(ComponentName::Host->value, $patternComponents)
                    ? Component::fromEmpty()
                    : Component::fromAsterisk();
                continue;
            }

            $component = $patternComponents[$name->value];
            $component instanceof Component || throw new ValueError('the component must be a "'.Component::class.'"; '.get_debug_type($component).' given.');
            $components[$name->value] = $component;
            $hasRegexpGroup = $hasRegexpGroup || $component->hasRegexpGroup;
            $hasVariable = $hasVariable || $component->hasVariable;
        }

        $this->components = $components;
        $this->matchMode = $matchMode;
        $this->hasRegexpGroup = $hasRegexpGroup;
        $this->hasVariable = $hasVariable;
        $this->type = '*' !== $this->components[ComponentName::Scheme->value]->pattern ? PatternType::Absolute : PatternType::Relative;
    }

    public static function parse(
        Stringable|string $pattern,
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null,
        MatchMode $matchMode = MatchMode::CaseSensitive
    ): ?self {
        try {
            return self::from($pattern, $baseUrl, $matchMode);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @throws SyntaxError
     */
    public static function from(
        Stringable|string $pattern,
        Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string|null $baseUrl = null,
        MatchMode $matchMode = MatchMode::CaseSensitive
    ): self {

        return UrlPatternBuilder::from($pattern, $baseUrl)
            ->build(matchMode: $matchMode, type: PatternType::Absolute);
    }

    public function match(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $input): bool
    {
        try {
            return $this->extract($input) instanceof Result;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @throws SyntaxError
     */
    public function extract(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $input): ?Result
    {
        $components = self::components($input);
        $result = [];
        foreach ($this->components as $name => $component) {
            $found = ComponentResult::extract($components[$name], $component, ComponentName::from($name), $this->matchMode);
            if (null === $found) {
                return null;
            }

            $result[$name] = $found;
        }

        return Result::tryFrom($result, $input);
    }

    public function hasVariable(): bool
    {
        return $this->hasVariable;
    }

    public function __get(string $name): string
    {
        $key = ComponentName::tryFrom($name)?->value;
        null !== $key || throw new ValueError('the property named "'.$name.'" does not exist.');

        return $this->components[$key]->pattern;
    }

    /**
     * @return array<value-of<ComponentName>, string>
     */
    public function __debugInfo(): array
    {
        return array_map(static fn (Component $component): string => $component->pattern, $this->components);
    }

    /**
     * @throws SyntaxError
     *
     * @return array{
     *     scheme: string,
     *     username: string,
     *     password: string,
     *     host: string,
     *     port: string,
     *     path: string,
     *     query: string,
     *     fragment: string
     * }
     */
    private static function components(WhatWgUrl|Rfc3986Uri|Stringable|BackedEnum|string $input): array
    {
        if ($input instanceof WhatWgUrl || $input instanceof UriInterface || $input instanceof Rfc3986Uri) {
            return [
                ComponentName::Scheme->value => (string) $input->getScheme(),
                ComponentName::Username->value => (string) $input->getUsername(),
                ComponentName::Password->value => (string) $input->getPassword(),
                ComponentName::Host->value => (string) ($input instanceof WhatWgUrl ? $input->getAsciiHost() : $input->getHost()),
                ComponentName::Port->value => (string) $input->getPort(),
                ComponentName::Path->value => $input->getPath(),
                ComponentName::Query->value => (string) $input->getQuery(),
                ComponentName::Fragment->value => (string) $input->getFragment(),
            ];
        }

        if ($input instanceof Psr7UriInterface) {
            $username = '';
            $password = '';
            $userInfo = $input->getUserInfo();
            if ('' !== $userInfo) {
                [$username, $password] = explode(':', $userInfo, 2) + [1 => ''];
            }

            return [
                ComponentName::Scheme->value => $input->getScheme(),
                ComponentName::Username->value => $username,
                ComponentName::Password->value => $password,
                ComponentName::Host->value => $input->getHost(),
                ComponentName::Port->value => (string) $input->getPort(),
                ComponentName::Path->value => $input->getPath(),
                ComponentName::Query->value => $input->getQuery(),
                ComponentName::Fragment->value => $input->getFragment(),
            ];
        }

        $components = array_map(static fn (string|int|null $value): string => (string)$value, UriString::parse($input));
        $components[ComponentName::Username->value] = $components['user'];
        $components[ComponentName::Password->value] = $components['pass'];

        return $components;
    }
}
