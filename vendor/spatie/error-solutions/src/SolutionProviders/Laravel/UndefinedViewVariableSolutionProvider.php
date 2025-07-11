<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Spatie\ErrorSolutions\Contracts\BaseSolution;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Solutions\Laravel\MakeViewVariableOptionalSolution;
use Spatie\ErrorSolutions\Solutions\Laravel\SuggestCorrectVariableNameSolution;
use Spatie\LaravelFlare\Exceptions\ViewException as FlareViewException;
use Spatie\LaravelIgnition\Exceptions\ViewException as IgnitionViewException;
use Throwable;

class UndefinedViewVariableSolutionProvider implements HasSolutionsForThrowable
{
    protected string $variableName;

    protected string $viewFile;

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof IgnitionViewException && ! $throwable instanceof FlareViewException) {
            return false;
        }

        return $this->getNameAndView($throwable) !== null;
    }

    public function getSolutions(Throwable $throwable): array
    {
        $solutions = [];

        /** @phpstan-ignore-next-line  */
        extract($this->getNameAndView($throwable));

        if (! isset($variableName)) {
            return [];
        }

        if (isset($viewFile)) {
            /** @phpstan-ignore-next-line  */
            $solutions = $this->findCorrectVariableSolutions($throwable, $variableName, $viewFile);
            $solutions[] = $this->findOptionalVariableSolution($variableName, $viewFile);
        }


        return $solutions;
    }

    /**
     * @param IgnitionViewException|FlareViewException $throwable
     * @param string $variableName
     * @param string $viewFile
     *
     * @return array<int, \Spatie\ErrorSolutions\Contracts\Solution>
     */
    protected function findCorrectVariableSolutions(
        IgnitionViewException|FlareViewException $throwable,
        string $variableName,
        string $viewFile
    ): array {
        return collect($throwable->getViewData())
            ->map(function ($value, $key) use ($variableName) {
                similar_text($variableName, $key, $percentage);

                return ['match' => $percentage, 'value' => $value];
            })
            ->sortByDesc('match')
            ->filter(fn ($var) => $var['match'] > 40)
            ->keys()
            ->map(fn ($suggestion) => new SuggestCorrectVariableNameSolution($variableName, $viewFile, $suggestion))
            ->map(function ($solution) {
                return $solution->isRunnable()
                    ? $solution
                    : BaseSolution::create($solution->getSolutionTitle())
                        ->setSolutionDescription($solution->getSolutionDescription());
            })
            ->toArray();
    }

    protected function findOptionalVariableSolution(string $variableName, string $viewFile): Solution
    {
        $optionalSolution = new MakeViewVariableOptionalSolution($variableName, $viewFile);

        return $optionalSolution->isRunnable()
            ? $optionalSolution
            : BaseSolution::create($optionalSolution->getSolutionTitle())
                ->setSolutionDescription($optionalSolution->getSolutionDescription());
    }

    /**
     * @param \Throwable $throwable
     *
     * @return array<string, string>|null
     */
    protected function getNameAndView(Throwable $throwable): ?array
    {
        $pattern = '/Undefined variable:? (.*?) \(View: (.*?)\)/';

        preg_match($pattern, $throwable->getMessage(), $matches);

        if (count($matches) === 3) {
            [, $variableName, $viewFile] = $matches;
            $variableName = ltrim($variableName, '$');

            return compact('variableName', 'viewFile');
        }

        return null;
    }
}
