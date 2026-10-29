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
     * Identified elements nested inside this one with no identified element
     * between. Rendering this element renders them, and every `<use>` inside
     * them, so they count towards this element's uses.
     *
     * @var Subject[]
     */
    protected $containedSubjects = [];

    /**
     * @var int
     */
    protected $useNestingLimit;

    /**
     * @var string
     */
    protected $elementId;

    /**
     * Number of subjects on the longest chain of uses starting here, once this
     * subject is known to lead to no loop. Null until then.
     *
     * Remembering it means each subject is walked once. Without it, a layered
     * graph where every element uses every element of the next layer was walked
     * once per path, and the number of paths grows exponentially with depth.
     *
     * @var int|null
     */
    protected $loopFreeDepth;

    /**
     * Results of countUse(), keyed by its $accumulated argument.
     *
     * @var array<int, int>
     */
    protected $useCounts = [];

    /**
     * @var bool
     */
    protected $countingUse = false;

    /**
     * Result of countRenderedUse(), once known.
     *
     * @var int|null
     */
    protected $renderedUseCount;

    /**
     * @var bool
     */
    protected $countingRenderedUse = false;

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
        // Read the id once. getAttribute() walks the element's attributes, and
        // the graph asks for the id on every edge, so many attributes times many
        // references was quadratic. Ids are final by the time the graph is built.
        $this->elementId = $element->getAttribute('id');
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
        return $this->elementId;
    }

    /**
     * @param array $subjects   Previously processed subjects
     * @param int   $level      The current level of nesting.
     * @return bool
     * @throws \enshrined\svgSanitize\Exceptions\NestingException
     */
    public function hasInfiniteLoop(array $subjects = [], $level = 1)
    {
        $onPath = array();
        foreach ($subjects as $subject) {
            $onPath[spl_object_hash($subject)] = true;
        }

        return $this->findLoop($onPath, $level);
    }

    /**
     * Walk the subjects this one renders, looking for one already on the path.
     *
     * The path is one shared set, added to on the way down and removed from on
     * the way back. Copying a path array at every step instead was quadratic in
     * memory on long chains of nested identified elements.
     *
     * @param array<string, bool> $onPath Subjects on the current path
     * @param int $level The current level of nesting
     * @return bool
     * @throws \enshrined\svgSanitize\Exceptions\NestingException
     */
    protected function findLoop(array &$onPath, $level)
    {
        if ($level > $this->useNestingLimit) {
            throw new \enshrined\svgSanitize\Exceptions\NestingException('Nesting level too high, aborting', 1570713498, null, $this->getElement());
        }

        $key = spl_object_hash($this);
        if (isset($onPath[$key])) {
            return true;
        }

        // Already known to lead to no loop: only the nesting limit still depends
        // on the level we reached this subject at. If the known chain would go
        // past the limit, walk again so the exception names the right element.
        if ($this->loopFreeDepth !== null && $level + $this->loopFreeDepth - 1 <= $this->useNestingLimit) {
            return false;
        }

        $onPath[$key] = true;
        try {
            $depth = 1;
            foreach ($this->useCollection as $usage) {
                $usedSubject = $usage->getSubject();
                if ($usedSubject->findLoop($onPath, $level + 1)) {
                    return true;
                }
                $depth = max($depth, 1 + $usedSubject->loopFreeDepth);
            }
            // A contained element is rendered as part of this one, not through a
            // `<use>`, so it does not add a nesting level.
            foreach ($this->containedSubjects as $containedSubject) {
                if ($containedSubject->findLoop($onPath, $level)) {
                    return true;
                }
                $depth = max($depth, $containedSubject->loopFreeDepth);
            }
            $this->loopFreeDepth = $depth;

            return false;
        } finally {
            unset($onPath[$key]);
        }
    }

    /**
     * @param Subject $subject
     */
    public function addUse(Subject $subject)
    {
        if ($subject === $this) {
            throw new \LogicException('Cannot add self usage', 1570713416);
        }
        $this->forgetComputedValues();
        $identifier = $subject->getElementId();
        if (isset($this->useCollection[$identifier])) {
            $this->useCollection[$identifier]->increment();
            return;
        }
        $this->useCollection[$identifier] = new Usage($subject);
    }

    /**
     * Record an identified element nested inside this one.
     *
     * @param Subject $subject
     */
    public function addContained(Subject $subject)
    {
        if ($subject === $this) {
            throw new \LogicException('Cannot contain self', 1570713418);
        }
        $this->forgetComputedValues();
        $this->containedSubjects[] = $subject;
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
    /**
     * How many `<use>` instances rendering this subject produces, counting every
     * level of every chain: each `<use>` itself plus everything its target
     * renders, and everything inside contained subjects.
     *
     * countUse() counts only the deepest instances, so a chain with one `<use>`
     * per level counts as 1 however long it is. This is the count the
     * expansion threshold needs.
     *
     * @return int
     */
    public function countRenderedUse()
    {
        if ($this->renderedUseCount !== null) {
            return $this->renderedUseCount;
        }

        // Reached again while still counting: a reference loop. Loops are found
        // and removed by hasInfiniteLoop(), so count nothing rather than recurse
        // forever.
        if ($this->countingRenderedUse) {
            return 0;
        }

        $this->countingRenderedUse = true;
        $count = 0;
        foreach ($this->useCollection as $use) {
            $count += $use->getCount() * (1 + $use->getSubject()->countRenderedUse());
            // Stop at the largest integer instead of overflowing into a float.
            if (!is_int($count)) {
                $count = PHP_INT_MAX;
                break;
            }
        }
        foreach ($this->containedSubjects as $containedSubject) {
            if ($count === PHP_INT_MAX) {
                break;
            }
            $count += $containedSubject->countRenderedUse();
            if (!is_int($count)) {
                $count = PHP_INT_MAX;
            }
        }
        $this->countingRenderedUse = false;

        return $this->renderedUseCount = $count;
    }

    public function countUse($accumulated = false)
    {
        $key = $accumulated ? 1 : 0;
        if (isset($this->useCounts[$key])) {
            return $this->useCounts[$key];
        }

        // Reached again while still counting: a reference loop. Loops are found
        // and removed by hasInfiniteLoop(), so count nothing rather than recurse
        // forever.
        if ($this->countingUse) {
            return 0;
        }

        $this->countingUse = true;
        $count = 0;
        foreach ($this->useCollection as $use) {
            $useCount = $use->getSubject()->countUse();
            $count += $use->getCount() * ($accumulated ? 1 + $useCount : max(1, $useCount));
            // Stop at the largest integer instead of overflowing into a float.
            if (!is_int($count)) {
                $count = PHP_INT_MAX;
                break;
            }
        }
        // Uses inside contained elements are rendered as part of this one.
        foreach ($this->containedSubjects as $containedSubject) {
            if ($count === PHP_INT_MAX) {
                break;
            }
            $count += $containedSubject->countUse($accumulated);
            if (!is_int($count)) {
                $count = PHP_INT_MAX;
            }
        }
        $this->countingUse = false;

        return $this->useCounts[$key] = $count;
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
        $this->containedSubjects = [];
        $this->forgetComputedValues();

        return $elements;
    }

    /**
     * Drop remembered results after this subject's own uses change.
     */
    protected function forgetComputedValues()
    {
        $this->loopFreeDepth = null;
        $this->useCounts = [];
        $this->renderedUseCount = null;
    }
}