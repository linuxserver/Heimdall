<?php declare(strict_types=1);
/*
 * This file is part of sebastian/lines-of-code.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\LinesOfCode;

use function array_merge;
use function array_unique;
use function assert;
use function count;
use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeVisitorAbstract;

final class LineCountingVisitor extends NodeVisitorAbstract
{
    /**
     * @var non-negative-int
     */
    private readonly int $linesOfCode;

    /**
     * @var Comment[]
     */
    private array $comments = [];

    /**
     * @var int[]
     */
    private array $linesWithStatements = [];

    /**
     * @param non-negative-int $linesOfCode
     */
    public function __construct(int $linesOfCode)
    {
        $this->linesOfCode = $linesOfCode;
    }

    public function enterNode(Node $node): null
    {
        $this->comments = array_merge($this->comments, $node->getComments());

        if (!$node instanceof Expr) {
            return null;
        }

        $this->linesWithStatements[] = $node->getStartLine();

        return null;
    }

    public function result(): LinesOfCode
    {
        $commentLines = [];

        foreach ($this->comments() as $comment) {
            for ($line = $comment->getStartLine(); $line <= $comment->getEndLine(); $line++) {
                $commentLines[$line] = true;
            }
        }

        $commentLinesOfCode    = count($commentLines);
        $nonCommentLinesOfCode = $this->linesOfCode - $commentLinesOfCode;
        $logicalLinesOfCode    = count(array_unique($this->linesWithStatements));

        assert($nonCommentLinesOfCode >= 0);

        return new LinesOfCode(
            $this->linesOfCode,
            $commentLinesOfCode,
            $nonCommentLinesOfCode,
            $logicalLinesOfCode,
        );
    }

    /**
     * @return Comment[]
     */
    private function comments(): array
    {
        $comments = [];

        foreach ($this->comments as $comment) {
            $comments[$comment->getStartLine() . '_' . $comment->getStartTokenPos() . '_' . $comment->getEndLine() . '_' . $comment->getEndTokenPos()] = $comment;
        }

        return $comments;
    }
}
