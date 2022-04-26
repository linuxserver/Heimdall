<?php

namespace Facade\Ignition\Support;

use Facade\FlareClient\Report;
use Illuminate\Support\Arr;

class SentReports
{
    /** @var array<int, Report> */
    protected $reports = [];

    public function add(Report $report): self
    {
        $this->reports[] = $report;

        return $this;
    }

    public function all(): array
    {
        return $this->reports;
    }

    public function uuids(): array
    {
        return array_map(function (Report $report) {
            return $report->trackingUuid();
        }, $this->reports);
    }

    public function urls(): array
    {
        return array_map(function (string $trackingUuid) {
            return "https://flareapp.io/tracked-occurrence/{$trackingUuid}";
        }, $this->uuids());
    }

    public function latestUuid(): ?string
    {
        if (! $latestReport = Arr::last($this->reports)) {
            return null;
        }

        return $latestReport->trackingUuid();
    }

    public function latestUrl(): ?string
    {
        return Arr::last($this->urls());
    }

    public function clear()
    {
        $this->reports = [];
    }
}
