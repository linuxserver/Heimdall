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

namespace League\Uri\UriTemplate;

use Deprecated;
use Iterator;
use IteratorAggregate;
use League\Uri\Exceptions\SyntaxError;
use Stringable;
use ValueError;

use function array_filter;
use function array_map;
use function array_reverse;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function implode;
use function is_string;
use function ksort;
use function strlen;
use function strpos;
use function substr;

/**
 * @internal The class exposes the internal representation of an Expression and its usage
 * @link https://www.rfc-editor.org/rfc/rfc6570#section-2.2
 * @implements IteratorAggregate<VarSpecifier>
 */
final class Expression implements IteratorAggregate
{
    /** @var array<VarSpecifier> */
    private readonly array $varSpecifiers;
    /** @var array<string> */
    public readonly array $variableNames;
    public readonly string $value;

    private function __construct(public readonly Operator $operator, VarSpecifier ...$varSpecifiers)
    {
        $this->varSpecifiers = $varSpecifiers;
        $this->variableNames = array_unique(
            array_map(
                static fn (VarSpecifier $varSpecifier): string => $varSpecifier->name,
                $varSpecifiers
            )
        );
        $this->value = '{'.$operator->value.implode(',', array_map(
            static fn (VarSpecifier $varSpecifier): string => $varSpecifier->toString(),
            $varSpecifiers
        )).'}';
    }

    /**
     * @throws SyntaxError if the expression is invalid
     */
    public static function new(Stringable|string $expression): self
    {
        $parts = Operator::parseExpression($expression);

        return new Expression($parts['operator'], ...array_map(
            static fn (string $varSpec): VarSpecifier => VarSpecifier::new($varSpec),
            explode(',', $parts['variables'])
        ));
    }

    /**
     * @return Iterator<VarSpecifier>
     */
    public function getIterator(): Iterator
    {
        yield from $this->varSpecifiers;
    }

    public function expand(VariableBag $variables): string
    {
        $expanded = implode(
            $this->operator->separator(),
            array_filter(
                array_map(
                    fn (VarSpecifier $varSpecifier): string => $this->operator->expand($varSpecifier, $variables),
                    $this->varSpecifiers
                ),
                static fn ($value): bool => '' !== $value
            )
        );

        return match ('') {
            $expanded => '',
            default => $this->operator->first().$expanded,
        };
    }

    /**
     * Extracts variables from a value according to this expression.
     *
     * Named expressions match variables by name, query expressions match query
     * parameters by name regardless of their order, while positional expressions
     * assign values according to the order of their variable specifiers.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be extracted according
     *                                   to the expression.
     */
    public function extract(string $value): ExtractionResult
    {
        return $this->operator->isNamed()
            ? $this->extractNamedValues($value)
            : $this->extractPositionalValues($value);
    }

    /**
     * Extracts variables from a named expression value.
     *
     * Named variables are matched by name rather than by position. Non-exploded
     * variables may consume at most one component, while an exploded variable
     * may consume multiple components.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be matched to the
     *                                   variable specifiers.
     */
    private function extractNamedValues(string $value): ExtractionResult
    {
        /** @var non-empty-string $separator */
        $separator = $this->operator->separator();
        $components = '' === $value ? [] : explode($separator, $value);
        $matched = $this->matchNamedValues($components);
        null !== $matched || throw VariableCanNotBeExtracted::dueTo('The value "'.$value.'" cannot be extracted from the expression "'.$this->value.'".', ExtractionErrorReason::MalformedValue);

        $variables = [];
        foreach ($this->varSpecifiers as $offset => $varSpecifier) {
            $extracted = $this->operator->extract($varSpecifier, $matched[$offset] ?? null);
            $this->assertPrefixLength($varSpecifier, $extracted);

            foreach ($extracted->names() as $name) {
                $variables[$name] = $extracted->fetch($name);
            }
        }

        return ExtractionResult::success($variables);
    }

    /**
     * Matches named components against the variable specifiers.
     *
     * Components are matched by variable name rather than by position.
     * Non-exploded variables may occur at most once, while exploded variables
     * may consume multiple components.
     *
     * @param list<string> $components
     *
     * @return list<string|null>|null
     */
    private function matchNamedValues(array $components): ?array
    {
        /** @var array<string, list<string>> $componentsByName */
        $componentsByName = [];

        foreach ($components as $component) {
            [$name] = explode('=', $component, 2);
            $componentsByName[$name] ??= [];
            $componentsByName[$name][] = $component;
        }

        $explodedSpecifiers = [];
        foreach ($this->varSpecifiers as $offset => $varSpecifier) {
            if ('*' === $varSpecifier->modifier) {
                $explodedSpecifiers[$offset] = $varSpecifier;
            }
        }

        $matched = [];
        foreach ($this->varSpecifiers as $offset => $varSpecifier) {
            $name = $varSpecifier->name;
            if (!isset($componentsByName[$name])) {
                $matched[$offset] = null;

                continue;
            }

            $occurrences = $componentsByName[$name];
            if ('*' !== $varSpecifier->modifier && 1 !== count($occurrences)) {
                return null;
            }

            $matched[$offset] = implode($this->operator->separator(), $occurrences);

            unset($componentsByName[$name]);
        }

        if ([] !== $componentsByName) {
            /*
             * Remaining components have names that are not explicitly declared.
             * They can only be consumed by a single exploded variable.
             */
            if (1 !== count($explodedSpecifiers)) {
                return null;
            }

            $explodedOffset = array_key_first($explodedSpecifiers);
            $remaining = [];
            foreach ($componentsByName as $occurrences) {
                foreach ($occurrences as $component) {
                    $remaining[] = $component;
                }
            }

            $matched[$explodedOffset] = implode($this->operator->separator(), $remaining);
        }

        ksort($matched);

        return array_values($matched);
    }

