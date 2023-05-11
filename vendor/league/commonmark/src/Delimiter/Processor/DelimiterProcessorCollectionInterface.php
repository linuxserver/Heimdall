<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

interface DelimiterProcessorCollectionInterface
{
    /**
     * Add the given delim processor to the collection
     *
     * @param DelimiterProcessorInterface $processor The delim processor to add
     *
     * @throws \InvalidArgumentException Exception will be thrown if attempting to add multiple processors for the same character
     *
     * @return void
     */
    public function add(DelimiterProcessorInterface $processor);

    /**
     * Returns the delim processor which handles the given character if one exists
     *
     * @param string $char
     *
     * @return DelimiterProcessorInterface|null
     */
    public function getDelimiterProcessor(string $char): ?DelimiterProcessorInterface;

    /**
     * Returns an array of delimiter characters who have associated processors
     *
     * @return string[]
     */
    public function getDelimiterCharacters(): array;
}
