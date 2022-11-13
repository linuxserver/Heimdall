<?php

namespace Facade\Ignition\Context;

use Facade\FlareClient\Context\ContextDetectorInterface;
use Facade\FlareClient\Context\ContextInterface;
use Illuminate\Http\Request;
use Livewire\LivewireManager;

class LaravelContextDetector implements ContextDetectorInterface
{
    public function detectCurrentContext(): ContextInterface
    {
        if (app()->runningInConsole()) {
            return new LaravelConsoleContext($_SERVER['argv'] ?? []);
        }

        $request = app(Request::class);

        if ($this->isRunningLiveWire($request)) {
            return new LivewireRequestContext($request, app(LivewireManager::class));
        }

        return new LaravelRequestContext($request);
    }

    protected function isRunningLiveWire(Request $request)
    {
        return $request->hasHeader('x-livewire') && $request->hasHeader('referer');
    }
}
