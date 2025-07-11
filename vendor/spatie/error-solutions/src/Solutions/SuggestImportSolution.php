<?php

namespace Spatie\ErrorSolutions\Solutions;

use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class SuggestImportSolution implements Solution
{
    use IsProvidedByFlare;

    protected string $class;

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
