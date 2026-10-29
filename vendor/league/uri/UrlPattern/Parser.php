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

use TypeError;

use function array_key_exists;
use function count;
use function in_array;
use function min;
use function preg_match;
use function strtolower;
use function substr;

final class Parser
{
    private const SPECIAL_SCHEMES = ['ftp', 'file', 'http', 'https', 'ws', 'wss'];

    /** @var list<Token> */
    private array $tokenList = [];

    private int $tokenIndex = 0;

    private int $tokenIncrement = 1;

    private int $componentStart = 0;

    private State $state = State::Init;

    private int $groupDepth = 0;

    private int $hostnameIPv6BracketDepth = 0;

    private bool $shouldTreatAsStandardUrl = false;

    /** @var array<string, string> */
    private array $components = [];

    public function __construct(private string $input)
    {
        $this->parse();
    }

    public function components(): array
    {
        return $this->components;
    }

    public function hasRegexpGroup(): bool
    {
        //@todo
        return false;
    }

    private function parse(): array
    {
        $this->tokenList = PathToRegexp::lex($this->input, lenient: true);

        for (; $this->tokenIndex < count($this->tokenList); $this->tokenIndex += $this->tokenIncrement) {
            $this->tokenIncrement = 1;

            if (TokenType::End === $this->currentToken()->type) {
                if (State::Init === $this->state) {
                    $this->rewind();
                    if ($this->isHashPrefix()) {
                        $this->changeState(State::Fragment, 1);
                    } elseif ($this->isSearchPrefix()) {
                        $this->changeState(State::Query, 1);
                    } else {
                        $this->changeState(State::Path, 0);
                    }

                    continue;
                }

                if (State::Authority === $this->state) {
                    $this->rewindAndSetState(State::Host);

                    continue;
                }

                $this->changeState(State::Done, 0);

                break;
            }

            if ($this->groupDepth > 0) {
                if ($this->isGroupClose()) {
                    --$this->groupDepth;
                } else {
                    continue;
                }
            }

            if ($this->isGroupOpen()) {
                ++$this->groupDepth;

                continue;
            }

            switch ($this->state) {
                case State::Init:
                    if ($this->isProtocolSuffix()) {
                        $this->rewindAndSetState(State::Scheme);
                    }

                    break;

                case State::Scheme:
                    if ($this->isProtocolSuffix()) {
                        $this->computeShouldTreatAsStandardUrl();

                        $nextState = State::Path;
                        $skip = 1;

                        if ($this->nextIsAuthoritySlashes()) {
                            $nextState = State::Authority;
                            $skip = 3;
                        } elseif ($this->shouldTreatAsStandardUrl) {
                            $nextState = State::Authority;
                        }

                        $this->changeState($nextState, $skip);
                    }

                    break;

                case State::Authority:
                    if ($this->isIdentityTerminator()) {
                        $this->rewindAndSetState(State::Username);
                    } elseif (
                        $this->isPathnameStart()
                        || $this->isSearchPrefix()
                        || $this->isHashPrefix()
                    ) {
                        $this->rewindAndSetState(State::Host);
                    }

                    break;

                case State::Username:
                    if ($this->isPasswordPrefix()) {
                        $this->changeState(State::Password, 1);
                    } elseif ($this->isIdentityTerminator()) {
                        $this->changeState(State::Host, 1);
                    }

                    break;

                case State::Password:
                    if ($this->isIdentityTerminator()) {
                        $this->changeState(State::Host, 1);
                    }

                    break;

                case State::Host:
                    if ($this->isIPv6Open()) {
                        ++$this->hostnameIPv6BracketDepth;
                    } elseif ($this->isIPv6Close()) {
                        --$this->hostnameIPv6BracketDepth;
                    }

                    if (
                        $this->isPortPrefix()
                        && 0 === $this->hostnameIPv6BracketDepth
                    ) {
                        $this->changeState(State::Port, 1);
                    } elseif ($this->isPathnameStart()) {
                        $this->changeState(State::Path, 0);
                    } elseif ($this->isSearchPrefix()) {
                        $this->changeState(State::Query, 1);
                    } elseif ($this->isHashPrefix()) {
                        $this->changeState(State::Fragment, 1);
                    }

                    break;

                case State::Port:
                    if ($this->isPathnameStart()) {
                        $this->changeState(State::Path, 0);
                    } elseif ($this->isSearchPrefix()) {
                        $this->changeState(State::Query, 1);
                    } elseif ($this->isHashPrefix()) {
                        $this->changeState(State::Fragment, 1);
                    }

                    break;

                case State::Path:
                    if ($this->isSearchPrefix()) {
                        $this->changeState(State::Query, 1);
                    } elseif ($this->isHashPrefix()) {
                        $this->changeState(State::Fragment, 1);
                    }

                    break;

                case State::Query:
                    if ($this->isHashPrefix()) {
                        $this->changeState(State::Fragment, 1);
                    }

                    break;

                case State::Fragment:
                case State::Done:
                    break;
            }
        }

        // If hostname was specified but port wasn't, the parser produces
        // an empty port component.
        if ($this->hasComponent(State::Host->value) && ! $this->hasComponent(State::Port->value)) {
            $this->components[State::Port->value] = '';
        }

        return $this->components;
    }

