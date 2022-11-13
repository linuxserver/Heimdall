<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use Facade\Ignition\LogRecorder\LogRecorder;

class AddLogs
{
    /** @var \Facade\Ignition\LogRecorder\LogRecorder */
    protected $logRecorder;

    public function __construct(LogRecorder $logRecorder)
    {
        $this->logRecorder = $logRecorder;
    }

    public function handle(Report $report, $next)
    {
        $report->group('logs', $this->logRecorder->getLogMessages());

        return $next($report);
    }
}
