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
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Reference\Reference;

final class NumberFootnotesListener
{
    public function onDocumentParsed(DocumentParsedEvent $event): void
    {
        $document     = $event->getDocument();
        $nextCounter  = 1;
        $usedLabels   = [];
        $usedCounters = [];
        $backrefs     = [];

        foreach ($document->iterator() as $node) {
            if (! $node instanceof FootnoteRef) {
                continue;
            }

            $existingReference   = $node->getReference();
            $label               = $existingReference->getLabel();
            $counter             = $nextCounter;
            $canIncrementCounter = true;

            if (\array_key_exists($label, $usedLabels)) {
                /*
                 * Reference is used again, we need to point
                 * to the same footnote. But with a different ID
                 */
                $counter             = $usedCounters[$label];
                $label              .= '__' . ++$usedLabels[$label];
                $canIncrementCounter = false;
            }

            // rewrite reference title to use a numeric link
            $newReference = new Reference(
                $label,
                $existingReference->getDestination(),
                (string) $counter
            );

            // Override reference with numeric link
            $node->setReference($newReference);
            $document->getReferenceMap()->add($newReference);

            /*
             * Store created references for creating FootnoteBackrefs.
             *
             * These are collected into a plain array keyed by the exact destination rather than
             * written straight into $document->data: destinations are built from user-supplied
             * labels, and Data treats both "." and "/" as key path delimiters. Using them as
             * paths would let distinct labels collapse onto a shared entry, or nest one label's
             * entry inside another's.
             */
            $backrefs[$existingReference->getDestination()][] = $newReference;

            $usedLabels[$label]   = 1;
            $usedCounters[$label] = $nextCounter;

            if ($canIncrementCounter) {
                $nextCounter++;
            }
        }

        $document->data->set('footnote/backrefs', $backrefs);
    }
}
