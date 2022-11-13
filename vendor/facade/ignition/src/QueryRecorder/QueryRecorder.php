<?php

namespace Facade\Ignition\QueryRecorder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;

class QueryRecorder
{
    /** @var \Facade\Ignition\QueryRecorder\Query|[] */
    protected $queries = [];

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /** @var bool */
    private $reportBindings;

    /** @var int|null */
    private $maxQueries;

    public function __construct(
        Application $app,
        bool $reportBindings = true,
        ?int $maxQueries = null
    ) {
        $this->app = $app;
        $this->reportBindings = $reportBindings;
        $this->maxQueries = $maxQueries;
    }

    public function register()
    {
        $this->app['events']->listen(QueryExecuted::class, [$this, 'record']);

        return $this;
    }

    public function record(QueryExecuted $queryExecuted)
    {
        $this->queries[] = Query::fromQueryExecutedEvent($queryExecuted, $this->reportBindings);

        if (is_int($this->maxQueries)) {
            $this->queries = array_slice($this->queries, -$this->maxQueries);
        }
    }

    public function getQueries(): array
    {
        $queries = [];

        foreach ($this->queries as $query) {
            $queries[] = $query->toArray();
        }

        return $queries;
    }

    public function reset()
    {
        $this->queries = [];
    }

    public function getReportBindings(): bool
    {
        return $this->reportBindings;
    }

    public function setReportBindings(bool $reportBindings): self
    {
        $this->reportBindings = $reportBindings;

        return $this;
    }

    public function getMaxQueries(): ?int
    {
        return $this->maxQueries;
    }

    public function setMaxQueries(?int $maxQueries): self
    {
        $this->maxQueries = $maxQueries;

        return $this;
    }
}
