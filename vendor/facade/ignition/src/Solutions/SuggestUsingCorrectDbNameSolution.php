<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\Solution;

class SuggestUsingCorrectDbNameSolution implements Solution
{
    public function getSolutionTitle(): string
    {
        return 'Database name seems incorrect';
    }

    public function getSolutionDescription(): string
    {
        $defaultDatabaseName = env('DB_DATABASE');

        return "You're using the default database name `$defaultDatabaseName`. This database does not exist.\n\nEdit the `.env` file and use the correct database name in the `DB_DATABASE` key.";
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Database: Getting Started docs' => 'https://laravel.com/docs/master/database#configuration',
        ];
    }
}
