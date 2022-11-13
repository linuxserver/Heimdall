<?php

namespace Facade\Ignition\DumpRecorder;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper as BaseHtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

class DumpRecorder
{
    protected $dumps = [];

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register(): self
    {
        $multiDumpHandler = new MultiDumpHandler();

        $this->app->singleton(MultiDumpHandler::class, function () use ($multiDumpHandler) {
            return $multiDumpHandler;
        });

        $previousHandler = VarDumper::setHandler(function ($var) use ($multiDumpHandler) {
            $multiDumpHandler->dump($var);
        });

        if ($previousHandler) {
            $multiDumpHandler->addHandler($previousHandler);
        } else {
            $multiDumpHandler->addHandler($this->getDefaultHandler());
        }

        $multiDumpHandler->addHandler(function ($var) {
            (new DumpHandler($this))->dump($var);
        });

        return $this;
    }

    public function record(Data $data)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8);
        $file = (string)Arr::get($backtrace, '6.file');
        $lineNumber = (int)Arr::get($backtrace, '6.line');

        if (! Arr::exists($backtrace, '7.class') && (string)Arr::get($backtrace, '7.function') === 'ddd') {
            $file = (string)Arr::get($backtrace, '7.file');
            $lineNumber = (int)Arr::get($backtrace, '7.line');
        }

        $htmlDump = (new HtmlDumper())->dump($data);

        $this->dumps[] = new Dump($htmlDump, $file, $lineNumber);
    }

    public function getDumps(): array
    {
        return $this->toArray();
    }

    public function reset()
    {
        $this->dumps = [];
    }

    public function toArray(): array
    {
        $dumps = [];

        foreach ($this->dumps as $dump) {
            $dumps[] = $dump->toArray();
        }

        return $dumps;
    }

    protected function getDefaultHandler()
    {
        return function ($value) {
            $data = (new VarCloner())->cloneVar($value);

            $this->getDumper()->dump($data);
        };
    }

    protected function getDumper()
    {
        if (isset($_SERVER['VAR_DUMPER_FORMAT'])) {
            if ($_SERVER['VAR_DUMPER_FORMAT'] === 'html') {
                return new BaseHtmlDumper();
            }

            return new CliDumper();
        }

        if (in_array(PHP_SAPI, ['cli', 'phpdbg']) && ! isset($_SERVER['LARAVEL_OCTANE'])) {
            return new CliDumper() ;
        }

        return new BaseHtmlDumper();
    }
}
