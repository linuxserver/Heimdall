<?php

namespace App\Jobs;

use App\Application;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateApps implements ShouldQueue, ShouldBeUnique
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
     * @throws GuzzleException
     */
    public function handle(): void
    {
        Log::debug('Update of all apps triggered!');
        $apps = Application::all('appid')->toArray();

        // We onl update the apps that are actually in use by items
        // 1 sec delay after each update to throttle the requests
        foreach ($apps as $appKey => $app) {
            Application::getApp($app['appid']);
            sleep(1);
        }

        Log::debug('Update of all apps finished!');

        Cache::lock('updateApps')->forceRelease();
    }

    public function failed($exception): void
    {
        Cache::lock('updateApps')->forceRelease();
    }
}
