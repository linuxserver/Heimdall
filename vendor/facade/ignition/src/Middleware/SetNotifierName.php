<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;

class SetNotifierName
{
    public const NOTIFIER_NAME = 'Laravel Client';

    public function handle(Report $report, $next)
    {
        $report->notifierName(static::NOTIFIER_NAME);

        return $next($report);
    }
}
