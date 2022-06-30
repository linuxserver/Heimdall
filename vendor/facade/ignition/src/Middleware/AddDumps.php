<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use Facade\Ignition\DumpRecorder\DumpRecorder;

class AddDumps
{
    /** @var \Facade\Ignition\DumpRecorder\DumpRecorder */
    protected $dumpRecorder;

    public function __construct(DumpRecorder $dumpRecorder)
    {
        $this->dumpRecorder = $dumpRecorder;
    }

    public function handle(Report $report, $next)
    {
        $report->group('dumps', $this->dumpRecorder->getDumps());

        return $next($report);
    }
}
