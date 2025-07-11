<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class SuggestUsingMariadbDatabaseSolution implements Solution
{
    use IsProvidedByFlare;

    public function getSolutionTitle(): string
    {
        return 'Database is not a MariaDB database';
    }

    public function getSolutionDescription(): string
    {
        return "Laravel 11 changed the default collation for MySQL and MariaDB. It seems you are trying to use the MariaDB collation `utf8mb4_uca1400_ai_ci` with a MySQL database.\n\nEdit the `.env` file and use the correct database in the `DB_CONNECTION` key.";
    }

    /** @return array<string, string> */
    public function getDocumentationLinks(): array
    {
        return [
            'Database: Getting Started docs' => 'https://laravel.com/docs/master/database#configuration',
        ];
    }
}
