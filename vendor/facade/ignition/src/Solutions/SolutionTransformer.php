<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;

class SolutionTransformer implements Arrayable
{
    /** @var \Facade\IgnitionContracts\Solution */
    protected $solution;

    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
    }

    public function toArray(): array
    {
        $isRunnable = ($this->solution instanceof RunnableSolution);

        return [
            'class' => get_class($this->solution),
            'title' => $this->solution->getSolutionTitle(),
            'description' => $this->solution->getSolutionDescription(),
            'links' => $this->solution->getDocumentationLinks(),
            'is_runnable' => $isRunnable,
            'run_button_text' => $isRunnable ? $this->solution->getRunButtonText() : '',
            'run_parameters' => $isRunnable ? $this->solution->getRunParameters() : [],
            'action_description' => $isRunnable ? $this->solution->getSolutionActionDescription() : '',
            'execute_endpoint' => $this->executeEndpoint(),
        ];
    }

    protected function executeEndpoint(): string
    {
        try {
            return action('\Facade\Ignition\Http\Controllers\ExecuteSolutionController');
        } catch (Throwable $exception) {
            return '';
        }
    }
}
