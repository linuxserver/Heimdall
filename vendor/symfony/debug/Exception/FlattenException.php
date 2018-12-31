<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Exception;

use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * FlattenException wraps a PHP Error or Exception to be able to serialize it.
 *
 * Basically, this class removes all objects from the trace.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FlattenException
{
    private $message;
    private $code;
    private $previous;
    private $trace;
    private $class;
    private $statusCode;
    private $headers;
    private $file;
    private $line;

    public static function create(\Exception $exception, $statusCode = null, array $headers = array())
    {
        return static::createFromThrowable($exception, $statusCode, $headers);
    }

    public static function createFromThrowable(\Throwable $exception, ?int $statusCode = null, array $headers = array()): self
    {
        $e = new static();
        $e->setMessage($exception->getMessage());
        $e->setCode($exception->getCode());

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = array_merge($headers, $exception->getHeaders());
        } elseif ($exception instanceof RequestExceptionInterface) {
            $statusCode = 400;
        }

        if (null === $statusCode) {
            $statusCode = 500;
        }

        $e->setStatusCode($statusCode);
        $e->setHeaders($headers);
        $e->setTraceFromThrowable($exception);
        $e->setClass($exception instanceof FatalThrowableError ? $exception->getOriginalClassName() : \get_class($exception));
        $e->setFile($exception->getFile());
        $e->setLine($exception->getLine());

        $previous = $exception->getPrevious();

        if ($previous instanceof \Throwable) {
            $e->setPrevious(static::createFromThrowable($previous));
        }

        return $e;
    }

    public function toArray()
    {
        $exceptions = array();
        foreach (array_merge(array($this), $this->getAllPrevious()) as $exception) {
            $exceptions[] = array(
                'message' => $exception->getMessage(),
                'class' => $exception->getClass(),
                'trace' => $exception->getTrace(),
            );
        }

        return $exceptions;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = 'c' === $class[0] && 0 === strpos($class, "class@anonymous\0") ? get_parent_class($class).'@anonymous' : $class;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return $this
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return $this
     */
    public function setMessage($message)
    {
        if (false !== strpos($message, "class@anonymous\0")) {
            $message = preg_replace_callback('/class@anonymous\x00.*?\.php0x?[0-9a-fA-F]++/', function ($m) {
                return \class_exists($m[0], false) ? get_parent_class($m[0]).'@anonymous' : $m[0];
            }, $message);
        }

        $this->message = $message;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @return $this
     */
    public function setPrevious(self $previous)
    {
        $this->previous = $previous;

        return $this;
    }

    public function getAllPrevious()
    {
        $exceptions = array();
        $e = $this;
        while ($e = $e->getPrevious()) {
            $exceptions[] = $e;
        }

        return $exceptions;
    }

    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @deprecated since 4.1, use {@see setTraceFromThrowable()} instead.
     */
    public function setTraceFromException(\Exception $exception)
    {
        @trigger_error(sprintf('The "%s()" method is deprecated since Symfony 4.1, use "setTraceFromThrowable()" instead.', __METHOD__), E_USER_DEPRECATED);

        $this->setTraceFromThrowable($exception);
    }

    public function setTraceFromThrowable(\Throwable $throwable)
    {
        return $this->setTrace($throwable->getTrace(), $throwable->getFile(), $throwable->getLine());
    }

    /**
     * @return $this
     */
    public function setTrace($trace, $file, $line)
    {
        $this->trace = array();
        $this->trace[] = array(
            'namespace' => '',
            'short_class' => '',
            'class' => '',
            'type' => '',
            'function' => '',
            'file' => $file,
            'line' => $line,
            'args' => array(),
        );
        foreach ($trace as $entry) {
            $class = '';
            $namespace = '';
            if (isset($entry['class'])) {
                $parts = explode('\\', $entry['class']);
                $class = array_pop($parts);
                $namespace = implode('\\', $parts);
            }

            $this->trace[] = array(
                'namespace' => $namespace,
                'short_class' => $class,
                'class' => isset($entry['class']) ? $entry['class'] : '',
                'type' => isset($entry['type']) ? $entry['type'] : '',
                'function' => isset($entry['function']) ? $entry['function'] : null,
                'file' => isset($entry['file']) ? $entry['file'] : null,
                'line' => isset($entry['line']) ? $entry['line'] : null,
                'args' => isset($entry['args']) ? $this->flattenArgs($entry['args']) : array(),
            );
        }

        return $this;
    }

    private function flattenArgs($args, $level = 0, &$count = 0)
    {
        $result = array();
        foreach ($args as $key => $value) {
            if (++$count > 1e4) {
                return array('array', '*SKIPPED over 10000 entries*');
            }
            if ($value instanceof \__PHP_Incomplete_Class) {
                // is_object() returns false on PHP<=7.1
                $result[$key] = array('incomplete-object', $this->getClassNameFromIncomplete($value));
            } elseif (\is_object($value)) {
                $result[$key] = array('object', \get_class($value));
            } elseif (\is_array($value)) {
                if ($level > 10) {
                    $result[$key] = array('array', '*DEEP NESTED ARRAY*');
                } else {
                    $result[$key] = array('array', $this->flattenArgs($value, $level + 1, $count));
                }
            } elseif (null === $value) {
                $result[$key] = array('null', null);
            } elseif (\is_bool($value)) {
                $result[$key] = array('boolean', $value);
            } elseif (\is_int($value)) {
                $result[$key] = array('integer', $value);
            } elseif (\is_float($value)) {
                $result[$key] = array('float', $value);
            } elseif (\is_resource($value)) {
                $result[$key] = array('resource', get_resource_type($value));
            } else {
                $result[$key] = array('string', (string) $value);
            }
        }

        return $result;
    }

    private function getClassNameFromIncomplete(\__PHP_Incomplete_Class $value)
    {
        $array = new \ArrayObject($value);

        return $array['__PHP_Incomplete_Class_Name'];
    }
}
