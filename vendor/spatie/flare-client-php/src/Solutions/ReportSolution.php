<?php

namespace Spatie\FlareClient\Solutions;

use Spatie\ErrorSolutions\Contracts\RunnableSolution;
use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\Ignition\Contracts\RunnableSolution as IgnitionRunnableSolution;
use Spatie\Ignition\Contracts\Solution as IgnitionSolution;

class ReportSolution
{
    protected Solution|IgnitionSolution $solution;

    public function __construct(Solution|IgnitionSolution $solution)
    {
        $this->solution = $solution;
    }

    public static function fromSolution(Solution|IgnitionSolution $solution): self
    {
        return new self($solution);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $isRunnable = ($this->solution instanceof RunnableSolution || $this->solution instanceof IgnitionRunnableSolution);

        return [
            'class' => get_class($this->solution),
            'title' => $this->solution->getSolutionTitle(),
            'description' => $this->solution->getSolutionDescription(),
            'links' => $this->solution->getDocumentationLinks(),
            /** @phpstan-ignore-next-line  */
            'action_description' => $isRunnable ? $this->solution->getSolutionActionDescription() : null,
            'is_runnable' => $isRunnable,
            'ai_generated' => $this->solution->aiGenerated ?? false,
        ];
    }
}
