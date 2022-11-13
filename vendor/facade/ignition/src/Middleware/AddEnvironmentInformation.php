<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;

class AddEnvironmentInformation
{
    public function handle(Report $report, $next)
    {
        $report->frameworkVersion(app()->version());

        $report->group('env', [
            'laravel_version' => app()->version(),
            'laravel_locale' => app()->getLocale(),
            'laravel_config_cached' => app()->configurationIsCached(),
            'php_version' => phpversion(),
        ]);

        return $next($report);
    }
}
