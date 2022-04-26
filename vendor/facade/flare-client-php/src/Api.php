<?php

namespace Facade\FlareClient;

use Exception;
use Facade\FlareClient\Http\Client;
use Facade\FlareClient\Truncation\ReportTrimmer;

class Api
{
    /** @var \Facade\FlareClient\Http\Client */
    protected $client;

    /** @var bool */
    public static $sendInBatches = true;

    /** @var array */
    protected $queue = [];

    public function __construct(Client $client)
    {
        $this->client = $client;

        register_shutdown_function([$this, 'sendQueuedReports']);
    }

    public static function sendReportsInBatches(bool $batchSending = true)
    {
        static::$sendInBatches = $batchSending;
    }

    public function report(Report $report)
    {
        try {
            if (static::$sendInBatches) {
                $this->addReportToQueue($report);
            } else {
                $this->sendReportToApi($report);
            }
        } catch (Exception $e) {
            //
        }
    }

    public function sendTestReport(Report $report)
    {
        $this->sendReportToApi($report);
    }

    protected function addReportToQueue(Report $report)
    {
        $this->queue[] = $report;
    }

    public function sendQueuedReports()
    {
        try {
            foreach ($this->queue as $report) {
                $this->sendReportToApi($report);
            }
        } catch (Exception $e) {
            //
        } finally {
            $this->queue = [];
        }
    }

    protected function sendReportToApi(Report $report)
    {
        $this->client->post('reports', $this->truncateReport($report->toArray()));
    }

    protected function truncateReport(array $payload): array
    {
        return (new ReportTrimmer())->trim($payload);
    }
}
