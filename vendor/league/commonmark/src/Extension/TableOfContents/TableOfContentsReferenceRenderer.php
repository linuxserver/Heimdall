<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\TableOfContents;

use League\CommonMark\Extension\TableOfContents\Node\TableOfContentsReference;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class TableOfContentsReferenceRenderer implements NodeRendererInterface
{
    /**
     * @param TableOfContentsReference $node
     *
     * {@inheritDoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string
    {
        TableOfContentsReference::assertInstanceOf($node);

        $cache = $node->getRenderCache();

        $html = $cache->getHtml();
        if ($html === null) {
            $html = $childRenderer->renderNodes([$node->getTableOfContents()]);
            $cache->setHtml($html);
        }

        return $html;
    }
}
