<?php

namespace Facade\Ignition\Views\Engines;

use Exception;
use Facade\Ignition\Exceptions\ViewException;
use Facade\Ignition\Views\Concerns\CollectsViewExceptions;
use Throwable;

class PhpEngine extends \Illuminate\View\Engines\PhpEngine
{
    use CollectsViewExceptions;

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $this->collectViewData($path, $data);

        return parent::get($path, $data);
    }

    /**
     * Handle a view exception.
     *
     * @param  \Throwable  $baseException
     * @param  int  $obLevel
     *
     * @return void
     *
     * @throws \Throwable
     */
    protected function handleViewException(Throwable $baseException, $obLevel)
    {
        $exception = new ViewException($baseException->getMessage(), 0, 1, $baseException->getFile(), $baseException->getLine(), $baseException);

        $exception->setView($this->getCompiledViewName($baseException->getFile()));
        $exception->setViewData($this->getCompiledViewData($baseException->getFile()));

        parent::handleViewException($exception, $obLevel);
    }
}
