<?php
namespace enshrined\svgSanitize\ElementReference;

use enshrined\svgSanitize\data\XPath;
use enshrined\svgSanitize\Exceptions\NestingException;
use enshrined\svgSanitize\Helper;

class Resolver
{
    /**
     * @var XPath
     */
    protected $xPath;

    /**
     * @var Subject[]
     */
    protected $subjects = [];

    /**
     * @var array DOMElement[]
     */
    protected $elementsToRemove = [];

    /**
     * @var int
     */
    protected $useNestingLimit;

    public function __construct(XPath $xPath, $useNestingLimit)
    {
        $this->xPath = $xPath;
        $this->useNestingLimit = $useNestingLimit;
    }

    public function collect()
    {
        $this->collectIdentifiedElements();
        $this->processReferences();
        $this->determineInvalidSubjects();
    }

    /**
     * Resolves one subject by element.
     *
     * @param \DOMElement $element
     * @param bool $considerChildren Whether to search in Subject's children as well
     * @return Subject|null
     */
    public function findByElement(\DOMElement $element, $considerChildren = false)
    {
        foreach ($this->subjects as $subject) {
            if (
                $element === $subject->getElement()
                || $considerChildren && Helper::isElementContainedIn($element, $subject->getElement())
            ) {
                return $subject;
            }
        }
        return null;
    }

    /**
     * Resolves subjects (plural!) by element id - in theory malformed
     * DOM might have same ids assigned to different elements and leaving
     * it to client/browser implementation which element to actually use.
     *
     * @param string $elementId
     * @return Subject[]
     */
    public function findByElementId($elementId)
    {
        // Subjects are keyed by id, so look it up directly. Filtering every
        // subject here made each `<use>` check cost as much as the whole graph.
        if (!isset($this->subjects[$elementId])) {
            return [];
        }

        return [$elementId => $this->subjects[$elementId]];
    }

    /**
     * Collects elements having `id` attribute (those that can be referenced).
     */
    protected function collectIdentifiedElements()
    {
        $this->collectIdentifiedElementsUnder($this->xPath->document);
    }

    /**
     * Visit the elements under $node in document order through their sibling
     * links. Before PHP 8.4, an XPath query such as `//*[@id]` creates a PHP
     * object for every match at once, at about half a kilobyte each.
     *
     * @param \DOMNode $node
     */
    protected function collectIdentifiedElementsUnder(\DOMNode $node)
    {
        for ($element = $node->firstChild; $element !== null; $element = $element->nextSibling) {
            if (!$element instanceof \DOMElement) {
                continue;
            }
            if ($element->hasAttribute('id')) {
                $id = $element->getAttribute('id');
                // Browsers resolve `#id` to the first element with that id. Keeping a
                // later duplicate instead would let an empty decoy stand in for the
                // element that is actually rendered.
                if (!isset($this->subjects[$id])) {
                    $this->subjects[$id] = new Subject($element, $this->useNestingLimit);
                }
            }
            if ($element->hasChildNodes()) {
                $this->collectIdentifiedElementsUnder($element);
            }
        }
    }

    /**
     * Processes references from and to elements having `id` attribute concerning
     * their occurrence in `<use ... xlink:href="#identifier">` statements.
     */
    protected function processReferences()
    {
        // Note: the href attribute is deliberately not filtered in the XPath predicate.
        // XPath attribute matching is case sensitive, so `[@href or @xlink:href]` would
        // skip `<use HrEf="#id">`/`<use xlink:HrEf="#id">` - names that
        // `Sanitizer::cleanHrefAttributes()` normalizes back to `href`/`xlink:href`
        // afterwards, which would hand back a live reference the graph never saw.
        //
        // Every `<use>` inside an identified element counts, however deeply it is
        // nested: rendering the element renders all of them, so wrapping them in
        // a plain `<g>` must not hide them from the graph. To keep this linear,
        // each `<use>` is attached to its nearest identified ancestor only, and
        // each identified element records the identified elements nested
        // directly inside it (see Subject::addContained()).
        $documentElement = $this->xPath->document->documentElement;
        if ($documentElement !== null) {
            $ancestorIds = [];
            // A `<use>` counts when it is in the root's namespace, which is what
            // the `svg:use` XPath name matches. Checking that during the walk,
            // instead of collecting every `<use>` up front, keeps memory flat.
            $this->walkReferences($documentElement, null, $ancestorIds, (string) $documentElement->namespaceURI);
        }
    }

