<?php

namespace Facade\Ignition\ErrorPage;

use Error;
use ErrorException;
use Whoops\Handler\Handler;

class IgnitionWhoopsHandler extends Handler
{
    /** @var \Facade\Ignition\ErrorPage\ErrorPageHandler */
    protected $errorPageHandler;

    /** @var \Throwable */
    protected $exception;

    public function __construct(ErrorPageHandler $errorPageHandler)
    {
        $this->errorPageHandler = $errorPageHandler;
    }

    public function handle(): ?int
    {
        try {
            $this->errorPageHandler->handle($this->exception);
        } catch (Error $error) {
            // Errors aren't caught by Whoops.
            // Convert the error to an exception and throw again.

            throw new ErrorException(
                $error->getMessage(),
                $error->getCode(),
                1,
                $error->getFile(),
                $error->getLine(),
                $error
            );
        }

        return Handler::QUIT;
    }

    /** @param \Throwable $exception */
    public function setException($exception): void
    {
        $this->exception = $exception;
    }
}
