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

use function array_merge;
use function range;
use function strpos;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class IgnoredLinesFindingVisitor extends NodeVisitorAbstract
{
    /**
     * @psalm-var list<int>
     */
    private $ignoredLines = [];

    /**
     * @var bool
     */
    private $useAnnotationsForIgnoringCode;

    /**
     * @var bool
     */
    private $ignoreDeprecated;

    public function __construct(bool $useAnnotationsForIgnoringCode, bool $ignoreDeprecated)
    {
        $this->useAnnotationsForIgnoringCode = $useAnnotationsForIgnoringCode;
        $this->ignoreDeprecated              = $ignoreDeprecated;
    }

    public function enterNode(Node $node): void
    {
        if (!$node instanceof Class_ &&
            !$node instanceof Trait_ &&
            !$node instanceof Interface_ &&
            !$node instanceof ClassMethod &&
            !$node instanceof Function_) {
            return;
        }

        if ($node instanceof Class_ && $node->isAnonymous()) {
            return;
        }

        // Workaround for https://bugs.xdebug.org/view.php?id=1798
        if ($node instanceof Class_ ||
            $node instanceof Trait_ ||
            $node instanceof Interface_) {
            $this->ignoredLines[] = $node->getStartLine();
        }

        if (!$this->useAnnotationsForIgnoringCode) {
            return;
        }

        if ($node instanceof Interface_) {
            return;
        }

        $docComment = $node->getDocComment();

        if ($docComment === null) {
            return;
        }

        if (strpos($docComment->getText(), '@codeCoverageIgnore') !== false) {
            $this->ignoredLines = array_merge(
                $this->ignoredLines,
                range($node->getStartLine(), $node->getEndLine())
            );
        }

        if ($this->ignoreDeprecated && strpos($docComment->getText(), '@deprecated') !== false) {
            $this->ignoredLines = array_merge(
                $this->ignoredLines,
                range($node->getStartLine(), $node->getEndLine())
            );
        }
    }

    /**
     * @psalm-return list<int>
     */
    public function ignoredLines(): array
    {
        return $this->ignoredLines;
    }
}
