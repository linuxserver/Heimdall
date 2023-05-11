<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DataCollector;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ExceptionDataCollector extends DataCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if (null !== $exception) {
            $this->data = [
                'exception' => FlattenException::createFromThrowable($exception),
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    public function hasException(): bool
    {
        return isset($this->data['exception']);
    }

    /**
     * @return \Exception|FlattenException
     */
    public function getException()
    {
        return $this->data['exception'];
    }

    public function getMessage(): string
    {
        return $this->data['exception']->getMessage();
    }

    public function getCode(): int
    {
        return $this->data['exception']->getCode();
    }

    public function getStatusCode(): int
    {
        return $this->data['exception']->getStatusCode();
    }

    public function getTrace(): array
    {
        return $this->data['exception']->getTrace();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'exception';
    }
}
