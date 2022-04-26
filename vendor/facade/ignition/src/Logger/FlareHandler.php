<?php

namespace Facade\Ignition\Logger;

use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Facade\Ignition\Ignition;
use Facade\Ignition\Support\SentReports;
use Facade\Ignition\Tabs\Tab;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Throwable;

class FlareHandler extends AbstractProcessingHandler
{
    /** @var \Facade\FlareClient\Flare */
    protected $flare;

    /** @var \Facade\Ignition\Support\SentReports */
    protected $sentReports;

    protected $minimumReportLogLevel = Logger::ERROR;

    public function __construct(Flare $flare, SentReports $sentReports, $level = Logger::DEBUG, $bubble = true)
    {
        $this->flare = $flare;

        $this->sentReports = $sentReports;

        parent::__construct($level, $bubble);
    }

    public function setMinimumReportLogLevel(int $level)
    {
        if (! in_array($level, Logger::getLevels())) {
            throw new \InvalidArgumentException('The given minimum log level is not supported.');
        }

        $this->minimumReportLogLevel = $level;
    }

    protected function write(array $record): void
    {
        if (! $this->shouldReport($record)) {
            return;
        }

        if ($this->hasException($record)) {
            /** @var Throwable $throwable */
            $throwable = $record['context']['exception'];

            collect(Ignition::$tabs)
                ->each(function (Tab $tab) use ($throwable) {
                    $tab->beforeRenderingErrorPage($this->flare, $throwable);
                });

            $report = $this->flare->report($record['context']['exception']);

            if ($report) {
                $this->sentReports->add($report);
            }

            return;
        }

        if (config('flare.send_logs_as_events')) {
            if ($this->hasValidLogLevel($record)) {
                $this->flare->reportMessage(
                    $record['message'],
                    'Log ' . Logger::getLevelName($record['level']),
                    function (Report $flareReport) use ($record) {
                        foreach ($record['context'] as $key => $value) {
                            $flareReport->context($key, $value);
                        }
                    }
                );
            }
        }
    }

    protected function shouldReport(array $report): bool
    {
        if (! config('flare.key')) {
            return false;
        }

        return $this->hasException($report) || $this->hasValidLogLevel($report);
    }

    protected function hasException(array $report): bool
    {
        $context = $report['context'];

        return isset($context['exception']) && $context['exception'] instanceof Throwable;
    }

    protected function hasValidLogLevel(array $report): bool
    {
        return $report['level'] >= $this->minimumReportLogLevel;
    }
}
