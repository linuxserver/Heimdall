<?php

namespace Spatie\LaravelIgnition\FlareMiddleware;

use Closure;
use Illuminate\Log\Context\Repository;
use Illuminate\Support\Facades\Context;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;

class AddContext implements FlareMiddleware
{
    public function handle(Report $report, Closure $next)
    {
        if (! class_exists(Repository::class)) {
            return $next($report);
        }

        $allContext = Context::all();

        if (count($allContext)) {
            $report->group('laravel_context', $allContext);
        }

        return $next($report);
    }
}
