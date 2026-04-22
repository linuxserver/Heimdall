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
use Throwable;

class UpdateApps implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Bound total attempts so a failed job stops after three retries across
     * worker restarts, independent of the broker's reserved-job recovery path.
     */
    public int $tries = 3;

    /** @var array<int, int> seconds between retries */
    public array $backoff = [30, 60, 120];

    /**
     * Per-attempt wall-clock ceiling. Heavy users with many apps may need
     * a larger value; 60s covers the typical Heimdall deployment.
     */
    public int $timeout = 60;

    /**
     * Expire the ShouldBeUnique lock after 1 hour so a crashed worker does
     * not permanently block future UpdateApps dispatches.
     */
    public int $uniqueFor = 3600;

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

    public function failed(Throwable $exception): void
    {
        Cache::lock('updateApps')->forceRelease();

        Log::error(static::class . ' permanently failed', [
            'exception' => $exception->getMessage(),
        ]);
    }
}
