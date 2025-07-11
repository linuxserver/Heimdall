<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Illuminate\Support\Facades\Artisan;
use Spatie\ErrorSolutions\Contracts\RunnableSolution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class GenerateAppKeySolution implements RunnableSolution
{
    use IsProvidedByFlare;

    public function getSolutionTitle(): string
    {
        return 'Your app key is missing';
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Laravel installation' => 'https://laravel.com/docs/master/installation#configuration',
        ];
    }

    public function getSolutionActionDescription(): string
    {
        return 'Generate your application encryption key using `php artisan key:generate`.';
    }

    public function getRunButtonText(): string
    {
        return 'Generate app key';
    }

    public function getSolutionDescription(): string
    {
        return $this->getSolutionActionDescription();
    }

    public function getRunParameters(): array
    {
        return [];
    }

    public function run(array $parameters = []): void
    {
        Artisan::call('key:generate');
    }
}
