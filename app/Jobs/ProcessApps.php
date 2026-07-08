<?php

namespace App\Jobs;

use App\Application;
use App\Item;
use App\SupportedApps;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessApps implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Most failures here are GitHub rate-limit responses; retries inside the
     * same window do not help, so a single attempt is enough.
     */
    public int $tries = 1;

    /**
     * Expire the ShouldBeUnique lock after 10 minutes so a crashed worker
     * does not permanently block future ProcessApps dispatches.
     */
    public int $uniqueFor = 600;

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
        Log::debug('Process Apps dispatched');
        $localapps = Application::whereNull('class')->get();
        $json = SupportedApps::getList()->getBody();

        Storage::disk('local')->put('supportedapps.json', $json);

        foreach ($localapps as $app) {
            $app->class = $app->class();
            $app->save();
        }

        $items = Item::whereNotNull('class')->get();
        foreach ($items as $item) {
            if (! file_exists(app_path('SupportedApps/'.Item::nameFromClass($item->class)))) {
                $app = Application::where('class', $item->class)->first();
                if ($app) {
                    Application::getApp($app->appid);
                }
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error(static::class . ' permanently failed', [
            'exception_class' => $exception::class,
            'exception_message' => $exception->getMessage(),
            'file' => $exception->getFile() . ':' . $exception->getLine(),
        ]);
    }
}
