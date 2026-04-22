<?php

namespace Tests\Feature;

use App\Jobs\ProcessApps;
use App\Jobs\UpdateApps;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class QueueFailedHandlerTest extends TestCase
{
    /** @test */
    public function processApps_failed_logs_structured_context(): void
    {
        Log::spy();

        $job = new ProcessApps();
        $exception = new RuntimeException('GitHub API rate limit exceeded');

        $job->failed($exception);

        Log::shouldHaveReceived('error')
            ->once()
            ->with(
                ProcessApps::class . ' permanently failed',
                Mockery::on(function ($context) {
                    return $context['exception_class'] === RuntimeException::class
                        && $context['exception_message'] === 'GitHub API rate limit exceeded'
                        && str_contains($context['file'], 'QueueFailedHandlerTest.php');
                })
            );
    }

    /** @test */
    public function updateApps_failed_logs_structured_context_and_releases_cache_lock(): void
    {
        Log::spy();

        $lockMock = Mockery::mock();
        $lockMock->shouldReceive('forceRelease')->once();
        Cache::shouldReceive('lock')->with('updateApps')->once()->andReturn($lockMock);

        $job = new UpdateApps();
        $exception = new RuntimeException('Something broke');

        $job->failed($exception);

        Log::shouldHaveReceived('error')
            ->once()
            ->with(
                UpdateApps::class . ' permanently failed',
                Mockery::on(function ($context) {
                    return $context['exception_class'] === RuntimeException::class
                        && $context['exception_message'] === 'Something broke'
                        && isset($context['file'])
                        && str_contains($context['file'], ':');
                })
            );
    }
}
