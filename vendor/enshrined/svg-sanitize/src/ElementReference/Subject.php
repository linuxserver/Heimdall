<?php
namespace enshrined\svgSanitize\ElementReference;

class Subject
{
    /**
     * @var \DOMElement
     */
    protected $element;

    /**
     * @var Usage[]
     */
    protected $useCollection = [];

    /**
     * @var Usage[]
     */
    protected $usedInCollection = [];

    /**
     * @var int
     */
    protected $useNestingLimit;

    /**
     * Subject constructor.
     *
     * @param \DOMElement $element
     * @param int         $useNestingLimit
     */
    public function __construct(\DOMElement $element, $useNestingLimit)
    {
        $this->element = $element;
        $this->useNestingLimit = $useNestingLimit;
    }

    /**
     * @return \DOMElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getElementId()
    {
        return $this->element->getAttribute('id');
    }

    /**
     * @param array $subjects   Previously processed subjects
     * @param int   $level      The current level of nesting.
     * @return bool
     * @throws \enshrined\svgSanitize\Exceptions\NestingException
     */
    public function hasInfiniteLoop(array $subjects = [], $level = 1)
    {
        if ($level > $this->useNestingLimit) {
            throw new \enshrined\svgSanitize\Exceptions\NestingException('Nesting level too high, aborting', 1570713498, null, $this->getElement());
        }

        if (in_array($this, $subjects, true)) {
            return true;
        }
        $subjects[] = $this;
        foreach ($this->useCollection as $usage) {
            if ($usage->getSubject()->hasInfiniteLoop($subjects, $level + 1)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Subject $subject
     */
    public function addUse(Subject $subject)
    {
        if ($subject === $this) {
            throw new \LogicException('Cannot add self usage', 1570713416);
        }
        $identifier = $subject->getElementId();
        if (isset($this->useCollection[$identifier])) {
            $this->useCollection[$identifier]->increment();
            return;
        }
        $this->useCollection[$identifier] = new Usage($subject);
    }

    /**
     * @param Subject $subject
     */
    public function addUsedIn(Subject $subject)
    {
        if ($subject === $this) {
            throw new \LogicException('Cannot add self as usage', 1570713417);
        }
        $identifier = $subject->getElementId();
        if (isset($this->usedInCollection[$identifier])) {
            $this->usedInCollection[$identifier]->increment();
            return;
        }
        $this->usedInCollection[$identifier] = new Usage($subject);
    }

    /**
     * @param bool $accumulated
     * @return int
     */
    public function countUse($accumulated = false)
    {
        $count = 0;
        foreach ($this->useCollection as $use) {
            $useCount = $use->getSubject()->countUse();
            $count += $use->getCount() * ($accumulated ? 1 + $useCount : max(1, $useCount));
        }
        return $count;
    }

    /**
     * @return int
     */
    public function countUsedIn()
    {
        $count = 0;
        foreach ($this->usedInCollection as $usedIn) {
            $count += $usedIn->getCount() * max(1, $usedIn->getSubject()->countUsedIn());
        }
        return $count;
    }

    /**
     * Clear the internal arrays (to free up memory as they can get big)
     * and return all the child usages DOMElement's
     *
     * @return array
     */
    public function clearInternalAndGetAffectedElements()
    {
        $elements = array_map(function(Usage $usage) {
            return $usage->getSubject()->getElement();
        }, $this->useCollection);

        $this->usedInCollection = [];
        $this->useCollection = [];

        return $elements;
    }
}