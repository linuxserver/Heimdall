<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Application;
use App\SupportedApps;

class ProcessApps implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $localapps = Application::all();
        $list = json_decode(SupportedApps::getList()->getBody());
        $validapps = [];
        
        foreach($list->apps as $app) {
            $validapps[] = $app->appid;
            $localapp = $localapps->where('appid', $app->appid)->first();

            $application = ($localapp) ? $localapp : new Application;

            if(!file_exists(app_path('SupportedApps/'.className($app->name)))) {
                SupportedApps::getFiles($app);
                SupportedApps::saveApp($app, $application);
            } else {
                // check if there has been an update for this app
                $localapp = $localapps->where('appid', $app->appid)->first();
                if($localapp) {
                    if($localapp->sha !== $app->sha) {
                        SupportedApps::getFiles($app);
                        SupportedApps::saveApp($app, $application);
                    }
                }  else {
                    SupportedApps::getFiles($app);
                    SupportedApps::saveApp($app, $application);
      
                }
            }
        }
        //$delete = Application::whereNotIn('appid', $validapps)->delete(); // delete any apps not in list
        // removed the delete so local apps can be added

    }
}