    /**
     * Walk the tree once, recording each `<use>` reference on its nearest
     * identified ancestor and each identified element on the one around it.
     *
     * @param \DOMElement $element
     * @param Subject|null $nearest Nearest identified ancestor
     * @param array<string, bool> $ancestorIds Ids of all ancestors
     * @param string $useNamespace The namespace a `<use>` must be in
     */
    protected function walkReferences(\DOMElement $element, $nearest, array &$ancestorIds, $useNamespace)
    {
        $id = null;
        if ($element->hasAttribute('id')) {
            $id = $element->getAttribute('id');
            if (isset($this->subjects[$id]) && $this->subjects[$id]->getElement() === $element) {
                if ($nearest !== null) {
                    $nearest->addContained($this->subjects[$id]);
                }
                $nearest = $this->subjects[$id];
            }
            if (isset($ancestorIds[$id])) {
                $id = null;
            } else {
                $ancestorIds[$id] = true;
            }
        }

        // Record the reference only now, so a `<use>` with its own id owns it:
        // rendering `<use id="u" href="#x">` renders `#x`, so anything that uses
        // `#u` renders `#x` too. Its own id is in $ancestorIds by now, so
        // `<use id="a" href="#a">` counts as a self reference.
        if ($element->localName === 'use' && (string) $element->namespaceURI === $useNamespace) {
            $this->addReference($element, $nearest, $ancestorIds);
        }

        // Follow the sibling links. Before PHP 8.1, iterating childNodes with
        // foreach looked each child up by position, which was quadratic in the
        // number of children.
        for ($child = $element->firstChild; $child !== null; $child = $child->nextSibling) {
            if ($child instanceof \DOMElement) {
                $this->walkReferences($child, $nearest, $ancestorIds, $useNamespace);
            }
        }

        if ($id !== null) {
            unset($ancestorIds[$id]);
        }
    }

    /**
     * @param \DOMElement $useElement
     * @param Subject|null $nearest
     * @param array<string, bool> $ancestorIds
     */
    protected function addReference(\DOMElement $useElement, $nearest, array $ancestorIds)
    {
        $useId = Helper::extractIdReferenceFromHref(
            Helper::getElementHref($useElement)
        );
        if ($useId === null || !isset($this->subjects[$useId])) {
            return;
        }

        // A `<use>` inside the element it references would render itself
        // forever. That is attacker input, not a programming error, so remove
        // the `<use>` instead of adding a self edge.
        if (isset($ancestorIds[$useId])) {
            $this->elementsToRemove[] = $useElement;
            return;
        }

        if ($nearest === null) {
            return;
        }

        $referenced = $this->subjects[$useId];
        $nearest->addUse($referenced);
        $referenced->addUsedIn($nearest);
    }

    /**
     * Determines and tags infinite loops.
     */
    protected function determineInvalidSubjects()
    {
        foreach ($this->subjects as $subject) {

            if (in_array($subject->getElement(), $this->elementsToRemove)) {
                continue;
            }

            $useId = Helper::extractIdReferenceFromHref(
                Helper::getElementHref($subject->getElement())
            );

            try {
                // Only a `<use>` follows its own href. An href on any other
                // element, such as `<g id="a" href="#a">`, is inert.
                if ($useId === $subject->getElementId() && strtolower($subject->getElement()->localName) === 'use') {
                    $this->markSubjectAsInvalid($subject);
                } elseif ($subject->hasInfiniteLoop()) {
                    $this->markSubjectAsInvalid($subject);
                }
            } catch (NestingException $e) {
                $this->elementsToRemove[] = $e->getElement();
                $this->markSubjectAsInvalid($subject);
            }
        }
    }

    /**
     * Get all the elements that caused a nesting exception.
     *
     * @return array
     */
    public function getElementsToRemove() {
        return $this->elementsToRemove;
    }

    /**
     * The Subject is invalid for some reason, therefore we should
     * remove it and all it's child usages.
     *
     * @param Subject $subject
     */
    protected function markSubjectAsInvalid(Subject $subject) {
        // Remove the `<use>` elements the subject renders, not only the targets of
        // its direct uses. A subject whose loop runs through an element nested
        // inside it has no direct uses, so clearing its edges alone would remove
        // nothing and hide the loop from every later check.
        $useElements = iterator_to_array(
            $this->xPath->query('descendant-or-self::' . $this->xPath->createNodeName('use'), $subject->getElement()),
            false
        );

        $this->elementsToRemove = array_merge(
            $this->elementsToRemove,
            $subject->clearInternalAndGetAffectedElements(),
            $useElements
        );
    }
}