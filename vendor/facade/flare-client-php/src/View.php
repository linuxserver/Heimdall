<?php

namespace Facade\FlareClient;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class View
{
    /** @var string */
    private $file;

    /** @var array */
    private $data = [];

    public function __construct(string $file, array $data = [])
    {
        $this->file = $file;
        $this->data = $data;
    }

    public static function create(string $file, array $data = []): self
    {
        return new static($file, $data);
    }

    private function dumpViewData($variable): string
    {
        $cloner = new VarCloner();

        $dumper = new HtmlDumper();
        $dumper->setDumpHeader('');

        $output = fopen('php://memory', 'r+b');

        $dumper->dump($cloner->cloneVar($variable)->withMaxDepth(1), $output, [
            'maxDepth' => 1,
            'maxStringLength' => 160,
        ]);

        return stream_get_contents($output, -1, 0);
    }

    public function toArray()
    {
        return [
            'file' => $this->file,
            'data' => array_map([$this, 'dumpViewData'], $this->data),
        ];
    }
}
