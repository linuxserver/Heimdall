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
abstract class Target
{
    /**
     * @param non-empty-string $namespace
     */
    public static function forNamespace(string $namespace): Namespace_
    {
        return new Namespace_($namespace);
    }

    /**
     * @param class-string $className
     */
    public static function forClass(string $className): Class_
    {
        return new Class_($className);
    }

    /**
     * @param class-string     $className
     * @param non-empty-string $methodName
     */
    public static function forMethod(string $className, string $methodName): Method
    {
        return new Method($className, $methodName);
    }

    /**
     * @param class-string $interfaceName
     */
    public static function forClassesThatImplementInterface(string $interfaceName): ClassesThatImplementInterface
    {
        return new ClassesThatImplementInterface($interfaceName);
    }

    /**
     * @param class-string $className
     */
    public static function forClassesThatExtendClass(string $className): ClassesThatExtendClass
    {
        return new ClassesThatExtendClass($className);
    }

    /**
     * @param non-empty-string $functionName
     */
    public static function forFunction(string $functionName): Function_
    {
        return new Function_($functionName);
    }

    /**
     * @param trait-string $traitName
     */
    public static function forTrait(string $traitName): Trait_
    {
        return new Trait_($traitName);
    }

    public function isNamespace(): bool
    {
        return false;
    }

    public function isClass(): bool
    {
        return false;
    }

    public function isMethod(): bool
    {
        return false;
    }

    public function isClassesThatImplementInterface(): bool
    {
        return false;
    }

    public function isClassesThatExtendClass(): bool
    {
        return false;
    }

    public function isFunction(): bool
    {
        return false;
    }

    public function isTrait(): bool
    {
        return false;
    }

    /**
     * @return non-empty-string
     */
    abstract public function key(): string;

    /**
     * @return non-empty-string
     */
    abstract public function target(): string;

    /**
     * @return non-empty-string
     */
    abstract public function description(): string;
}
