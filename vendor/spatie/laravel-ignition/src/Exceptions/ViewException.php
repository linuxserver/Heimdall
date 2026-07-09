<?php

namespace Spatie\LaravelIgnition\Exceptions;

use ErrorException;

class ViewException extends ErrorException
{
    /** @var array<string, mixed> */
    protected array $viewData = [];

    protected string $view = '';

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function setViewData(array $data): void
    {
        $this->viewData = $data;
    }

    /** @return array<string, mixed> */
    public function getViewData(): array
    {
        return $this->viewData;
    }

    public function getView(): string
    {
        return $this->view;
    }

    public function setView(string $path): void
    {
        $this->view = $path;
    }
}
