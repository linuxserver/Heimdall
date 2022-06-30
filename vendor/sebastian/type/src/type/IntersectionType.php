<?php declare(strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Type;

use function array_unique;
use function assert;
use function count;
use function implode;
use function sort;

final class IntersectionType extends Type
{
    /**
     * @psalm-var list<Type>
     */
    private $types;

    /**
     * @throws RuntimeException
     */
    public function __construct(Type ...$types)
    {
        $this->ensureMinimumOfTwoTypes(...$types);
        $this->ensureOnlyValidTypes(...$types);
        $this->ensureNoDuplicateTypes(...$types);

        $this->types = $types;
    }

    public function isAssignable(Type $other): bool
    {
        return $other->isObject();
    }

    public function asString(): string
    {
        return $this->name();
    }

    public function name(): string
    {
        $types = [];

        foreach ($this->types as $type) {
            $types[] = $type->name();
        }

        sort($types);

        return implode('&', $types);
    }

    public function allowsNull(): bool
    {
        return false;
    }

    public function isIntersection(): bool
    {
        return true;
    }

    /**
     * @throws RuntimeException
     */
    private function ensureMinimumOfTwoTypes(Type ...$types): void
    {
        if (count($types) < 2) {
            throw new RuntimeException(
                'An intersection type must be composed of at least two types'
            );
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureOnlyValidTypes(Type ...$types): void
    {
        foreach ($types as $type) {
            if (!$type->isObject()) {
                throw new RuntimeException(
                    'An intersection type can only be composed of interfaces and classes'
                );
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    private function ensureNoDuplicateTypes(Type ...$types): void
    {
        $names = [];

        foreach ($types as $type) {
            assert($type instanceof ObjectType);

            $names[] = $type->className()->qualifiedName();
        }

        if (count(array_unique($names)) < count($names)) {
            throw new RuntimeException(
                'An intersection type must not contain duplicate types'
            );
        }
    }
}
