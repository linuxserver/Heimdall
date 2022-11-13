<?php

namespace Facade\Ignition\ErrorPage;

use Illuminate\Contracts\Foundation\ExceptionRenderer;

/** @psalm-suppress UndefinedClass */
class IgnitionExceptionRenderer implements ExceptionRenderer
{
    /** @var \Facade\Ignition\ErrorPage\ErrorPageHandler */
    protected $errorPageHandler;

    public function __construct(ErrorPageHandler $errorPageHandler)
    {
        $this->errorPageHandler = $errorPageHandler;
    }

    public function render($throwable)
    {
        ob_start();

        $this->errorPageHandler->handle($throwable);

        return ob_get_clean();
    }
}
