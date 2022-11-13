<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\Solution;

class SuggestLivewireMethodNameSolution implements Solution
{
    /** @var string */
    private $methodName;

    /** @var string */
    private $componentClass;

    /** @var string|null */
    private $suggested;

    public function __construct($methodName = null, $componentClass = null, $suggested = null)
    {
        $this->methodName = $methodName;
        $this->componentClass = $componentClass;
        $this->suggested = $suggested;
    }

    public function getSolutionTitle(): string
    {
        return "Possible typo `{$this->componentClass}::{$this->methodName}()`";
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getSolutionDescription(): string
    {
        return "Did you mean `{$this->suggested}()`?";
    }

    public function isRunnable(): bool
    {
        return false;
    }
}
