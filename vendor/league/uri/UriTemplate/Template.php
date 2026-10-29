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

use BackedEnum;
use Deprecated;
use League\Uri\Exceptions\SyntaxError;
use Stringable;
use Uri\Rfc3986\Uri as Rfc3986Uri;
use Uri\WhatWg\Url as WhatWgUrl;
use ValueError;

use function array_filter;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_unique;
use function array_values;
use function count;
use function implode;
use function preg_match_all;
use function preg_replace;
use function str_starts_with;
use function strlen;
use function strpbrk;
use function strpos;
use function substr;

use const PREG_OFFSET_CAPTURE;
use const PREG_SET_ORDER;

/**
 * @internal The class exposes the internal representation of a Template and its usage
 */
final class Template implements Stringable
{
    /**
     * Expression regular expression pattern.
     */
    private const REGEXP_EXPRESSION_DETECTOR = '/(?<expression>\{[^}]*})/x';

    /** @var array<string> */
    public readonly array $variableNames;

    /** @var list<Expression|Literal>  */
    private readonly array $parts;

    private function __construct(public readonly string $value, Expression|Literal ...$parts)
    {
        $this->parts = array_values($parts);
        $this->variableNames = array_unique(
            array_merge(
                ...array_map(
                    static fn (Expression $expression): array => $expression->variableNames,
                    array_filter($parts, fn (Expression|Literal $p): bool => $p instanceof Expression)
                )
            )
        );
    }

