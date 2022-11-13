<?php

namespace Facade\Ignition\Tabs;

use Facade\FlareClient\Flare;
use Illuminate\Support\Str;
use JsonSerializable;
use Throwable;

abstract class Tab implements JsonSerializable
{
    public $scripts = [];

    public $styles = [];

    /** @var \Facade\FlareClient\Flare */
    protected $flare;

    /** @var Throwable */
    protected $throwable;

    public function __construct()
    {
        $this->registerAssets();
    }

    public function name(): string
    {
        return Str::studly(class_basename(get_called_class()));
    }

    public function component(): string
    {
        return Str::snake(class_basename(get_called_class()), '-');
    }

    public function beforeRenderingErrorPage(Flare $flare, Throwable $throwable)
    {
        $this->flare = $flare;

        $this->throwable = $throwable;
    }

    public function script(string $name, string $path)
    {
        $this->scripts[$name] = $path;

        return $this;
    }

    public function style(string $name, string $path)
    {
        $this->styles[$name] = $path;

        return $this;
    }

    abstract protected function registerAssets();

    public function meta(): array
    {
        return [];
    }

    public function jsonSerialize()
    {
        return [
            'title' => $this->name(),
            'component' => $this->component(),
            'props' => [
                'meta' => $this->meta(),
            ],
        ];
    }
}
