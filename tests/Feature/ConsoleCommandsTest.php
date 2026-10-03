<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

/**
 * Guards registration of the app's own artisan commands.
 *
 * AppServiceProvider::boot() can call Artisan (migrate on a fresh or upgraded
 * database, key:generate, storage:link). An Artisan call made while providers
 * are still booting loads the console kernel's commands before the framework
 * has added app/Console/Commands to its discovery paths, so path discovery
 * alone silently loses register:app for that process (#1606). The test
 * database is always fresh, so every test boot goes through that migrate
 * call.
 */
class ConsoleCommandsTest extends TestCase
{
    public function test_register_app_command_is_registered(): void
    {
        $this->assertArrayHasKey('register:app', $this->app->make(Kernel::class)->all());
    }
}
