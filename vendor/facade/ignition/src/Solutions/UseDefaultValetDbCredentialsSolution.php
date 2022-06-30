<?php

namespace Facade\Ignition\Solutions;

use Facade\IgnitionContracts\RunnableSolution;
use Illuminate\Support\Str;

class UseDefaultValetDbCredentialsSolution implements RunnableSolution
{
    public function getSolutionActionDescription(): string
    {
        return 'Pressing the button below will change `DB_USER` and `DB_PASSWORD` in your `.env` file.';
    }

    public function getRunButtonText(): string
    {
        return 'Use default Valet credentials';
    }

    public function getSolutionTitle(): string
    {
        return 'Could not connect to database';
    }

    public function run(array $parameters = [])
    {
        if (! file_exists(base_path('.env'))) {
            return;
        }

        $this->ensureLineExists('DB_USERNAME', 'root');
        $this->ensureLineExists('DB_PASSWORD', '');
    }

    protected function ensureLineExists(string $key, string $value)
    {
        $envPath = base_path('.env');

        $envLines = array_map(function (string $envLine) use ($value, $key) {
            return Str::startsWith($envLine, $key)
                ? "{$key}={$value}".PHP_EOL
                : $envLine;
        }, file($envPath));

        file_put_contents($envPath, implode('', $envLines));
    }

    public function getDocumentationLinks(): array
    {
        return [];
    }

    public function getRunParameters(): array
    {
        return [
            'Valet documentation' => 'https://laravel.com/docs/master/valet',
        ];
    }

    public function getSolutionDescription(): string
    {
        return 'You seem to be using Valet, but the .env file does not contain the right default database credentials.';
    }
}
