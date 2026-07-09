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
final class Namespace_ extends Target
{
    /**
     * @var non-empty-string
     */
    private string $namespace;

    /**
     * @param non-empty-string $namespace
     */
    protected function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function isNamespace(): true
    {
        return true;
    }

    /**
     * @return non-empty-string
     */
    public function namespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function key(): string
    {
        return 'namespaces';
    }

    /**
     * @return non-empty-string
     */
    public function target(): string
    {
        return $this->namespace;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return 'Namespace ' . $this->target();
    }
}
