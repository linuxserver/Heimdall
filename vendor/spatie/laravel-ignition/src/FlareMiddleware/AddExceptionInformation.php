<?php

namespace Spatie\LaravelIgnition\FlareMiddleware;

use Illuminate\Database\QueryException;
use Spatie\FlareClient\Contracts\ProvidesFlareContext;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;
use Spatie\LaravelIgnition\Exceptions\ViewException;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\HtmlDumper;

class AddExceptionInformation implements FlareMiddleware
{
    public function handle(Report $report, $next)
    {
        $throwable = $report->getThrowable();

        $this->addUserDefinedContext($report);
        $this->addViewContext($report);

        if ($throwable instanceof QueryException) {
            $report->group('exception', [
                'raw_sql' => $throwable->getSql(),
            ]);
        }

        return $next($report);
    }

    private function addViewContext(Report $report): void
    {
        $throwable = $report->getThrowable();

        if (! $throwable instanceof ViewException) {
            return;
        }

        $dumper = new HtmlDumper();

        $report->group('view', [
            'view' => $throwable->getView(),
            'data' => array_map(
                fn (mixed $variable) => $dumper->dumpVariable($variable),
                $throwable->getViewData()
            ),
        ]);
    }

    private function addUserDefinedContext(Report $report): void
    {
        $throwable = $report->getThrowable();

        if ($throwable === null) {
            return;
        }

        if ($throwable instanceof ProvidesFlareContext) {
            return;
        }

        if (! method_exists($throwable, 'context')) {
            return;
        }

        $context = $throwable->context();

        if (! is_array($context)) {
            return;
        }

        $exceptionContextGroup = [];
        foreach ($context as $key => $value) {
            $exceptionContextGroup[$key] = $value;
        }
        $report->group('exception', $exceptionContextGroup);
    }
}
