<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Exception;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class ControllerDoesNotReturnResponseException extends \LogicException
{
    public function __construct(string $message, callable $controller, string $file, int $line)
    {
        parent::__construct($message);

        if (!$controllerDefinition = $this->parseControllerDefinition($controller)) {
            return;
        }

        $this->file = $controllerDefinition['file'];
        $this->line = $controllerDefinition['line'];
        $r = new \ReflectionProperty(\Exception::class, 'trace');
        $r->setAccessible(true);
        $r->setValue($this, array_merge(array(
            array(
                'line' => $line,
                'file' => $file,
            ),
        ), $this->getTrace()));
    }

    private function parseControllerDefinition(callable $controller): ?array
    {
        if (\is_string($controller) && false !== strpos($controller, '::')) {
            $controller = explode('::', $controller);
        }

        if (\is_array($controller)) {
            try {
                $r = new \ReflectionMethod($controller[0], $controller[1]);

                return array(
                    'file' => $r->getFileName(),
                    'line' => $r->getEndLine(),
                );
            } catch (\ReflectionException $e) {
                return null;
            }
        }

        if ($controller instanceof \Closure) {
            $r = new \ReflectionFunction($controller);

            return array(
                'file' => $r->getFileName(),
                'line' => $r->getEndLine(),
            );
        }

        if (\is_object($controller)) {
            $r = new \ReflectionClass($controller);

            return array(
                'file' => $r->getFileName(),
                'line' => $r->getEndLine(),
            );
        }

        return null;
    }
}
