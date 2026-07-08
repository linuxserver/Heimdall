<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use function assert;
use function implode;
use function rtrim;
use function trim;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\UnionType;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SebastianBergmann\Complexity\CyclomaticComplexityCalculatingVisitor;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class CodeUnitFindingVisitor extends NodeVisitorAbstract
{
    /**
     * @var non-empty-string
     */
    private string $file;

    /**
     * @var array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Interface_>
     */
    private array $interfaces = [];

    /**
     * @var array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Class_>
     */
    private array $classes = [];

    /**
     * @var array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Trait_>
     */
    private array $traits = [];

    /**
     * @var array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Function_>
     */
    private array $functions = [];

    /**
     * @param non-empty-string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof Interface_) {
            $this->processInterface($node);
        }

        if ($node instanceof Class_) {
            if ($node->isAnonymous()) {
                return null;
            }

            $this->processClass($node);
        }

        if ($node instanceof Enum_) {
            $this->processClass($node);
        }

        if ($node instanceof Trait_) {
            $this->processTrait($node);
        }

        if (!$node instanceof Function_) {
            return null;
        }

        $this->processFunction($node);

        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($node instanceof Class_ && $node->isAnonymous()) {
            return null;
        }

        if (!$node instanceof Class_ && !$node instanceof Enum_ && !$node instanceof Trait_) {
            return null;
        }

        $traits = [];

        foreach ($node->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                $traits[] = $trait->toString();
            }
        }

        if ($traits === []) {
            return null;
        }

        $this->postProcessClassOrTrait($node, $traits);

        return null;
    }

    /**
     * @return array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Interface_>
     */
    public function interfaces(): array
    {
        return $this->interfaces;
    }

    /**
     * @return array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Class_>
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * @return array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Trait_>
     */
    public function traits(): array
    {
        return $this->traits;
    }

    /**
     * @return array<string, \SebastianBergmann\CodeCoverage\StaticAnalysis\Function_>
     */
    public function functions(): array
    {
        return $this->functions;
    }

    private function cyclomaticComplexity(ClassMethod|Function_ $node): int
    {
        $nodes = $node->getStmts();

        if ($nodes === null) {
            return 0;
        }

        $traverser = new NodeTraverser;

        $cyclomaticComplexityCalculatingVisitor = new CyclomaticComplexityCalculatingVisitor;

        $traverser->addVisitor($cyclomaticComplexityCalculatingVisitor);

        /* @noinspection UnusedFunctionResultInspection */
        $traverser->traverse($nodes);

        return $cyclomaticComplexityCalculatingVisitor->cyclomaticComplexity();
    }

    private function signature(ClassMethod|Function_ $node): string
    {
        $signature  = ($node->returnsByRef() ? '&' : '') . $node->name->toString() . '(';
        $parameters = [];

        foreach ($node->getParams() as $parameter) {
            assert(isset($parameter->var->name));

            $parameterAsString = '';

            if ($parameter->type !== null) {
                $parameterAsString = $this->type($parameter->type) . ' ';
            }

            $parameterAsString .= '$' . $parameter->var->name;

            /* @todo Handle default values */

            $parameters[] = $parameterAsString;
        }

        $signature .= implode(', ', $parameters) . ')';

        $returnType = $node->getReturnType();

        if ($returnType !== null) {
            $signature .= ': ' . $this->type($returnType);
        }

        return $signature;
    }

    private function type(ComplexType|Identifier|Name $type): string
    {
        if ($type instanceof NullableType) {
            return '?' . $type->type;
        }

        if ($type instanceof UnionType) {
            return $this->unionTypeAsString($type);
        }

        if ($type instanceof IntersectionType) {
            return $this->intersectionTypeAsString($type);
        }

        return $type->toString();
    }

    private function visibility(ClassMethod $node): Visibility
    {
        if ($node->isPrivate()) {
            return Visibility::Private;
        }

        if ($node->isProtected()) {
            return Visibility::Protected;
        }

        return Visibility::Public;
    }

    private function processInterface(Interface_ $node): void
    {
        $name             = $node->name->toString();
        $namespacedName   = $node->namespacedName->toString();
        $parentInterfaces = [];

        foreach ($node->extends as $parentInterface) {
            $parentInterfaces[] = $parentInterface->toString();
        }

        $this->interfaces[$namespacedName] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Interface_(
            $name,
            $namespacedName,
            $this->namespace($namespacedName, $name),
            $node->getStartLine(),
            $node->getEndLine(),
            $parentInterfaces,
        );
    }

    private function processClass(Class_|Enum_ $node): void
    {
        $name           = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();
        $parentClass    = null;
        $interfaces     = [];

        if (!$node instanceof Enum_) {
            if ($node->extends instanceof Name) {
                $parentClass = $node->extends->toString();
            }

            foreach ($node->implements as $interface) {
                $interfaces[] = $interface->toString();
            }
        }

        $this->classes[$namespacedName] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Class_(
            $name,
            $namespacedName,
            $this->namespace($namespacedName, $name),
            $this->file,
            $node->getStartLine(),
            $node->getEndLine(),
            $parentClass,
            $interfaces,
            [],
            $this->processMethods($node->getMethods()),
        );
    }

    private function processTrait(Trait_ $node): void
    {
        $name           = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();

        $this->traits[$namespacedName] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Trait_(
            $name,
            $namespacedName,
            $this->namespace($namespacedName, $name),
            $this->file,
            $node->getStartLine(),
            $node->getEndLine(),
            [],
            $this->processMethods($node->getMethods()),
        );
    }

    /**
     * @param list<ClassMethod> $nodes
     *
     * @return array<non-empty-string, Method>
     */
    private function processMethods(array $nodes): array
    {
        $methods = [];

        foreach ($nodes as $node) {
            $methods[$node->name->toString()] = new Method(
                $node->name->toString(),
                $node->getStartLine(),
                $node->getEndLine(),
                $this->signature($node),
                $this->visibility($node),
                $this->cyclomaticComplexity($node),
            );
        }

        return $methods;
    }

    private function processFunction(Function_ $node): void
    {
        assert(isset($node->name));
        assert(isset($node->namespacedName));
        assert($node->namespacedName instanceof Name);

        $name           = $node->name->toString();
        $namespacedName = $node->namespacedName->toString();

        $this->functions[$namespacedName] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Function_(
            $name,
            $namespacedName,
            $this->namespace($namespacedName, $name),
            $node->getStartLine(),
            $node->getEndLine(),
            $this->signature($node),
            $this->cyclomaticComplexity($node),
        );
    }

    private function namespace(string $namespacedName, string $name): string
    {
        return trim(rtrim($namespacedName, $name), '\\');
    }

    private function unionTypeAsString(UnionType $node): string
    {
        $types = [];

        foreach ($node->types as $type) {
            if ($type instanceof IntersectionType) {
                $types[] = '(' . $this->intersectionTypeAsString($type) . ')';

                continue;
            }

            $types[] = $this->typeAsString($type);
        }

        return implode('|', $types);
    }

    private function intersectionTypeAsString(IntersectionType $node): string
    {
        $types = [];

        foreach ($node->types as $type) {
            $types[] = $this->typeAsString($type);
        }

        return implode('&', $types);
    }

    private function typeAsString(Identifier|Name $node): string
    {
        if ($node instanceof Name) {
            return $node->toCodeString();
        }

        return $node->toString();
    }

    /**
     * @param list<non-empty-string> $traits
     */
    private function postProcessClassOrTrait(Class_|Enum_|Trait_ $node, array $traits): void
    {
        $name = $node->namespacedName->toString();

        if ($node instanceof Class_ || $node instanceof Enum_) {
            assert(isset($this->classes[$name]));

            $this->classes[$name] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Class_(
                $this->classes[$name]->name(),
                $this->classes[$name]->namespacedName(),
                $this->classes[$name]->namespace(),
                $this->classes[$name]->file(),
                $this->classes[$name]->startLine(),
                $this->classes[$name]->endLine(),
                $this->classes[$name]->parentClass(),
                $this->classes[$name]->interfaces(),
                $traits,
                $this->classes[$name]->methods(),
            );

            return;
        }

        assert(isset($this->traits[$name]));

        $this->traits[$name] = new \SebastianBergmann\CodeCoverage\StaticAnalysis\Trait_(
            $this->traits[$name]->name(),
            $this->traits[$name]->namespacedName(),
            $this->traits[$name]->namespace(),
            $this->traits[$name]->file(),
            $this->traits[$name]->startLine(),
            $this->traits[$name]->endLine(),
            $traits,
            $this->traits[$name]->methods(),
        );
    }
}
