<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application;
use App\SupportedApps;

class RegisterApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:app {folder}';

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
     *
     * @return mixed
     */
    public function handle()
    {
        $folder = $this->argument('folder');
        if($folder == 'all') {
            $apps = scandir(app_path('SupportedApps'));
            foreach($apps as $folder) {
                if($folder == '.' || $folder == '..') continue;
                $this->addApp($folder);
            }

        } else {
            $this->addApp($folder);
        }
        
    }

    public function addApp($folder)
    {
        $json = app_path('SupportedApps/'.$folder.'/app.json');
        if(file_exists($json)) {
            $app = json_decode(file_get_contents($json));
            if(isset($app->appid)) {
                $exists = Application::find($app->appid);
                if($exists) {
                    $this->error('Application already registered - '.$exists->name." - ".$exists->appid);
                } else {
                    // Doesn't exist so add it
                    SupportedApps::saveApp($app, new Application);
                    $this->info("Application Added - ".$app->name." - ".$app->appid);
                }
            } else {
                $this->error('No App ID for - '.$folder);
            }
            
        } else {
            $this->error('Could not find '.$json);
        }

    }
}
