<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Event;

use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\Footnote\Node\Footnote;
use League\CommonMark\Extension\Footnote\Node\FootnoteBackref;
use League\CommonMark\Extension\Footnote\Node\FootnoteContainer;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Reference\Reference;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class GatherFootnotesListener implements ConfigurationAwareInterface
{
    private ConfigurationInterface $config;

    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document    = $event->getDocument();
        $footnotes   = [];
        $definitions = [];
        $discarded   = [];

        /** @var array<string, Reference[]> $backrefs */
        $backrefs = $document->data->get('footnote/backrefs', []);

        /*
         * A label may be defined more than once. Only the first definition is used, matching how
         * duplicate link reference definitions are resolved; the rest are discarded below.
         *
         * Keeping just one definition per label is also what bounds the work done here: every
         * definition sharing a label claims that label's entire backref list, so N duplicate
         * definitions of a label referenced M times would otherwise produce M * N backrefs - a
         * denial of service vector, since both are attacker-controlled.
         *
         * Nodes are collected rather than detached here because detaching mid-iteration would
         * truncate the walk.
         */
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if (! $node instanceof Footnote) {
                continue;
            }

            $label = $node->getReference()->getLabel();
            if (isset($definitions[$label])) {
                $discarded[] = $node;

                continue;
            }

            $definitions[$label] = $node;
        }

        foreach ($definitions as $node) {
            // Look for existing reference with footnote label
            $ref = $document->getReferenceMap()->get($node->getReference()->getLabel());
            if ($ref !== null) {
                // Use numeric title to get footnotes order
                $footnotes[(int) $ref->getTitle()] = $node;
            } else {
                // Footnote call is missing, append footnote at the end
                $footnotes[\PHP_INT_MAX] = $node;
            }

            $key = '#' . $this->config->get('footnote/footnote_id_prefix') . $node->getReference()->getDestination();
            if (isset($backrefs[$key])) {
                $this->createBackrefs($node, $backrefs[$key]);
            }
        }

        foreach ($discarded as $duplicate) {
            $duplicate->detach();
        }

        // Only add a footnote container if there are any
        if (\count($footnotes) === 0) {
            return;
        }

        $container = $this->getFootnotesContainer($document);

        \ksort($footnotes);
        foreach ($footnotes as $footnote) {
            $container->appendChild($footnote);
        }
    }

    private function getFootnotesContainer(Document $document): FootnoteContainer
    {
        $footnoteContainer = new FootnoteContainer();
        $document->appendChild($footnoteContainer);

        return $footnoteContainer;
    }

    /**
     * Look for all footnote refs pointing to this footnote and create each footnote backrefs.
     *
     * @param Footnote    $node     The target footnote
     * @param Reference[] $backrefs References to create backrefs for
     */
    private function createBackrefs(Footnote $node, array $backrefs): void
    {
        // Backrefs should be added to the child paragraph
        $target = $node->lastChild();
        if ($target === null) {
            // This should never happen, but you never know
            $target = $node;
        }

        foreach ($backrefs as $backref) {
            $target->appendChild(new FootnoteBackref(new Reference(
                $backref->getLabel(),
                '#' . $this->config->get('footnote/ref_id_prefix') . $backref->getLabel(),
                $backref->getTitle()
            )));
        }
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }
}
