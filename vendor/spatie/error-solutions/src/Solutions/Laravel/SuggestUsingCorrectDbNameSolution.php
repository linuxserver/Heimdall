<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class SuggestUsingCorrectDbNameSolution implements Solution
{
    use IsProvidedByFlare;

    public function getSolutionTitle(): string
    {
        return 'Database name seems incorrect';
    }

    public function getSolutionDescription(): string
    {
        $defaultDatabaseName = env('DB_DATABASE');

        return "You're using the default database name `$defaultDatabaseName`. This database does not exist.\n\nEdit the `.env` file and use the correct database name in the `DB_DATABASE` key.";
    }

    /** @return array<string, string> */
    public function getDocumentationLinks(): array
    {
        return [
            'Database: Getting Started docs' => 'https://laravel.com/docs/master/database#configuration',
        ];
    }
}
