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
final class Method extends Target
{
    /**
     * @var class-string
     */
    private string $className;

    /**
     * @var non-empty-string
     */
    private string $methodName;

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    protected function __construct(string $className, string $methodName)
    {
        $this->className  = $className;
        $this->methodName = $methodName;
    }

    public function isMethod(): true
    {
        return true;
    }

    /**
     * @return class-string
     */
    public function className(): string
    {
        return $this->className;
    }

    /**
     * @return non-empty-string
     */
    public function methodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return non-empty-string
     */
    public function key(): string
    {
        return 'methods';
    }

    /**
     * @return non-empty-string
     */
    public function target(): string
    {
        return $this->className . '::' . $this->methodName;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return 'Method ' . $this->target();
    }
}
