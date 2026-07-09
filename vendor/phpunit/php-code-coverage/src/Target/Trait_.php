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
final class Trait_ extends Target
{
    /**
     * @var trait-string
     */
    private string $traitName;

    /**
     * @param trait-string $traitName
     */
    protected function __construct(string $traitName)
    {
        $this->traitName = $traitName;
    }

    public function isTrait(): true
    {
        return true;
    }

    /**
     * @return trait-string
     */
    public function traitName(): string
    {
        return $this->traitName;
    }

    /**
     * @return non-empty-string
     */
    public function key(): string
    {
        return 'traits';
    }

    /**
     * @return non-empty-string
     */
    public function target(): string
    {
        return $this->traitName;
    }

    /**
     * @return non-empty-string
     */
    public function description(): string
    {
        return 'Trait ' . $this->target();
    }
}
