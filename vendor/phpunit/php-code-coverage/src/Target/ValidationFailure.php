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
final readonly class ValidationFailure extends ValidationResult
{
    /**
     * @var non-empty-string
     */
    private string $message;

    /**
     * @param non-empty-string $message
     *
     * @noinspection PhpMissingParentConstructorInspection
     */
    protected function __construct(string $message)
    {
        $this->message = $message;
    }

    public function isFailure(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function message(): string
    {
        return $this->message;
    }
}
