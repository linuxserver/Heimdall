<?php

namespace Facade\FlareClient\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Facade\IgnitionContracts\Solution as SolutionContract;

class ReportSolution
{
    /** @var SolutionContract */
    protected $solution;

    public function __construct(SolutionContract $solution)
    {
        $this->solution = $solution;
    }

    public static function fromSolution(SolutionContract $solution)
    {
        return new static($solution);
    }

    public function toArray(): array
    {
        $isRunnable = ($this->solution instanceof RunnableSolution);

        return [
            'class' => get_class($this->solution),
            'title' => $this->solution->getSolutionTitle(),
            'description' => $this->solution->getSolutionDescription(),
            'links' => $this->solution->getDocumentationLinks(),
            'action_description' => $isRunnable ? $this->solution->getSolutionActionDescription() : null,
            'is_runnable' => $isRunnable,
        ];
    }
}
