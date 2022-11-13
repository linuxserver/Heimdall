<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Solutions\SuggestLivewirePropertyNameSolution;
use Facade\Ignition\Support\LivewireComponentParser;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Livewire\Exceptions\PropertyNotFoundException;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Throwable;

class UndefinedLivewirePropertySolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        return $throwable instanceof PropertyNotFoundException || $throwable instanceof PublicPropertyNotFoundException;
    }

    public function getSolutions(Throwable $throwable): array
    {
        ['variable' => $variable, 'component' => $component] = $this->getMethodAndComponent($throwable);

        if ($variable === null || $component === null) {
            return [];
        }

        $parsed = LivewireComponentParser::create($component);

        return $parsed->getPropertyNamesLike($variable)
            ->map(function (string $suggested) use ($parsed, $variable) {
                return new SuggestLivewirePropertyNameSolution(
                    $variable,
                    $parsed->getComponentClass(),
                    '$'.$suggested
                );
            })
            ->toArray();
    }

    protected function getMethodAndComponent(Throwable $throwable): array
    {
        preg_match_all('/\[([\d\w\-_\$]*)\]/m', $throwable->getMessage(), $matches, PREG_SET_ORDER, 0);

        return [
            'variable' => $matches[0][1] ?? null,
            'component' => $matches[1][1] ?? null,
        ];
    }
}
