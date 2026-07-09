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
final class ClassesThatImplementInterface extends Target
{
    /**
     * @var class-string
     */
    private string $interfaceName;

    /**
     * @param class-string $interfaceName
     */
    protected function __construct(string $interfaceName)
    {
        $this->interfaceName = $interfaceName;
    }

    public function isClassesThatImplementInterface(): true
    {
        return true;
    }

    /**
     * @return class-string
     */
    public function interfaceName(): string
    {
        return $this->interfaceName;
    }

    /**
     * @return non-empty-string
     */
    public function key(): string
    {
        return 'classesThatImplementInterface';
    }

    /**
     * @return non-empty-string
     */
    public function target(): string
    {
        return $this->interfaceName;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return 'Classes that implement interface ' . $this->target();
    }
}
