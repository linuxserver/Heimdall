<?php

namespace Illuminate\Support;

use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class Reflector
{
    /**
     * This is a PHP 7.4 compatible implementation of is_callable.
     *
     * @param  mixed  $var
     * @param  bool  $syntaxOnly
     * @return bool
     */
    public static function isCallable($var, $syntaxOnly = false)
    {
        if (! is_array($var)) {
            return is_callable($var, $syntaxOnly);
        }

        if ((! isset($var[0]) || ! isset($var[1])) ||
            ! is_string($var[1] ?? null)) {
            return false;
        }

        if ($syntaxOnly &&
            (is_string($var[0]) || is_object($var[0])) &&
            is_string($var[1])) {
            return true;
        }

        $class = is_object($var[0]) ? get_class($var[0]) : $var[0];

        $method = $var[1];

        if (! class_exists($class)) {
            return false;
        }

        if (method_exists($class, $method)) {
            return (new ReflectionMethod($class, $method))->isPublic();
        }

        if (is_object($var[0]) && method_exists($class, '__call')) {
            return (new ReflectionMethod($class, '__call'))->isPublic();
        }

        if (! is_object($var[0]) && method_exists($class, '__callStatic')) {
            return (new ReflectionMethod($class, '__callStatic'))->isPublic();
        }

        return false;
    }

    /**
     * Get the class name of the given parameter's type, if possible.
     *
     * @param  \ReflectionParameter  $parameter
     * @return string|null
     */
    public static function getParameterClassName($parameter)
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType || $type->isBuiltin()) {
            return;
        }

        $name = $type->getName();

        if (! is_null($class = $parameter->getDeclaringClass())) {
            if ($name === 'self') {
                return $class->getName();
            }

            if ($name === 'parent' && $parent = $class->getParentClass()) {
                return $parent->getName();
            }
        }

        return $name;
    }

    /**
     * Determine if the parameter's type is a subclass of the given type.
     *
     * @param  \ReflectionParameter  $parameter
     * @param  string  $className
     * @return bool
     */
    public static function isParameterSubclassOf($parameter, $className)
    {
        $paramClassName = static::getParameterClassName($parameter);

        return ($paramClassName && class_exists($paramClassName))
            ? (new ReflectionClass($paramClassName))->isSubclassOf($className)
            : false;
    }
}
