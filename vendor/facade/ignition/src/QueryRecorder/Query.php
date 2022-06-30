<?php

namespace Facade\Ignition\QueryRecorder;

use Illuminate\Database\Events\QueryExecuted;

class Query
{
    /** @var string */
    protected $sql;

    /** @var float */
    protected $time;

    /** @var string */
    protected $connectionName;

    /** @var null|array */
    protected $bindings;

    /** @var float */
    protected $microtime;

    public static function fromQueryExecutedEvent(QueryExecuted $queryExecuted, bool $reportBindings = false)
    {
        return new static(
            $queryExecuted->sql,
            $queryExecuted->time,
            $queryExecuted->connectionName ?? '',
            $reportBindings ? $queryExecuted->bindings : null
        );
    }

    protected function __construct(
        string $sql,
        float $time,
        string $connectionName,
        ?array $bindings = null,
        ?float $microtime = null
    ) {
        $this->sql = $sql;
        $this->time = $time;
        $this->connectionName = $connectionName;
        $this->bindings = $bindings;
        $this->microtime = $microtime ?? microtime(true);
    }

    public function toArray(): array
    {
        return [
            'sql' => $this->sql,
            'time' => $this->time,
            'connection_name' => $this->connectionName,
            'bindings' => $this->bindings,
            'microtime' => $this->microtime,
        ];
    }
}
