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

use function array_pop;
use function count;
use PhpParser\Node;
use PhpParser\NodeVisitor;

/**
 * Visitor that connects a child node to its parent node optimized for Attribute nodes.
 *
 * On the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>.
 *
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class AttributeParentConnectingVisitor implements NodeVisitor
{
    /**
     * @var Node[]
     */
    private array $stack = [];

    public function beforeTraverse(array $nodes): null
    {
        $this->stack = [];

        return null;
    }

    public function enterNode(Node $node): null
    {
        if ($this->stack !== [] &&
            ($node instanceof Node\Attribute || $node instanceof Node\AttributeGroup)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }

        $this->stack[] = $node;

        return null;
    }

    public function leaveNode(Node $node): null
    {
        array_pop($this->stack);

        return null;
    }

    public function afterTraverse(array $nodes): null
    {
        return null;
    }
}
