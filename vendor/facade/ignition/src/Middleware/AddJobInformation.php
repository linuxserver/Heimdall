<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use Facade\Ignition\JobRecorder\JobRecorder;

class AddJobInformation
{
    /** @var \Facade\Ignition\JobRecorder\JobRecorder */
    protected $jobRecorder;

    public function __construct(JobRecorder $jobRecorder)
    {
        $this->jobRecorder = $jobRecorder;
    }

    public function handle(Report $report, $next)
    {
        if ($this->jobRecorder->getJob()) {
            $report->group('job', $this->jobRecorder->toArray());
        }

        return $next($report);
    }
}
