<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->guardAgainstRealDatabase();
    }

    /**
     * Refuse to run the test suite against anything other than an
     * in-memory SQLite database.
     *
     * The suite uses RefreshDatabase (migrate:fresh), which would wipe
     * whatever database it is pointed at. The only supported test
     * configuration for this repo is sqlite + ':memory:'. Any other
     * connection (a real sqlite file, mysql, pgsql, ...) is rejected so
     * we can never destroy real data.
     */
    private function guardAgainstRealDatabase(): void
    {
        $default = config('database.default');
        $driver = config("database.connections.{$default}.driver");
        $database = config("database.connections.{$default}.database");

        if ($driver === 'sqlite' && $database === ':memory:') {
            return;
        }

        throw new \RuntimeException(sprintf(
            'Refusing to run tests: the default database connection (%s) is '
            . 'driver "%s" pointing at "%s". Tests only run against an '
            . 'in-memory SQLite database (driver "sqlite", database ":memory:") '
            . 'to avoid wiping real data via RefreshDatabase. Check phpunit.xml '
            . 'DB_CONNECTION/DB_DATABASE overrides.',
            $default,
            $driver,
            is_scalar($database) ? (string) $database : gettype($database)
        ));
    }
}
