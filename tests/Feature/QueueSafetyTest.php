<?php

namespace Tests\Feature;

use App\Jobs\ProcessApps;
use App\Jobs\UpdateApps;
use Tests\TestCase;

class QueueSafetyTest extends TestCase
{
    /** @test */
    public function updateApps_has_bounded_retry_properties(): void
    {
        $job = new UpdateApps();

        $this->assertSame(3, $job->tries);
        $this->assertSame([30, 60, 120], $job->backoff);
        $this->assertSame(60, $job->timeout);
        $this->assertSame(3600, $job->uniqueFor);
    }

    /** @test */
    public function processApps_has_bounded_retry_properties(): void
    {
        $job = new ProcessApps();

        $this->assertSame(3, $job->tries);
        $this->assertSame([30, 60, 120], $job->backoff);
        $this->assertSame(60, $job->timeout);
        $this->assertSame(3600, $job->uniqueFor);
    }

    /** @test */
    public function both_jobs_expose_a_failed_method(): void
    {
        $this->assertTrue(method_exists(UpdateApps::class, 'failed'));
        $this->assertTrue(method_exists(ProcessApps::class, 'failed'));
    }
}
