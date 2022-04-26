<?php

namespace Facade\Ignition\ErrorPage;

use Exception;
use Facade\Ignition\Exceptions\ViewException;

class Renderer
{
    /** @var string */
    protected $viewPath;

    public function __construct(string $viewPath)
    {
        $this->viewPath = $this->formatPath($viewPath);
    }

    public function render(string $viewName, array $_data): string
    {
        ob_start();

        $viewFile = "{$this->viewPath}/{$viewName}.php";

        try {
            extract($_data, EXTR_OVERWRITE);

            include $viewFile;
        } catch (Exception $exception) {
            $viewException = new ViewException($exception->getMessage());
            $viewException->setView($viewFile);
            $viewException->setViewData($_data);

            throw $viewException;
        }

        return ob_get_clean();
    }

    protected function formatPath(string $path): string
    {
        return preg_replace('/(?:\/)+$/u', '', $path).'/';
    }
}
