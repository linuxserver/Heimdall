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

namespace League\CommonMark;

use League\CommonMark\Output\RenderedContentInterface;

/**
 * Interface for a service which converts content from one format (like Markdown) to another (like HTML).
 */
interface ConverterInterface
{
    /**
     * @throws \RuntimeException
     */
    public function convert(string $input): RenderedContentInterface;
}
