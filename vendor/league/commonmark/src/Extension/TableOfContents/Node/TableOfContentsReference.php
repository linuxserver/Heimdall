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

namespace League\CommonMark\Extension\TableOfContents\Node;

use League\CommonMark\Extension\TableOfContents\TableOfContentsRenderCache;
use League\CommonMark\Node\Block\AbstractBlock;

/**
 * Lightweight stand-in for a placeholder which references the shared table of contents instead of holding a full copy
 */
final class TableOfContentsReference extends AbstractBlock
{
    /** @psalm-readonly */
    private TableOfContents $tableOfContents;

    /** @psalm-readonly */
    private TableOfContentsRenderCache $renderCache;

    public function __construct(TableOfContents $tableOfContents, TableOfContentsRenderCache $renderCache)
    {
        parent::__construct();

        $this->tableOfContents = $tableOfContents;
        $this->renderCache     = $renderCache;
    }

    public function getTableOfContents(): TableOfContents
    {
        return $this->tableOfContents;
    }

    public function getRenderCache(): TableOfContentsRenderCache
    {
        return $this->renderCache;
    }
}
