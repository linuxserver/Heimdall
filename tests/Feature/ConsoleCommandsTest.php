<?php

namespace Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

/**
 * Guards against #1606: an Artisan call in AppServiceProvider::boot() dropped
 * the commands in app/Console/Commands. The test database is always fresh, so
 * every test boot runs the boot-time migrate that triggers it.
 */
class ConsoleCommandsTest extends TestCase
{
    public function test_every_command_in_app_console_commands_is_registered(): void
    {
        $registered = array_map('get_class', $this->app->make(Kernel::class)->all());

        $files = glob(app_path('Console/Commands/*.php'));
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $class = 'App\\Console\\Commands\\'.basename($file, '.php');
            if (is_subclass_of($class, Command::class)) {
                $this->assertContains($class, $registered);
            }
        }
    }
}
