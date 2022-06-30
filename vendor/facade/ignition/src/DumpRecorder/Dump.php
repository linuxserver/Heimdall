<?php

namespace Facade\Ignition\DumpRecorder;

class Dump
{
    /** @var string */
    protected $htmlDump;

    /** @var ?string */
    protected $file;

    /** @var ?int */
    protected $lineNumber;

    /** @var float */
    protected $microtime;

    public function __construct(string $htmlDump, ?string $file, ?int $lineNumber, ?float $microtime = null)
    {
        $this->htmlDump = $htmlDump;
        $this->file = $file;
        $this->lineNumber = $lineNumber;
        $this->microtime = $microtime ?? microtime(true);
    }

    public function toArray(): array
    {
        return [
            'html_dump' => $this->htmlDump,
            'file' => $this->file,
            'line_number' => $this->lineNumber,
            'microtime' => $this->microtime,
        ];
    }
}
