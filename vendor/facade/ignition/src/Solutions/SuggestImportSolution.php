<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\Solution;

class SuggestImportSolution implements Solution
{
    /** @var string */
    protected $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function getSolutionTitle(): string
    {
        return 'A class import is missing';
    }

    public function getSolutionDescription(): string
    {
        return 'You have a missing class import. Try importing this class: `'.$this->class.'`.';
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }
}
