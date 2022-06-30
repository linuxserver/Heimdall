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

use const PHP_VERSION;
use function get_class;
use function gettype;
use function strtolower;
use function version_compare;

abstract class Type
{
    public static function fromValue($value, bool $allowsNull): self
    {
        if ($value === false) {
            return new FalseType;
        }

        $typeName = gettype($value);

        if ($typeName === 'object') {
            return new ObjectType(TypeName::fromQualifiedName(get_class($value)), $allowsNull);
        }

        $type = self::fromName($typeName, $allowsNull);

        if ($type instanceof SimpleType) {
            $type = new SimpleType($typeName, $allowsNull, $value);
        }

        return $type;
    }

    public static function fromName(string $typeName, bool $allowsNull): self
    {
        if (version_compare(PHP_VERSION, '8.1.0-dev', '>=') && strtolower($typeName) === 'never') {
            return new NeverType;
        }

        switch (strtolower($typeName)) {
            case 'callable':
                return new CallableType($allowsNull);

            case 'false':
                return new FalseType;

            case 'iterable':
                return new IterableType($allowsNull);

            case 'null':
                return new NullType;

            case 'object':
                return new GenericObjectType($allowsNull);

            case 'unknown type':
                return new UnknownType;

            case 'void':
                return new VoidType;

            case 'array':
            case 'bool':
            case 'boolean':
            case 'double':
            case 'float':
            case 'int':
            case 'integer':
            case 'real':
            case 'resource':
            case 'resource (closed)':
            case 'string':
                return new SimpleType($typeName, $allowsNull);

            default:
                return new ObjectType(TypeName::fromQualifiedName($typeName), $allowsNull);
        }
    }

    public function asString(): string
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }

    public function isCallable(): bool
    {
        return false;
    }

    public function isFalse(): bool
    {
        return false;
    }

    public function isGenericObject(): bool
    {
        return false;
    }

    public function isIntersection(): bool
    {
        return false;
    }

    public function isIterable(): bool
    {
        return false;
    }

    public function isMixed(): bool
    {
        return false;
    }

    public function isNever(): bool
    {
        return false;
    }

    public function isNull(): bool
    {
        return false;
    }

    public function isObject(): bool
    {
        return false;
    }

    public function isSimple(): bool
    {
        return false;
    }

    public function isStatic(): bool
    {
        return false;
    }

    public function isUnion(): bool
    {
        return false;
    }

    public function isUnknown(): bool
    {
        return false;
    }

    public function isVoid(): bool
    {
        return false;
    }

    abstract public function isAssignable(self $other): bool;

    abstract public function name(): string;

    abstract public function allowsNull(): bool;
}
