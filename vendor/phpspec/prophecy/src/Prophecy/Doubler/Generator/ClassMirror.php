<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Prophecy\Doubler\Generator;

use Prophecy\Doubler\Generator\Node\ArgumentTypeNode;
use Prophecy\Doubler\Generator\Node\ReturnTypeNode;
use Prophecy\Exception\InvalidArgumentException;
use Prophecy\Exception\Doubler\ClassMirrorException;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

/**
 * Class mirror.
 * Core doubler class. Mirrors specific class and/or interfaces into class node tree.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ClassMirror
{
    private static $reflectableMethods = array(
        '__construct',
        '__destruct',
        '__sleep',
        '__wakeup',
        '__toString',
        '__call',
        '__invoke'
    );

    /**
     * Reflects provided arguments into class node.
     *
     * @param ReflectionClass|null $class
     * @param ReflectionClass[] $interfaces
     *
     * @return Node\ClassNode
     *
     */
    public function reflect(?ReflectionClass $class, array $interfaces)
    {
        $node = new Node\ClassNode;

        if (null !== $class) {
            if (true === $class->isInterface()) {
                throw new InvalidArgumentException(sprintf(
                    "Could not reflect %s as a class, because it\n".
                    "is interface - use the second argument instead.",
                    $class->getName()
                ));
            }

            $this->reflectClassToNode($class, $node);
        }

        foreach ($interfaces as $interface) {
            if (!$interface instanceof ReflectionClass) {
                throw new InvalidArgumentException(sprintf(
                    "[ReflectionClass \$interface1 [, ReflectionClass \$interface2]] array expected as\n".
                    "a second argument to `ClassMirror::reflect(...)`, but got %s.",
                    is_object($interface) ? get_class($interface).' class' : gettype($interface)
                ));
            }
            if (false === $interface->isInterface()) {
                throw new InvalidArgumentException(sprintf(
                    "Could not reflect %s as an interface, because it\n".
                    "is class - use the first argument instead.",
                    $interface->getName()
                ));
            }

            $this->reflectInterfaceToNode($interface, $node);
        }

        $node->addInterface('Prophecy\Doubler\Generator\ReflectionInterface');

        return $node;
    }

    private function reflectClassToNode(ReflectionClass $class, Node\ClassNode $node)
    {
        if (true === $class->isFinal()) {
            throw new ClassMirrorException(sprintf(
                'Could not reflect class %s as it is marked final.', $class->getName()
            ), $class);
        }

        $node->setParentClass($class->getName());

        foreach ($class->getMethods(ReflectionMethod::IS_ABSTRACT) as $method) {
            if (false === $method->isProtected()) {
                continue;
            }

            $this->reflectMethodToNode($method, $node);
        }

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (0 === strpos($method->getName(), '_')
                && !in_array($method->getName(), self::$reflectableMethods)) {
                continue;
            }

            if (true === $method->isFinal()) {
                $node->addUnextendableMethod($method->getName());
                continue;
            }

            $this->reflectMethodToNode($method, $node);
        }
    }

    private function reflectInterfaceToNode(ReflectionClass $interface, Node\ClassNode $node)
    {
        $node->addInterface($interface->getName());

        foreach ($interface->getMethods() as $method) {
            $this->reflectMethodToNode($method, $node);
        }
    }

    private function reflectMethodToNode(ReflectionMethod $method, Node\ClassNode $classNode)
    {
        $node = new Node\MethodNode($method->getName());

        if (true === $method->isProtected()) {
            $node->setVisibility('protected');
        }

        if (true === $method->isStatic()) {
            $node->setStatic();
        }

        if (true === $method->returnsReference()) {
            $node->setReturnsReference();
        }

        if ($method->hasReturnType()) {
            $returnTypes = $this->getTypeHints($method->getReturnType(), $method->getDeclaringClass(), $method->getReturnType()->allowsNull());
            $node->setReturnTypeNode(new ReturnTypeNode(...$returnTypes));
        }
        elseif (method_exists($method, 'hasTentativeReturnType') && $method->hasTentativeReturnType()) {
            $returnTypes = $this->getTypeHints($method->getTentativeReturnType(), $method->getDeclaringClass(), $method->getTentativeReturnType()->allowsNull());
            $node->setReturnTypeNode(new ReturnTypeNode(...$returnTypes));
        }

        if (is_array($params = $method->getParameters()) && count($params)) {
            foreach ($params as $param) {
                $this->reflectArgumentToNode($param, $node);
            }
        }

        $classNode->addMethod($node);
    }

    private function reflectArgumentToNode(ReflectionParameter $parameter, Node\MethodNode $methodNode)
    {
        $name = $parameter->getName() == '...' ? '__dot_dot_dot__' : $parameter->getName();
        $node = new Node\ArgumentNode($name);

        $typeHints = $this->getTypeHints($parameter->getType(), $parameter->getDeclaringClass(), $parameter->allowsNull());

        $node->setTypeNode(new ArgumentTypeNode(...$typeHints));

        if ($parameter->isVariadic()) {
            $node->setAsVariadic();
        }

        if ($this->hasDefaultValue($parameter)) {
            $node->setDefault($this->getDefaultValue($parameter));
        }

        if ($parameter->isPassedByReference()) {
            $node->setAsPassedByReference();
        }


        $methodNode->addArgument($node);
    }

    private function hasDefaultValue(ReflectionParameter $parameter)
    {
        if ($parameter->isVariadic()) {
            return false;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return true;
        }

        return $parameter->isOptional() || ($parameter->allowsNull() && $parameter->getType() && \PHP_VERSION_ID < 80100);
    }

    private function getDefaultValue(ReflectionParameter $parameter)
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return null;
        }

        return $parameter->getDefaultValue();
    }

    private function getTypeHints(?ReflectionType $type, ?ReflectionClass $class, bool $allowsNull) : array
    {
        $types = [];

        if ($type instanceof ReflectionNamedType) {
            $types = [$type->getName()];

        }
        elseif ($type instanceof ReflectionUnionType) {
            $types = $type->getTypes();
        }
        elseif ($type instanceof ReflectionIntersectionType) {
            throw new ClassMirrorException('Doubling intersection types is not supported', $class);
        }
        elseif(is_object($type)) {
            throw new ClassMirrorException('Unknown reflection type ' . get_class($type), $class);
        }

        $types = array_map(
            function(string $type) use ($class) {
                if ($type === 'self') {
                    return $class->getName();
                }
                if ($type === 'parent') {
                    return $class->getParentClass()->getName();
                }

                return $type;
            },
            $types
        );

        if ($types && $types != ['mixed'] && $allowsNull) {
            $types[] = 'null';
        }

        return $types;
    }
}
