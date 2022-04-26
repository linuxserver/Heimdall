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

use function assert;
use ReflectionFunctionAbstract;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class ReflectionMapper
{
    public function fromReturnType(ReflectionFunctionAbstract $functionOrMethod): Type
    {
        if (!$this->hasReturnType($functionOrMethod)) {
            return new UnknownType;
        }

        $returnType = $this->returnType($functionOrMethod);

        assert($returnType instanceof ReflectionNamedType || $returnType instanceof ReflectionUnionType || $returnType instanceof ReflectionIntersectionType);

        if ($returnType instanceof ReflectionNamedType) {
            if ($functionOrMethod instanceof ReflectionMethod && $returnType->getName() === 'self') {
                return ObjectType::fromName(
                    $functionOrMethod->getDeclaringClass()->getName(),
                    $returnType->allowsNull()
                );
            }

            if ($functionOrMethod instanceof ReflectionMethod && $returnType->getName() === 'static') {
                return new StaticType(
                    TypeName::fromReflection($functionOrMethod->getDeclaringClass()),
                    $returnType->allowsNull()
                );
            }

            if ($returnType->getName() === 'mixed') {
                return new MixedType;
            }

            if ($functionOrMethod instanceof ReflectionMethod && $returnType->getName() === 'parent') {
                return ObjectType::fromName(
                    $functionOrMethod->getDeclaringClass()->getParentClass()->getName(),
                    $returnType->allowsNull()
                );
            }

            return Type::fromName(
                $returnType->getName(),
                $returnType->allowsNull()
            );
        }

        assert($returnType instanceof ReflectionUnionType || $returnType instanceof ReflectionIntersectionType);

        $types = [];

        foreach ($returnType->getTypes() as $type) {
            if ($functionOrMethod instanceof ReflectionMethod && $type->getName() === 'self') {
                $types[] = ObjectType::fromName(
                    $functionOrMethod->getDeclaringClass()->getName(),
                    false
                );
            } else {
                $types[] = Type::fromName($type->getName(), false);
            }
        }

        if ($returnType instanceof ReflectionUnionType) {
            return new UnionType(...$types);
        }

        return new IntersectionType(...$types);
    }

    private function hasReturnType(ReflectionFunctionAbstract $functionOrMethod): bool
    {
        if ($functionOrMethod->hasReturnType()) {
            return true;
        }

        if (!method_exists($functionOrMethod, 'hasTentativeReturnType')) {
            return false;
        }

        return $functionOrMethod->hasTentativeReturnType();
    }

    private function returnType(ReflectionFunctionAbstract $functionOrMethod): ?ReflectionType
    {
        if ($functionOrMethod->hasReturnType()) {
            return $functionOrMethod->getReturnType();
        }

        if (!method_exists($functionOrMethod, 'getTentativeReturnType')) {
            return null;
        }

        return $functionOrMethod->getTentativeReturnType();
    }
}
