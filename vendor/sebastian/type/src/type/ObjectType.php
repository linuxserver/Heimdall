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

use function class_exists;
use function interface_exists;
use function is_subclass_of;
use function strcasecmp;
use ReflectionClass;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for this library
 */
final class ObjectType extends Type
{
    private TypeName $className;
    private bool $allowsNull;

    public function __construct(TypeName $className, bool $allowsNull)
    {
        $this->className  = $className;
        $this->allowsNull = $allowsNull;
    }

    public function isAssignable(Type $other): bool
    {
        if ($this->allowsNull && $other instanceof NullType) {
            return true;
        }

        if ($other instanceof self) {
            $thisName  = $this->canonicalClassName($this->className->qualifiedName());
            $otherName = $this->canonicalClassName($other->className->qualifiedName());

            if (strcasecmp($thisName, $otherName) === 0) {
                return true;
            }

            if (is_subclass_of($otherName, $thisName, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return non-empty-string
     */
    public function name(): string
    {
        return $this->className->qualifiedName();
    }

    public function allowsNull(): bool
    {
        return $this->allowsNull;
    }

    public function className(): TypeName
    {
        return $this->className;
    }

    public function isObject(): bool
    {
        return true;
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    private function canonicalClassName(string $name): string
    {
        if (class_exists($name) || interface_exists($name)) {
            return (new ReflectionClass($name))->getName();
        }

        return $name;
    }
}
