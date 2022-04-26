<?php

namespace Laravel\Sail\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:install
                {--with= : The services that should be included in the installation}
                {--devcontainer : Create a .devcontainer configuration directory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Sail\'s default Docker Compose file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('with')) {
            $services = $this->option('with') == 'none' ? [] : explode(',', $this->option('with'));
        } elseif ($this->option('no-interaction')) {
            $services = ['mysql', 'redis', 'selenium', 'mailhog'];
        } else {
            $services = $this->gatherServicesWithSymfonyMenu();
        }

        $this->buildDockerCompose($services);
        $this->replaceEnvVariables($services);

        if ($this->option('devcontainer')) {
            $this->installDevContainer();
        }

        $this->info('Sail scaffolding installed successfully.');
    }

    /**
     * Gather the desired Sail services using a Symfony menu.
     *
     * @return array
     */
    protected function gatherServicesWithSymfonyMenu()
    {
        return $this->choice('Which services would you like to install?', [
             'mysql',
             'pgsql',
             'mariadb',
             'redis',
             'memcached',
             'meilisearch',
             'minio',
             'mailhog',
             'selenium',
         ], 0, null, true);
    }

    /**
     * Build the Docker Compose file.
     *
     * @param  array  $services
     * @return void
     */
    protected function buildDockerCompose(array $services)
    {
        $depends = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'meilisearch', 'minio', 'selenium']);
            })->map(function ($service) {
                return "            - {$service}";
            })->whenNotEmpty(function ($collection) {
                return $collection->prepend('depends_on:');
            })->implode("\n");

        $stubs = rtrim(collect($services)->map(function ($service) {
            return file_get_contents(__DIR__ . "/../../stubs/{$service}.stub");
        })->implode(''));

        $volumes = collect($services)
            ->filter(function ($service) {
                return in_array($service, ['mysql', 'pgsql', 'mariadb', 'redis', 'meilisearch', 'minio']);
            })->map(function ($service) {
                return "    sail-{$service}:\n        driver: local";
            })->whenNotEmpty(function ($collection) {
                return $collection->prepend('volumes:');
            })->implode("\n");

        $dockerCompose = file_get_contents(__DIR__ . '/../../stubs/docker-compose.stub');

        $dockerCompose = str_replace('{{depends}}', empty($depends) ? '' : '        '.$depends, $dockerCompose);
        $dockerCompose = str_replace('{{services}}', $stubs, $dockerCompose);
        $dockerCompose = str_replace('{{volumes}}', $volumes, $dockerCompose);

        // Replace Selenium with ARM base container on Apple Silicon...
        if (in_array('selenium', $services) && php_uname('m') === 'arm64') {
            $dockerCompose = str_replace('selenium/standalone-chrome', 'seleniarm/standalone-chromium', $dockerCompose);
        }

        // Remove empty lines...
        $dockerCompose = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $dockerCompose);

        file_put_contents($this->laravel->basePath('docker-compose.yml'), $dockerCompose);
    }

    /**
     * Replace the Host environment variables in the app's .env file.
     *
     * @param  array  $services
     * @return void
     */
    protected function replaceEnvVariables(array $services)
    {
        $environment = file_get_contents($this->laravel->basePath('.env'));

        if (in_array('pgsql', $services)) {
            $environment = str_replace('DB_CONNECTION=mysql', "DB_CONNECTION=pgsql", $environment);
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=pgsql", $environment);
            $environment = str_replace('DB_PORT=3306', "DB_PORT=5432", $environment);
        } elseif (in_array('mariadb', $services)) {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mariadb", $environment);
        } else {
            $environment = str_replace('DB_HOST=127.0.0.1', "DB_HOST=mysql", $environment);
        }

        $environment = str_replace('DB_USERNAME=root', "DB_USERNAME=sail", $environment);
        $environment = preg_replace("/DB_PASSWORD=(.*)/", "DB_PASSWORD=password", $environment);

        $environment = str_replace('MEMCACHED_HOST=127.0.0.1', 'MEMCACHED_HOST=memcached', $environment);
        $environment = str_replace('REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis', $environment);

        if (in_array('meilisearch', $services)) {
            $environment .= "\nSCOUT_DRIVER=meilisearch";
            $environment .= "\nMEILISEARCH_HOST=http://meilisearch:7700\n";
        }

        file_put_contents($this->laravel->basePath('.env'), $environment);
    }

    /**
     * Install the devcontainer.json configuration file.
     *
     * @return void
     */
    protected function installDevContainer()
    {
        if (! is_dir($this->laravel->basePath('.devcontainer'))) {
            mkdir($this->laravel->basePath('.devcontainer'), 0755, true);
        }

        file_put_contents(
            $this->laravel->basePath('.devcontainer/devcontainer.json'),
            file_get_contents(__DIR__.'/../../stubs/devcontainer.stub')
        );

        $environment = file_get_contents($this->laravel->basePath('.env'));

        $environment .= "\nWWWGROUP=1000";
        $environment .= "\nWWWUSER=1000\n";

        file_put_contents($this->laravel->basePath('.env'), $environment);
    }
}
