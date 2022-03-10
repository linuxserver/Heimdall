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

use League\CommonMark\Delimiter\DelimiterInterface;
use League\CommonMark\Inline\Element\AbstractStringContainer;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;

final class EmphasisDelimiterProcessor implements DelimiterProcessorInterface, ConfigurationAwareInterface
{
    /** @var string */
    private $char;

    /** @var ConfigurationInterface|null */
    private $config;

    /**
     * @param string $char The emphasis character to use (typically '*' or '_')
     */
    public function __construct(string $char)
    {
        $this->char = $char;
    }

    public function getOpeningCharacter(): string
    {
        return $this->char;
    }

    public function getClosingCharacter(): string
    {
        return $this->char;
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getDelimiterUse(DelimiterInterface $opener, DelimiterInterface $closer): int
    {
        // "Multiple of 3" rule for internal delimiter runs
        if (($opener->canClose() || $closer->canOpen()) && $closer->getOriginalLength() % 3 !== 0 && ($opener->getOriginalLength() + $closer->getOriginalLength()) % 3 === 0) {
            return 0;
        }

        // Calculate actual number of delimiters used from this closer
        if ($opener->getLength() >= 2 && $closer->getLength() >= 2) {
            if ($this->enableStrong()) {
                return 2;
            }

            return 0;
        }

        if ($this->enableEm()) {
            return 1;
        }

        return 0;
    }

    public function process(AbstractStringContainer $opener, AbstractStringContainer $closer, int $delimiterUse)
    {
        if ($delimiterUse === 1) {
            $emphasis = new Emphasis();
        } elseif ($delimiterUse === 2) {
            $emphasis = new Strong();
        } else {
            return;
        }

        $next = $opener->next();
        while ($next !== null && $next !== $closer) {
            $tmp = $next->next();
            $emphasis->appendChild($next);
            $next = $tmp;
        }

        $opener->insertAfter($emphasis);
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    private function enableStrong(): bool
    {
        if ($this->config === null) {
            return false;
        }

        $deprecatedEnableStrong = $this->config->get('enable_strong', ConfigurationInterface::MISSING);
        if ($deprecatedEnableStrong !== ConfigurationInterface::MISSING) {
            @\trigger_error('The "enable_strong" configuration option is deprecated in league/commonmark 1.6 and will be replaced with "commonmark > enable_strong" in 2.0', \E_USER_DEPRECATED);
        } else {
            $deprecatedEnableStrong = true;
        }

        return $this->config->get('commonmark/enable_strong', $deprecatedEnableStrong);
    }

    private function enableEm(): bool
    {
        if ($this->config === null) {
            return false;
        }

        $deprecatedEnableEm = $this->config->get('enable_em', ConfigurationInterface::MISSING);
        if ($deprecatedEnableEm !== ConfigurationInterface::MISSING) {
            @\trigger_error('The "enable_em" configuration option is deprecated in league/commonmark 1.6 and will be replaced with "commonmark > enable_em" in 2.0', \E_USER_DEPRECATED);
        } else {
            $deprecatedEnableEm = true;
        }

        return $this->config->get('commonmark/enable_em', $deprecatedEnableEm);
    }
}
