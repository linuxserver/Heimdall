<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Test\Target;

/**
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract readonly class ValidationResult
{
    public static function success(): ValidationSuccess
    {
        return new ValidationSuccess;
    }

    /**
     * @param non-empty-string $message
     */
    public static function failure(string $message): ValidationFailure
    {
        return new ValidationFailure($message);
    }

    /**
     * @phpstan-assert-if-true ValidationSuccess $this
     */
    public function isSuccess(): bool
    {
        return false;
    }

    /**
     * @phpstan-assert-if-true ValidationFailure $this
     */
    public function isFailure(): bool
    {
        return false;
    }
}