    /**
     * @throws SyntaxError if the template contains invalid expressions
     * @throws SyntaxError if the template contains invalid variable specification
     */
    public static function new(BackedEnum|Stringable|string $template): self
    {
        if ($template instanceof BackedEnum) {
            $template = $template->value;
        }

        $template = (string) $template;
        /** @var string $remainder */
        $remainder = preg_replace(self::REGEXP_EXPRESSION_DETECTOR, '', $template);
        false === strpbrk($remainder, '{}') || throw new SyntaxError('The template "'.$template.'" contains invalid expressions.');

        preg_match_all(self::REGEXP_EXPRESSION_DETECTOR, $template, $founds, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $parts = [];
        $offset = 0;
        foreach ($founds as $found) {
            $expression = $found['expression'][0];
            $position = $found['expression'][1];

            if ($position > $offset) {
                $parts[] = new Literal(substr($template, $offset, $position - $offset));
            }

            $parts[] = Expression::new($expression);
            $offset = $position + strlen($found[0][0]);
        }

        if ($offset < strlen($template)) {
            $parts[] = new Literal(substr($template, $offset));
        }

        return new self($template, ...$parts);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**-----------
     * Expand API
    ------------*/

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid
     */
    public function expand(iterable|ExtractionResult $variables = []): string
    {
        if (!$variables instanceof VariableBag) {
            $variables = new VariableBag($variables);
        }

        return $this->expandAll($variables);
    }

    /**
     * @throws TemplateCanNotBeExpanded if the variables are invalid or missing
     */
    public function expandOrFail(iterable|ExtractionResult $variables = []): string
    {
        if (!$variables instanceof VariableBag) {
            $variables = new VariableBag($variables);
        }

        $missing = array_filter($this->variableNames, static fn (string $name): bool => !isset($variables[$name]));
        [] === $missing || throw TemplateCanNotBeExpanded::dueToMissingVariables(...$missing);

        return $this->expandAll($variables);
    }

    private function expandAll(VariableBag $variables): string
    {
        return implode('', array_map(
            static fn (Literal|Expression $part): string => $part instanceof Literal ? $part->encoded : $part->expand($variables),
            $this->parts,
        ));
    }

    /**-----------
     * Extract API
    ------------*/

    /**
     * Tells whether the value matches this URI template.
     *
     * This method performs a strict extraction and returns `true` only when the
     * value can be completely matched and all variables can be extracted.
     *
     * @return bool true`if the value matches the template, `false` otherwise.
     */
    public function match(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $value): bool
    {
        try {
            $this->extractOrFail($value);

            return true;
        } catch (VariableCanNotBeExtracted) {

            return false;
        }
    }

    /**
     * Extracts the variables from a value.
     *
     * Extraction is lenient: variables that are missing from the value are
     * represented in the result, while extraction failures are represented by
     * a failed result containing the reasons for the failure.
     *
     * @return ExtractionResult The extraction result.
     */
    public function extract(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $value): ExtractionResult
    {
        try {
            return $this->extractAll(self::uriString($value));
        } catch (VariableCanNotBeExtracted $exception) {
            return ExtractionResult::failure($exception, $this);
        }
    }

    /**
     * Extracts the variables from a value.
     *
     * Unlike {@see extract()}, this method is strict and reports extraction
     * failures or missing variables by throwing an exception.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be completely matched,
     *                                   a variable cannot be extracted, or a
     *                                   variable defined by the template is missing.
     *
     * @return ExtractionResult The extracted variables.
     */
    public function extractOrFail(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $value): ExtractionResult
    {
        $value = self::uriString($value);
        try {
            $result = $this->extractAll($value);
        } catch (VariableCanNotBeExtracted $exception) {
            throw VariableCanNotBeExtracted::dueToExtractionFailure($value, $this, $exception);
        }

        return [] === $result->missingNames()
            ? $result
            : throw VariableCanNotBeExtracted::dueToMissingVariables($value, $this, $result);
    }

    private static function uriString(Rfc3986Uri|WhatWgUrl|BackedEnum|Stringable|string $uri): string
    {
        return match (true) {
            $uri instanceof Rfc3986Uri => $uri->toRawString(),
            $uri instanceof WhatWgUrl => $uri->toUnicodeString(),
            $uri instanceof BackedEnum => (string) $uri->value,
            default => (string) $uri,
        };
    }

    /**
     * @throws VariableCanNotBeExtracted
     */
    private function extractAll(string $value): ExtractionResult
    {
        return $this->extractParts(
            value: $value,
            partOffset: 0,
            valueOffset: 0,
            previousResult: ExtractionResult::success(),
        );
    }

    /**
     * Extracts variables by matching the remaining template parts against the value.
     *
     * @param int $partOffset The offset of the next template part to match.
     * @param int $valueOffset The offset of the next value character to match.
     * @param ExtractionResult $previousResult The result accumulated from the
     *                                         previously matched template parts.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be matched against
     *                                   the remaining template parts.
     */
    private function extractParts(
        string $value,
        int $partOffset,
        int $valueOffset,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        if ($partOffset === count($this->parts)) {
            $valueOffset === strlen($value) || throw VariableCanNotBeExtracted::dueTo('The value contains unmatched content: "'.substr($value, $valueOffset).'".', ExtractionErrorReason::UnmatchedContent);

            return $previousResult;
        }

        return $this->parts[$partOffset] instanceof Literal
            ? $this->extractLiteral($value, $partOffset, $valueOffset, $previousResult)
            : $this->extractExpression($value, $partOffset, $valueOffset, $previousResult);
    }

    /**
     * Matches the literal against the value and continues with the remaining template parts.
     *
     * @param int $partOffset The offset of the next template part to match.
     * @param int $valueOffset The offset of the next value character to match.
     * @param ExtractionResult $previousResult The result accumulated from the
     *                                         previously matched template parts.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be matched against
     *                                   the remaining template parts.
     */
    private function extractLiteral(
        string $value,
        int $partOffset,
        int $valueOffset,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        /** @var Literal $literal */
        $literal = $this->parts[$partOffset];
        $literal->isPrefixOf(substr($value, $valueOffset)) || throw VariableCanNotBeExtracted::dueTo('The literal "'.$literal->encoded.'" does not match the value at the expected position.', ExtractionErrorReason::LiteralMismatch);

        return $this->extractParts($value, $partOffset + 1, $valueOffset + $literal->length, $previousResult);
    }

    /**
     * Extracts the variables from the expression against the value and continues with the remaining template parts.
     *
     * @param int $partOffset The offset of the next template part to match.
     * @param int $valueOffset The offset of the next value character to match.
     * @param ExtractionResult $previousResult The result accumulated from the
     *                                         previously matched template parts.
     *
     * @throws VariableCanNotBeExtracted If the value cannot be matched against
     *                                   the remaining template parts.
     */
    private function extractExpression(
        string $value,
        int $partOffset,
        int $valueOffset,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        /** @var Expression $expression */
        $expression = $this->parts[$partOffset];
        $prefix = $expression->operator->first();
        if ($expression->operator->allowEmpty() && '' !== $prefix && !str_starts_with(substr($value, $valueOffset), $prefix)) {
            return $this->extractParts($value, $partOffset + 1, $valueOffset, $previousResult->reconcile($expression->extract('')));
        }

        $expressionOffset = $this->expressionPrefix($expression, $value, $valueOffset);
        if ($partOffset + 1 === count($this->parts)) {
            return $this->extractExpressionRemainder($expression, $value, $expressionOffset, $previousResult);
        }

        $nextPart = $this->parts[$partOffset + 1];
        $delimiter = $nextPart instanceof Literal ? $nextPart->encoded : $nextPart->operator->first();

        return $this->extractExpressionCandidates($expression, $value, $partOffset, $expressionOffset, $delimiter, $previousResult);
    }

    /**
     * Extracts the last variables from the expression and matches the remaining value
     * against the end of the template.
     *
     * @param int $expressionOffset The offset of the expression value to extract.
     * @param ExtractionResult $previousResult The result accumulated from the
     *                                         previously matched template parts.
     *
     * @throws VariableCanNotBeExtracted If the expression cannot be extracted or
     *                                   the remaining value cannot be matched.
     */
    private function extractExpressionRemainder(
        Expression $expression,
        string $value,
        int $expressionOffset,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        $expressionEnd = $this->expressionEnd($expression, $value, $expressionOffset);
        $lastVariables = $expression->extract(substr($value, $expressionOffset, $expressionEnd - $expressionOffset));

        return $this->extractParts($value, count($this->parts), $expressionEnd, $previousResult->reconcile($lastVariables));
    }

    /**
     * Extracts an expression by trying each possible delimiter position and
     * continues with the remaining template parts until a complete match is found.
     *
     * For an exploded expression, candidates are matched from the last delimiter
     * position to the first to prefer the longest possible value.
     *
     * When the next template part is an expression that allows an empty value and
     * its delimiter is absent, the end of the input is considered as a candidate
     * boundary for the current expression. This allows consecutive expressions
     * to be extracted when the following expression is missing.
     *
     * @param int $partOffset The offset of the expression in the template parts.
     * @param int $expressionOffset The offset of the expression value in the input.
     * @param string $delimiter The delimiter used to identify candidate expression boundaries.
     * @param ExtractionResult $previousResult The result accumulated from the
     *                                         previously matched template parts.
     *
     * @throws VariableCanNotBeExtracted If no suitable candidate can satisfy the
     *                                   expression and the remaining template parts.
     */
    private function extractExpressionCandidates(
        Expression $expression,
        string $value,
        int $partOffset,
        int $expressionOffset,
        string $delimiter,
        ExtractionResult $previousResult,
    ): ExtractionResult {
        '' !== $delimiter || throw VariableCanNotBeExtracted::dueTo('Unable to determine the delimiter for the expression "'.$expression->value.'".', ExtractionErrorReason::UndeterminedDelimiter);
        $positions = $this->delimiterPositions($value, $expressionOffset, $delimiter);
        if ([] === $positions) {
            $nextPart = $this->parts[$partOffset + 1] ?? null;
            if (!$nextPart instanceof Expression || !$nextPart->operator->allowEmpty()) {
                return $this->extractParts($value, $partOffset + 1, $expressionOffset, $previousResult->reconcile($expression->extract('')));
            }
            $positions[] = strlen($value);
        }

        if ($expression->operator->first() === $delimiter) {
            foreach ($expression as $varSpecifier) {
                if ('*' === $varSpecifier->modifier) {
                    $positions = array_reverse($positions);
                    break;
                }
            }
        }

        $reasons = [];
        $missingNames = [];
        foreach ($positions as $position) {
            try {
                $newVar = $expression->extract(substr($value, $expressionOffset, $position - $expressionOffset));

                return $this->extractParts($value, $partOffset + 1, $position, $previousResult->reconcile($newVar));
            } catch (VariableCanNotBeExtracted $exception) {
                $reasons = [...$reasons, ...$exception->reasons];
                $missingNames = [...$missingNames, ...$exception->missingNames];
            }
        }

        throw VariableCanNotBeExtracted::dueToSuitableCandidateNotFound($value, $reasons, $missingNames);
    }

    /**
     * Validates and consumes the operator prefix of an expression.
     *
     * @param int $valueOffset The offset of the expression value in the input.
     *
     *
     * @throws VariableCanNotBeExtracted If the expression prefix does not match
     *                                   the value at the expected position.
     * @return int The offset immediately after the expression prefix.
     */
    private function expressionPrefix(Expression $expression, string $value, int $valueOffset): int
    {
        $prefix = $expression->operator->first();

        return ('' === $prefix || str_starts_with(substr($value, $valueOffset), $prefix))
            ? $valueOffset + strlen($prefix)
            : throw VariableCanNotBeExtracted::dueTo('The prefix "'.$prefix.'" does not match the value for the expression "'.$expression->value.'".', ExtractionErrorReason::PrefixMismatch);
    }

    /**
     * Finds the end of an expression value according to the URI component
     * boundaries imposed by its operator.
     *
     * Query expressions stop at a fragment delimiter, while expressions in other
     * components stop at either a query or fragment delimiter. Fragment expressions
     * have no following component boundary.
     *
     * @param int $offset The offset at which the expression value starts.
     *
     * @return int The offset of the first component delimiter, or the end of the value
     *             when no delimiter is found.
     */
    private function expressionEnd(Expression $expression, string $value, int $offset): int
    {
        $delimiters = $expression->operator->nextDelimiter();
        if (null === $delimiters) {
            return strlen($value);
        }

        $length = strlen($value);

        for ($position = $offset; $position < $length; ++$position) {
            if (str_contains($delimiters, $value[$position])) {
                return $position;
            }
        }

        return $length;
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
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @throws SyntaxError if the template contains invalid expressions
     * @throws SyntaxError if the template contains invalid variable specification
     * @deprecated Since version 7.0.0
     * @codeCoverageIgnore
     * @see Template::new()
     *
     * Create a new instance from a string.
     *
     */
    #[Deprecated(message:'use League\Uri\UriTemplate\Template::new() instead', since:'league/uri:7.0.0')]
    public static function createFromString(Stringable|string $template): self
    {
        return self::new($template);
    }
}
