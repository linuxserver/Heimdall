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

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Match_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\MatchArm;
use PhpParser\Node\Scalar\Encapsed;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\Do_;
use PhpParser\Node\Stmt\Echo_;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Finally_;
use PhpParser\Node\Stmt\For_;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Stmt\Goto_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\Throw_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\Node\Stmt\Unset_;
use PhpParser\Node\Stmt\While_;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ExecutableLinesFindingVisitor extends NodeVisitorAbstract
{
    /**
     * @psalm-var array<int, int>
     */
    private $executableLines = [];

    /**
     * @psalm-var array<int, int>
     */
    private $propertyLines = [];

    /**
     * @psalm-var array<int, Return_>
     */
    private $returns = [];

    public function enterNode(Node $node): void
    {
        $this->savePropertyLines($node);

        if (!$this->isExecutable($node)) {
            return;
        }

        foreach ($this->getLines($node) as $line) {
            if (isset($this->propertyLines[$line])) {
                return;
            }

            $this->executableLines[$line] = $line;
        }
    }

    /**
     * @psalm-return array<int, int>
     */
    public function executableLines(): array
    {
        $this->computeReturns();

        sort($this->executableLines);

        return $this->executableLines;
    }

    private function savePropertyLines(Node $node): void
    {
        if (!$node instanceof Property && !$node instanceof Node\Stmt\ClassConst) {
            return;
        }

        foreach (range($node->getStartLine(), $node->getEndLine()) as $index) {
            $this->propertyLines[$index] = $index;
        }
    }

    private function computeReturns(): void
    {
        foreach ($this->returns as $return) {
            foreach (range($return->getStartLine(), $return->getEndLine()) as $loc) {
                if (isset($this->executableLines[$loc])) {
                    continue 2;
                }
            }

            $line = $return->getEndLine();

            if ($return->expr !== null) {
                $line = $return->expr->getStartLine();
            }

            $this->executableLines[$line] = $line;
        }
    }

    /**
     * @return int[]
     */
    private function getLines(Node $node): array
    {
        if ($node instanceof BinaryOp) {
            if (($node->left instanceof Node\Scalar ||
                $node->left instanceof Node\Expr\ConstFetch) &&
                ($node->right instanceof Node\Scalar ||
                $node->right instanceof Node\Expr\ConstFetch)) {
                return [$node->right->getStartLine()];
            }

            return [];
        }

        if ($node instanceof Cast ||
            $node instanceof PropertyFetch ||
            $node instanceof NullsafePropertyFetch ||
            $node instanceof StaticPropertyFetch) {
            return [$node->getEndLine()];
        }

        if ($node instanceof ArrayDimFetch) {
            if (null === $node->dim) {
                return [];
            }

            return [$node->dim->getStartLine()];
        }

        if ($node instanceof Array_) {
            $startLine = $node->getStartLine();

            if (isset($this->executableLines[$startLine])) {
                return [];
            }

            if ([] === $node->items) {
                return [$node->getEndLine()];
            }

            if ($node->items[0] instanceof ArrayItem) {
                return [$node->items[0]->getStartLine()];
            }
        }

        if ($node instanceof ClassMethod) {
            if ($node->name->name !== '__construct') {
                return [];
            }

            $existsAPromotedProperty = false;

            foreach ($node->getParams() as $param) {
                if (0 !== ($param->flags & Class_::VISIBILITY_MODIFIER_MASK)) {
                    $existsAPromotedProperty = true;

                    break;
                }
            }

            if ($existsAPromotedProperty) {
                // Only the line with `function` keyword should be listed here
                // but `nikic/php-parser` doesn't provide a way to fetch it
                return range($node->getStartLine(), $node->name->getEndLine());
            }

            return [];
        }

        if ($node instanceof MethodCall) {
            return [$node->name->getStartLine()];
        }

        if ($node instanceof Ternary) {
            $lines = [$node->cond->getStartLine()];

            if (null !== $node->if) {
                $lines[] = $node->if->getStartLine();
            }

            $lines[] = $node->else->getStartLine();

            return $lines;
        }

        if ($node instanceof Match_) {
            return [$node->cond->getStartLine()];
        }

        if ($node instanceof MatchArm) {
            return [$node->body->getStartLine()];
        }

        if ($node instanceof Expression && (
            $node->expr instanceof Cast ||
            $node->expr instanceof Match_ ||
            $node->expr instanceof MethodCall
        )) {
            return [];
        }

        if ($node instanceof Return_) {
            $this->returns[] = $node;

            return [];
        }

        return [$node->getStartLine()];
    }

    private function isExecutable(Node $node): bool
    {
        return $node instanceof Assign ||
               $node instanceof ArrayDimFetch ||
               $node instanceof Array_ ||
               $node instanceof BinaryOp ||
               $node instanceof Break_ ||
               $node instanceof CallLike ||
               $node instanceof Case_ ||
               $node instanceof Cast ||
               $node instanceof Catch_ ||
               $node instanceof ClassMethod ||
               $node instanceof Closure ||
               $node instanceof Continue_ ||
               $node instanceof Do_ ||
               $node instanceof Echo_ ||
               $node instanceof ElseIf_ ||
               $node instanceof Else_ ||
               $node instanceof Encapsed ||
               $node instanceof Expression ||
               $node instanceof Finally_ ||
               $node instanceof For_ ||
               $node instanceof Foreach_ ||
               $node instanceof Goto_ ||
               $node instanceof If_ ||
               $node instanceof Match_ ||
               $node instanceof MatchArm ||
               $node instanceof MethodCall ||
               $node instanceof NullsafePropertyFetch ||
               $node instanceof PropertyFetch ||
               $node instanceof Return_ ||
               $node instanceof StaticPropertyFetch ||
               $node instanceof Switch_ ||
               $node instanceof Ternary ||
               $node instanceof Throw_ ||
               $node instanceof TryCatch ||
               $node instanceof Unset_ ||
               $node instanceof While_;
    }
}
