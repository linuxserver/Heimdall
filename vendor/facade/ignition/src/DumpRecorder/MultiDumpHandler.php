<?php

namespace Facade\Ignition\DumpRecorder;

class MultiDumpHandler
{
    /** @var array */
    protected $handlers = [];

    public function dump($value)
    {
        foreach ($this->handlers as $handler) {
            $handler($value);
        }
    }

    public function addHandler(callable $callable = null): self
    {
        $this->handlers[] = $callable;

        return $this;
    }
}
