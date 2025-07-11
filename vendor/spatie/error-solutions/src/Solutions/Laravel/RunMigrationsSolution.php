<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Illuminate\Support\Facades\Artisan;
use Spatie\ErrorSolutions\Contracts\RunnableSolution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class RunMigrationsSolution implements RunnableSolution
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
        return 'You might have forgotten to run your database migrations.';
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Database: Running Migrations docs' => 'https://laravel.com/docs/master/migrations#running-migrations',
        ];
    }

    public function getRunParameters(): array
    {
        return [];
    }

    public function getSolutionActionDescription(): string
    {
        return 'You can try to run your migrations using `php artisan migrate`.';
    }

    public function getRunButtonText(): string
    {
        return 'Run migrations';
    }

    public function run(array $parameters = []): void
    {
        Artisan::call('migrate');
    }
}
