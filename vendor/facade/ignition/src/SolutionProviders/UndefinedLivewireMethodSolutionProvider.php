<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Solutions\SuggestLivewireMethodNameSolution;
use Facade\Ignition\Support\LivewireComponentParser;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Livewire\Exceptions\MethodNotFoundException;
use Throwable;

class UndefinedLivewireMethodSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        return $throwable instanceof MethodNotFoundException;
    }

    public function getSolutions(Throwable $throwable): array
    {
        ['methodName' => $methodName, 'component' => $component] = $this->getMethodAndComponent($throwable);

        if ($methodName === null || $component === null) {
            return [];
        }

        $parsed = LivewireComponentParser::create($component);

        return $parsed->getMethodNamesLike($methodName)
            ->map(function (string $suggested) use ($parsed, $methodName) {
                return new SuggestLivewireMethodNameSolution(
                    $methodName,
                    $parsed->getComponentClass(),
                    $suggested
                );
            })
            ->toArray();
    }

    protected function getMethodAndComponent(Throwable $throwable): array
    {
        preg_match_all('/\[([\d\w\-_]*)\]/m', $throwable->getMessage(), $matches, PREG_SET_ORDER);

        return [
            'methodName' => $matches[0][1] ?? null,
            'component' => $matches[1][1] ?? null,
        ];
    }
}