    /**
     * Extracts variables from a positional expression value.
     *
     * Values are assigned to variable specifiers in their declared order.
     * Exploded variables consume the remaining values required to leave enough
     * values for the following variable specifiers.
     *
     * @throws VariableCanNotBeExtracted If a value cannot be extracted or a value
     *                                   exceeds its variable's prefix length.
     */
    private function extractPositionalValues(string $value): ExtractionResult
    {
        return $this->extractPositionalCandidates(
            value: $value,
            varSpecifierOffset: 0,
            valueOffset: 0,
            previousResult: ExtractionResult::empty(),
        );
    }

    private function extractPositionalCandidates(
        string $value,
        int $varSpecifierOffset,
        int $valueOffset,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        $varSpecifier = $this->varSpecifiers[$varSpecifierOffset];
        $lastVarSpecifier = $varSpecifierOffset + 1 === count($this->varSpecifiers);
        if ($lastVarSpecifier) {
            $extracted = $this->operator->extract(
                $varSpecifier,
                substr($value, $valueOffset),
            );
            $this->assertPrefixLength($varSpecifier, $extracted);

            return $previousResult->reconcile($extracted);
        }

        /** @var non-empty-string $separator */
        $separator = $this->operator->separator();
        $separatorLength = strlen($separator);
        $positions = $this->delimiterPositions($value, $valueOffset, $separator);

        $candidates = [];
        foreach ($positions as $position) {
            $candidates[] = [
                'end' => $position,
                'next' => $position + $separatorLength,
            ];
        }

        if ('*' === $varSpecifier->modifier) {
            $candidates = array_reverse($candidates);
        }

        $reasons = [];
        $missingNames = [];

        foreach ($candidates as ['end' => $end, 'next' => $next]) {
            try {
                $extracted = $this->operator->extract(
                    $varSpecifier,
                    substr($value, $valueOffset, $end - $valueOffset),
                );
                $this->assertPrefixLength($varSpecifier, $extracted);

                return $this->extractPositionalCandidates(
                    value: $value,
                    varSpecifierOffset: $varSpecifierOffset + 1,
                    valueOffset: $next,
                    previousResult: $previousResult->reconcile($extracted),
                );
            } catch (VariableCanNotBeExtracted $exception) {
                $reasons = [...$reasons, ...$exception->reasons];
                $missingNames = [...$missingNames, ...$exception->missingNames];
            }
        }

        throw VariableCanNotBeExtracted::dueToSuitableCandidateNotFound(
            $value,
            $reasons,
            $missingNames,
        );
    }

    /**
     * Finds all positions of a delimiter at or after the given offset.
     *
     * @param int $offset The position from which to search.
     *
     *
     * @throws ValueError If the delimiter is empty.
     * @return list<int> The positions at which the delimiter occurs.
     */
    private function delimiterPositions(string $value, int $offset, string $delimiter): array
    {
        '' !== $delimiter || throw new ValueError('The delimiter cannot be empty.');

        $positions = [];
        $position = $offset;

        while (false !== ($position = strpos($value, $delimiter, $position))) {
            $positions[] = $position;
            $position += strlen($delimiter);
        }

        return $positions;
    }

    /**
     * Ensures that an extracted value does not exceed its variable's prefix length.
     *
     * @throws VariableCanNotBeExtracted If the extracted value exceeds the
     *                                   variable's prefix length.
     */
    private function assertPrefixLength(VarSpecifier $varSpecifier, ExtractionResult $variables): void
    {
        if (0 === $varSpecifier->position) {
            return;
        }

        $value = $variables->fetch($varSpecifier->name);
        if (null === $value || !is_string($value->value)) {
            return;
        }

        (mb_strlen($value->value) <= $varSpecifier->position) || throw VariableCanNotBeExtracted::dueTo('The value for variable "'.$varSpecifier->name.'" exceeds the prefix length.', ExtractionErrorReason::PrefixLengthExceeded);
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @throws SyntaxError if the expression is invalid
     * @see Expression::new()
     *
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     */
    #[Deprecated(message:'use League\Uri\UriTemplate\Exppression::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $expression): self
    {
        return self::new($expression);
    }
}
