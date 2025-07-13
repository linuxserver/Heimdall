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
        return array_filter(
            $this->subjects,
            function (Subject $subject) use ($elementId) {
                return $elementId === $subject->getElementId();
            }
        );
    }

    /**
     * Collects elements having `id` attribute (those that can be referenced).
     */
    protected function collectIdentifiedElements()
    {
        /** @var \DOMNodeList|\DOMElement[] $elements */
        $elements = $this->xPath->query('//*[@id]');
        foreach ($elements as $element) {
            $this->subjects[$element->getAttribute('id')] = new Subject($element, $this->useNestingLimit);
        }
    }

    /**
     * Processes references from and to elements having `id` attribute concerning
     * their occurrence in `<use ... xlink:href="#identifier">` statements.
     */
    protected function processReferences()
    {
        $useNodeName = $this->xPath->createNodeName('use');
        foreach ($this->subjects as $subject) {
            $useElements = $this->xPath->query(
                $useNodeName . '[@href or @xlink:href]',
                $subject->getElement()
            );

            /** @var \DOMElement $useElement */
            foreach ($useElements as $useElement) {
                $useId = Helper::extractIdReferenceFromHref(
                    Helper::getElementHref($useElement)
                );
                if ($useId === null || !isset($this->subjects[$useId])) {
                    continue;
                }
                $subject->addUse($this->subjects[$useId]);
                $this->subjects[$useId]->addUsedIn($subject);
            }
        }
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
                if ($useId === $subject->getElementId()) {
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
        $this->elementsToRemove = array_merge(
            $this->elementsToRemove,
            $subject->clearInternalAndGetAffectedElements()
        );
    }
}