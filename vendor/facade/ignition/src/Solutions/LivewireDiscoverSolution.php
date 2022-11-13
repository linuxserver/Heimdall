<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Livewire\LivewireComponentsFinder;

class LivewireDiscoverSolution implements RunnableSolution
{
    private $customTitle;

    public function __construct($customTitle = '')
    {
        $this->customTitle = $customTitle;
    }

    public function getSolutionTitle(): string
    {
        return $this->customTitle;
    }

    public function getSolutionDescription(): string
    {
        return 'You might have forgotten to discover your Livewire components. You can discover your Livewire components using `php artisan livewire:discover`.';
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
        return 'Pressing the button below will try to discover your Livewire components.';
    }

    public function getRunButtonText(): string
    {
        return 'Run livewire:discover';
    }

    public function run(array $parameters = [])
    {
        app(LivewireComponentsFinder::class)->build();
    }
}
