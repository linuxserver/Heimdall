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

use function array_keys;
use function array_merge;
use function array_unique;
use function strcasecmp;

/**
 * @phpstan-type TargetMap array{namespaces: TargetMapPart, traits: TargetMapPart, classes: TargetMapPart, classesThatExtendClass: TargetMapPart, classesThatImplementInterface: TargetMapPart, methods: TargetMapPart, functions: TargetMapPart, reverseLookup: ReverseLookup}
 * @phpstan-type TargetMapPart array<non-empty-string, array<non-empty-string, list<positive-int>>>
 * @phpstan-type ReverseLookup array<non-empty-string, non-empty-string>
 *
 * @immutable
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for phpunit/php-code-coverage
 *
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final readonly class Mapper
{
    /**
     * @var TargetMap
     */
    private array $map;

    /**
     * @param TargetMap $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @return array<non-empty-string, list<positive-int>>
     */
    public function mapTargets(TargetCollection $targets): array
    {
        $result = [];

        foreach ($targets as $target) {
            foreach ($this->mapTarget($target) as $file => $lines) {
                if (!isset($result[$file])) {
                    $result[$file] = $lines;

                    continue;
                }

                $result[$file] = array_unique(array_merge($result[$file], $lines));
            }
        }

        return $result;
    }

    /**
     * @throws InvalidCodeCoverageTargetException
     *
     * @return array<non-empty-string, list<positive-int>>
     */
    public function mapTarget(Target $target): array
    {
        if (isset($this->map[$target->key()][$target->target()])) {
            return $this->map[$target->key()][$target->target()];
        }

        foreach (array_keys($this->map[$target->key()]) as $key) {
            if (strcasecmp($key, $target->target()) === 0) {
                return $this->map[$target->key()][$key];
            }
        }

        throw new InvalidCodeCoverageTargetException($target);
    }

    /**
     * @param non-empty-string $file
     * @param positive-int     $line
     *
     * @return non-empty-string
     */
    public function lookup(string $file, int $line): string
    {
        $key = $file . ':' . $line;

        if (isset($this->map['reverseLookup'][$key])) {
            return $this->map['reverseLookup'][$key];
        }

        return $key;
    }
}
