<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Facades\Artisan;

class RunMigrationsSolution implements RunnableSolution
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
        return 'You might have forgotten to run your migrations. You can run your migrations using `php artisan migrate`.';
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
        return 'Pressing the button below will try to run your migrations.';
    }

    public function getRunButtonText(): string
    {
        return 'Run migrations';
    }

    public function run(array $parameters = [])
    {
        Artisan::call('migrate');
    }
}
