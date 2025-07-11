<?php

namespace Spatie\ErrorSolutions\Solutions\Laravel;

use Spatie\ErrorSolutions\Contracts\Solution;
use Spatie\ErrorSolutions\Solutions\Concerns\IsProvidedByFlare;

class SuggestUsingMysql8DatabaseSolution implements Solution
{
    use IsProvidedByFlare;

    public function getSolutionTitle(): string
    {
        return 'Database is not a MySQL 8 database';
    }

    public function getSolutionDescription(): string
    {
        return "Laravel 11 changed the default collation for MySQL and MariaDB. It seems you are trying to use the MySQL 8 collation `utf8mb4_0900_ai_ci` with a MariaDB or MySQL 5.7 database.\n\nEdit the `.env` file and use the correct database in the `DB_CONNECTION` key.";
    }

    /** @return array<string, string> */
    public function getDocumentationLinks(): array
    {
        return [
            'Database: Getting Started docs' => 'https://laravel.com/docs/master/database#configuration',
        ];
    }
}
