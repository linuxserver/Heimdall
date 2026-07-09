<?php declare(strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Comparator;

use function assert;
use function spl_object_id;
use function sprintf;
use Closure;
use ReflectionFunction;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for sebastian/comparator
 *
 * @internal This class is not covered by the backward compatibility promise for sebastian/comparator
 */
final class ClosureComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return $expected instanceof Closure && $actual instanceof Closure;
    }

    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        assert($expected instanceof Closure);
        assert($actual instanceof Closure);

        if ($expected === $actual) {
            return;
        }

        $expectedReflector = new ReflectionFunction($expected);
        $actualReflector   = new ReflectionFunction($actual);

        if ($this->declarationIsEqual($expectedReflector, $actualReflector) &&
            $this->bindingIsEqual($expectedReflector, $actualReflector) &&
            $this->capturedStateIsEqual($expectedReflector, $actualReflector)) {
            return;
        }

        $expectedFilename  = $expectedReflector->getFileName();
        $expectedStartLine = $expectedReflector->getStartLine();
        $actualFilename    = $actualReflector->getFileName();
        $actualStartLine   = $actualReflector->getStartLine();

        assert($expectedFilename !== false);
        assert($expectedStartLine !== false);
        assert($actualFilename !== false);
        assert($actualStartLine !== false);

        throw new ComparisonFailure(
            $expected,
            $actual,
            'Closure Object #' . spl_object_id($expected) . ' ()',
            'Closure Object #' . spl_object_id($actual) . ' ()',
            sprintf(
                'Failed asserting that closure declared at %s:%d is equal to closure declared at %s:%d.',
                $expectedFilename,
                $expectedStartLine,
                $actualFilename,
                $actualStartLine,
            ),
        );
    }

    private function declarationIsEqual(ReflectionFunction $expected, ReflectionFunction $actual): bool
    {
        return $expected->getName() === $actual->getName() &&
            $expected->getFileName() === $actual->getFileName() &&
            $expected->getStartLine() === $actual->getStartLine() &&
            $expected->getEndLine() === $actual->getEndLine();
    }

    private function bindingIsEqual(ReflectionFunction $expected, ReflectionFunction $actual): bool
    {
        if ($expected->getClosureScopeClass()?->getName() !== $actual->getClosureScopeClass()?->getName()) {
            return false;
        }

        return $this->recursivelyEqual(
            $expected->getClosureThis(),
            $actual->getClosureThis(),
        );
    }

    private function capturedStateIsEqual(ReflectionFunction $expected, ReflectionFunction $actual): bool
    {
        return $this->recursivelyEqual(
            $expected->getClosureUsedVariables(),
            $actual->getClosureUsedVariables(),
        );
    }

    private function recursivelyEqual(mixed $expected, mixed $actual): bool
    {
        try {
            $this->factory()->getComparatorFor($expected, $actual)->assertEquals($expected, $actual);
        } catch (ComparisonFailure) {
            return false;
        }

        return true;
    }
}
