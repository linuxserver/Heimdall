<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Livewire\Exceptions\PropertyNotFoundException;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Solutions\Laravel\SuggestLivewirePropertyNameSolution;
use Spatie\ErrorSolutions\Support\Laravel\LivewireComponentParser;
use Throwable;

class UndefinedLivewirePropertySolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        return $throwable instanceof PropertyNotFoundException;
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

    /**
     * @param \Throwable $throwable
     *
     * @return array<string, string|null>
     */
    protected function getMethodAndComponent(Throwable $throwable): array
    {
        preg_match_all('/\[([\d\w\-_\$]*)\]/m', $throwable->getMessage(), $matches, PREG_SET_ORDER, 0);

        return [
            'variable' => $matches[0][1] ?? null,
            'component' => $matches[1][1] ?? null,
        ];
    }
}
