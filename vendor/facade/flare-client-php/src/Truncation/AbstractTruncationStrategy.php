<?php

namespace Facade\FlareClient\Truncation;

abstract class AbstractTruncationStrategy implements TruncationStrategy
{
    /** @var ReportTrimmer */
    protected $reportTrimmer;

    public function __construct(ReportTrimmer $reportTrimmer)
    {
        $this->reportTrimmer = $reportTrimmer;
    }
}
