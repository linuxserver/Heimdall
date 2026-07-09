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
final class Function_ extends Target
{
    /**
     * @var non-empty-string
     */
    private string $functionName;

    /**
     * @param non-empty-string $functionName
     */
    protected function __construct(string $functionName)
    {
        $this->functionName = $functionName;
    }

    public function isFunction(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function functionName(): string
    {
        return $this->functionName;
    }

    /**
     * @return non-empty-string
     */
    public function key(): string
    {
        return 'functions';
    }

    /**
     * @return non-empty-string
     */
    public function target(): string
    {
        return $this->functionName;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return 'Function ' . $this->target();
    }
}
