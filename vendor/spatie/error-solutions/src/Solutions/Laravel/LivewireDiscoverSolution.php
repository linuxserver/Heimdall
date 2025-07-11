<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Livewire\LivewireComponentsFinder;
use Spatie\ErrorSolutions\Contracts\RunnableSolution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class LivewireDiscoverSolution implements RunnableSolution
{
    use IsProvidedByFlare;

    protected string $customTitle;

    public function __construct(string $customTitle = '')
    {
        $this->customTitle = $customTitle;
    }

    public function getSolutionTitle(): string
    {
        return $this->customTitle;
    }

    public function getSolutionDescription(): string
    {
        return 'You might have forgotten to discover your Livewire components.';
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Livewire: Artisan Commands' => 'https://laravel-livewire.com/docs/2.x/artisan-commands',
        ];
    }

    public function getRunParameters(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'You can discover your Livewire components using `php artisan livewire:discover`.';
    }

    public function getRunButtonText(): string
    {
        return 'Run livewire:discover';
    }

    public function run(array $parameters = []): void
    {
        app(LivewireComponentsFinder::class)->build();
    }
}
