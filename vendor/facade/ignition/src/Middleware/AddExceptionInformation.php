<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;
use Illuminate\Database\QueryException;

class AddExceptionInformation
{
    public function handle(Report $report, $next)
    {
        $throwable = $report->getThrowable();

        if (! $throwable instanceof QueryException) {
            return $next($report);
        }

        $report->group('exception', [
            'raw_sql' => $throwable->getSql(),
        ]);

        return $next($report);
    }
}
