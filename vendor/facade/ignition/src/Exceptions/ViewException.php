<?php

namespace Facade\Ignition\Exceptions;

use ErrorException;
use Facade\FlareClient\Contracts\ProvidesFlareContext;
use Facade\Ignition\DumpRecorder\HtmlDumper;

class ViewException extends ErrorException implements ProvidesFlareContext
{
    /** @var array */
    protected $viewData = [];

    /** @var string */
    protected $view = '';

    public function setViewData(array $data)
    {
        $this->viewData = $data;
    }

    public function getViewData(): array
    {
        return $this->viewData;
    }

    public function setView(string $path)
    {
        $this->view = $path;
    }

    protected function dumpViewData($variable): string
    {
        return (new HtmlDumper())->dumpVariable($variable);
    }

    public function context(): array
    {
        $context = [
            'view' => [
                'view' => $this->view,
            ],
        ];

        if (config('flare.reporting.report_view_data')) {
            $context['view']['data'] = array_map([$this, 'dumpViewData'], $this->viewData);
        }

        return $context;
    }
}