    private function changeState(State $newState, int $skip): void
    {
        match ($this->state) {
            State::Init, State::Authority, State::Done => null,
            default => $this->setComponent($this->state),
        };

        if (State::Init !== $this->state && State::Done !== $newState) {
            if (
                in_array($this->state, [State::Scheme, State::Authority, State::Username, State::Password], true)
                && in_array($newState, [State::Port, State::Path, State::Query, State::Fragment], true)
            ) {
                $this->components[State::Host->value] ??= '';
            }

            if (
                in_array($this->state, [State::Scheme, State::Authority, State::Username, State::Password, State::Host, State::Port], true)
                && in_array($newState, [State::Query, State::Fragment], true)
            ) {
                $this->components[State::Path->value] ??= $this->shouldTreatAsStandardUrl ? '/' : '';
            }

            if (
                in_array($this->state, [State::Scheme, State::Authority, State::Username, State::Password, State::Host, State::Port, State::Path], true)
                && State::Fragment === $newState
            ) {
                $this->components[State::Query->value] ??= '';
            }
        }

        $this->changeStateWithoutSettingComponent($newState, $skip);
    }

    private function changeStateWithoutSettingComponent(
        State $newState,
        int $skip,
    ): void {
        $this->state = $newState;
        $this->componentStart = $this->tokenIndex + $skip;
        $this->tokenIndex += $skip;
        $this->tokenIncrement = 0;
    }

    private function rewind(): void
    {
        $this->tokenIndex = $this->componentStart;
        $this->tokenIncrement = 0;
    }

    private function rewindAndSetState(State $newState): void
    {
        $this->rewind();
        $this->state = $newState;
    }

    private function currentToken(): Token
    {
        return $this->safeToken($this->tokenIndex);
    }

    private function safeToken(int $index): Token
    {
        if ($index < 0) {
            $index = count($this->tokenList) + $index;
        }

        return $this->tokenList[
        min($index, count($this->tokenList) - 1)
        ];
    }

    private function isNonSpecialPatternChar(
        int $index,
        string $value,
    ): bool {
        $token = $this->safeToken($index);

        return $token->value === $value
            && in_array($token->type, [TokenType::Char, TokenType::EscapedChar, TokenType::InvalidChar], true);
    }

    private function isProtocolSuffix(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, ':');
    }

    private function nextIsAuthoritySlashes(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex + 1, '/')
            && $this->isNonSpecialPatternChar($this->tokenIndex + 2, '/');
    }

    private function isIdentityTerminator(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, '@');
    }

    private function isPasswordPrefix(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, ':');
    }

    private function isPortPrefix(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, ':');
    }

    private function isPathnameStart(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, '/');
    }

    private function isSearchPrefix(): bool
    {
        if ($this->isNonSpecialPatternChar($this->tokenIndex, '?')) {
            return true;
        }

        if ('?' !== $this->currentToken()->value) {
            return false;
        }

        $previousToken = $this->safeToken($this->tokenIndex - 1);

        return ! in_array($previousToken->type, [
            TokenType::Name,
            TokenType::Regex,
            TokenType::Close,
            TokenType::Asterisk,
        ], true);
    }

    private function isHashPrefix(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, '#');
    }

    private function isGroupOpen(): bool
    {
        return TokenType::Open === $this->currentToken()->type;
    }

    private function isGroupClose(): bool
    {
        return TokenType::Close === $this->currentToken()->type;
    }

    private function isIPv6Open(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, '[');
    }

    private function isIPv6Close(): bool
    {
        return $this->isNonSpecialPatternChar($this->tokenIndex, ']');
    }

    private function setComponent(State $name): void
    {
        $this->components[$name->value] = Component::fromPattern($this->makeComponentString())->pattern;
    }

    private function makeComponentString(): string
    {
        $token = $this->currentToken();
        $componentCharStart = $this->safeToken($this->componentStart)->index;

        return substr(
            $this->input,
            $componentCharStart,
            $token->index - $componentCharStart,
        );
    }

    private function hasComponent(string $name): bool
    {
        return array_key_exists($name, $this->components);
    }

    private function computeShouldTreatAsStandardUrl(): void
    {
        $regexp = PathToRegexp::stringToRegexp(
            $this->makeComponentString(),
            options: [
                'delimiter' => '',
                'prefixes' => '',
                'sensitive' => true,
                'strict' => true,
                'encodePart' => self::protocolEncodeCallback(...),
            ],
        );

        $this->shouldTreatAsStandardUrl = self::isSpecialScheme($regexp);
    }

    private static function isSpecialScheme(string $regexp): bool
    {
        foreach (self::SPECIAL_SCHEMES as $scheme) {
            if (1 === preg_match('~'.$regexp.'~', $scheme)) {
                return true;
            }
        }

        return false;
    }

    private static function protocolEncodeCallback(string $input): string
    {
        return match (true) {
            '' === $input => '',
            1 === preg_match('/^[-+.A-Za-z0-9]*$/', $input) => strtolower($input),
            default => throw new TypeError("Invalid protocol '{$input}'."),
        };
    }
}
