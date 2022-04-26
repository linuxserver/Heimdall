<?php

namespace Facade\Ignition\Http\Requests;

use Facade\IgnitionContracts\RunnableSolution;
use Facade\IgnitionContracts\Solution;
use Facade\IgnitionContracts\SolutionProviderRepository;
use Illuminate\Foundation\Http\FormRequest;

class ExecuteSolutionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'solution' => 'required',
            'parameters' => 'array',
        ];
    }

    public function getSolution(): Solution
    {
        $solution = app(SolutionProviderRepository::class)
            ->getSolutionForClass($this->get('solution'));

        abort_if(is_null($solution), 404, 'Solution could not be found');

        /** @var Solution */
        return $solution;
    }

    public function getRunnableSolution(): RunnableSolution
    {
        $solution = $this->getSolution();

        if (! $solution instanceof RunnableSolution) {
            abort(404, 'Runnable solution could not be found');
        }

        return $solution;
    }
}
