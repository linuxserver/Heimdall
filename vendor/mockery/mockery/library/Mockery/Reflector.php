<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2017 Dave Marshall https://github.com/davedevelopment
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery;

/**
 * @internal
 */
class Reflector
{
    /**
     * Determine if the parameter is typed as an array.
     *
     * @param \ReflectionParameter $param
     *
     * @return bool
     */
    public static function isArray(\ReflectionParameter $param)
    {
        if (\PHP_VERSION_ID < 70100) {
            return $param->isArray();
        }

        $type = $param->getType();

        return $type instanceof \ReflectionNamedType ? $type->getName() === 'array' : false;
    }

    /**
     * Compute the string representation for the paramater type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getTypeHint(\ReflectionParameter $param, $withoutNullable = false)
    {
        // returns false if we are running PHP 7+
        $typeHint = self::getLegacyTypeHint($param);

        if ($typeHint !== false) {
            return $typeHint;
        }

        if (!$param->hasType()) {
            return null;
        }

        $type = $param->getType();
        $declaringClass = $param->getDeclaringClass();
        $typeHint = self::typeToString($type, $declaringClass);

        // PHP 7.1+ supports nullable types via a leading question mark
        return (!$withoutNullable && \PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the return type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getReturnType(\ReflectionMethod $method, $withoutNullable = false)
    {
        // Strip all return types for HHVM and skip PHP 5.
        if (method_exists($method, 'getReturnTypeText') || \PHP_VERSION_ID < 70000) {
            return null;
        }

        $type = $method->getReturnType();

        if (is_null($type) && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (is_null($type)) {
            return null;
        }

        $typeHint = self::typeToString($type, $method->getDeclaringClass());

        // PHP 7.1+ supports nullable types via a leading question mark
        return (!$withoutNullable && \PHP_VERSION_ID >= 70100 && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the simplest return type.
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null
     */
    public static function getSimplestReturnType(\ReflectionMethod $method)
    {
        // Strip all return types for HHVM and skip PHP 5.
        if (method_exists($method, 'getReturnTypeText') || \PHP_VERSION_ID < 70000) {
            return null;
        }

        $type = $method->getReturnType();

        if (is_null($type) && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (is_null($type) || $type->allowsNull()) {
            return null;
        }

        $typeInformation = self::getTypeInformation($type, $method->getDeclaringClass());

        // return the first primitive type hint
        foreach ($typeInformation as $info) {
            if ($info['isPrimitive']) {
                return $info['typeHint'];
            }
        }

        // if no primitive type, return the first type
        foreach ($typeInformation as $info) {
            return $info['typeHint'];
        }

        return null;
    }

    /**
     * Compute the legacy type hint.
     *
     * We return:
     *   - string: the legacy type hint
     *   - null: if there is no legacy type hint
     *   - false: if we must check for PHP 7+ typing
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null|false
     */
    private static function getLegacyTypeHint(\ReflectionParameter $param)
    {
        // Handle HHVM typing
        if (\method_exists($param, 'getTypehintText')) {
            if ($param->isArray()) {
                return 'array';
            }

            if ($param->isCallable()) {
                return 'callable';
            }

            $typeHint = $param->getTypehintText();

            // throw away HHVM scalar types
            if (\in_array($typeHint, array('int', 'integer', 'float', 'string', 'bool', 'boolean'), true)) {
                return null;
            }

            return sprintf('\\%s', $typeHint);
        }

        // Handle PHP 5 typing
        if (\PHP_VERSION_ID < 70000) {
            if ($param->isArray()) {
                return 'array';
            }

            if ($param->isCallable()) {
                return 'callable';
            }

            $typeHint = self::getLegacyClassName($param);

            return $typeHint === null ? null : sprintf('\\%s', $typeHint);
        }

        return false;
    }

    /**
     * Compute the class name using legacy APIs, if possible.
     *
     * This method MUST only be called on PHP 5.
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null
     */
    private static function getLegacyClassName(\ReflectionParameter $param)
    {
        try {
            $class = $param->getClass();

            $typeHint = $class === null ? null : $class->getName();
        } catch (\ReflectionException $e) {
            $typeHint = null;
        }

        if ($typeHint === null) {
            if (preg_match('/^Parameter #[0-9]+ \[ \<(required|optional)\> (?<typehint>\S+ )?.*\$' . $param->getName() . ' .*\]$/', (string) $param, $typehintMatch)) {
                if (!empty($typehintMatch['typehint']) && $typehintMatch['typehint']) {
                    $typeHint = $typehintMatch['typehint'];
                }
            }
        }

        return $typeHint;
    }

    /**
     * Get the string representation of the given type.
     *
     * This method MUST only be called on PHP 7+.
     *
     * @param \ReflectionType  $type
     * @param \ReflectionClass $declaringClass
     *
     * @return string|null
     */
    private static function typeToString(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
        return \implode('|', \array_map(function (array $typeInformation) {
            return $typeInformation['typeHint'];
        }, self::getTypeInformation($type, $declaringClass)));
    }

    /**
     * Get the string representation of the given type.
     *
     * This method MUST only be called on PHP 7+.
     *
     * @param \ReflectionType  $type
     * @param \ReflectionClass $declaringClass
     *
     * @return list<array{typeHint: string, isPrimitive: bool}>
     */
    private static function getTypeInformation(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
        // PHP 8 union types can be recursively processed
        if ($type instanceof \ReflectionUnionType) {
            $types = [];

            foreach ($type->getTypes() as $innterType) {
                foreach (self::getTypeInformation($innterType, $declaringClass) as $info) {
                    if ($info['typeHint'] === 'null' && $info['isPrimitive']) {
                        continue;
                    }

                    $types[] = $info;
                }
            }

            return $types;
        }

        // PHP 7.0 doesn't have named types, but 7.1+ does
        $typeHint = $type instanceof \ReflectionNamedType ? $type->getName() : (string) $type;

        // builtins can be returned as is
        if ($type->isBuiltin()) {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => in_array($typeHint, ['array', 'bool', 'int', 'float', 'null', 'object', 'string']),
                ],
            ];
        }

        // 'static' can be returned as is
        if ($typeHint === 'static') {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => false,
                ],
            ];
        }

        // 'self' needs to be resolved to the name of the declaring class
        if ($typeHint === 'self') {
            $typeHint = $declaringClass->getName();
        }

        // 'parent' needs to be resolved to the name of the parent class
        if ($typeHint === 'parent') {
            $typeHint = $declaringClass->getParentClass()->getName();
        }

        // class names need prefixing with a slash
        return [
            [
                'typeHint' => sprintf('\\%s', $typeHint),
                'isPrimitive' => false,
            ],
        ];
    }

    /**
     * Format the given type as a nullable type.
     *
     * This method MUST only be called on PHP 7.1+.
     *
     * @param string $typeHint
     *
     * @return string
     */
    private static function formatNullableType($typeHint)
    {
        if (\PHP_VERSION_ID < 80000) {
            return sprintf('?%s', $typeHint);
        }

        return $typeHint === 'mixed' ? 'mixed' : sprintf('%s|null', $typeHint);
    }
}
