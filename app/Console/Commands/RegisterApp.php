<?php

namespace App\Console\Commands;

use App\Application;
use App\SupportedApps;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegisterApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:app {folder} {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a local app to the registry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $folder = $this->argument('folder');
        if ($folder == 'all') {
            $apps = scandir(app_path('SupportedApps'));
            foreach ($apps as $folder) {
                if ($folder == '.' || $folder == '..') {
                    continue;
                }
                $this->addApp($folder);
            }
        } else {
            $this->addApp($folder, $this->option('remove'));
        }
    }

    /**
     * @param $folder
     */
    public function addApp($folder, bool $remove = false): void
    {
        $json = app_path('SupportedApps/'.$folder.'/app.json');

        if (!file_exists($json)) {
            $this->error('Could not find ' . $json);
            return;
        }

        $app = json_decode(file_get_contents($json));

        if (!isset($app->appid)) {
            $this->error('No App ID for - ' . $folder);
            return;
        }

        $exists = Application::find($app->appid);

        if ($exists) {
            if ($remove) {
                $exists->delete();
                $this->info('Application Removed - ' . $app->name . ' - ' . $app->appid);
                return;
            }
            $this->error('Application already registered - ' . $exists->name . ' - ' . $exists->appid);
            return;
        }

        // Doesn't exist so add it
        SupportedApps::saveApp($app, new Application);
        $this->saveIcon($folder, $app->icon);
        $this->info('Application Added - ' . $app->name . ' - ' . $app->appid);
    }

    /**
     * @param $appFolder
     * @param $icon
     */
    private function saveIcon($appFolder, $icon): void
    {
        $iconPath = app_path('SupportedApps/' . $appFolder . '/' . $icon);
        $contents = file_get_contents($iconPath);
        Storage::disk('public')->put('icons/'.$icon, $contents);
    }
}
