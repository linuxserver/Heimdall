<?php

/**
 * File for ErrorHandler utility class.
 * @package Phrity > Util > ErrorHandler
 */

namespace Phrity\Util;

use Closure;
use ErrorException;
use Throwable;

/**
 * ErrorHandler utility class.
 * Allows catching and resolving errors inline.
 */
class ErrorHandler
{
    /* ----------------- Public methods ---------------------------------------------- */

    /**
     * Set error handler to run until removed.
     * @param Closure|Throwable|null $handling
     *   - If null, handler will throw ErrorException
     *   - If Throwable $t, throw $t with ErrorException attached as previous
     *   - If callable, will invoke callback with ErrorException as argument
     * @param int $levels Error levels to catch, all errors by default
     * @return (callable(): mixed)|null Previously registered error handler, if any
     */
    public function set(Closure|Throwable|null $handling = null, int $levels = E_ALL): callable|null
    {
        return set_error_handler($this->getHandler($handling), $levels);
    }

    /**
     * Remove error handler.
     * @return bool True if removed
     */
    public function restore(): bool
    {
        return restore_error_handler();
    }

    /**
     * Run code with error handling, breaks on first encountered error.
     * @param callable $callback The code to run
     * @param Closure|Throwable|null $handling
     *   - If null, handler will throw ErrorException
     *   - If Throwable $t, throw $t with ErrorException attached as previous
     *   - If callable, will invoke callback with ErrorException as argument
     * @param int $levels Error levels to catch, all errors by default
     * @return mixed Return what $callback returns, or what $handling retuns on error
     */
    public function with(callable $callback, Closure|Throwable|null $handling = null, int $levels = E_ALL): mixed
    {
        $error = null;
        $result = null;
        try {
            $this->set(null, $levels);
            $result = $callback();
        } catch (ErrorException $e) {
            $error = $this->handle($handling, $e);
        } finally {
            $this->restore();
        }
        return $error ?? $result;
    }

    /**
     * Run code with error handling, comletes code before handling errors
     * @param callable $callback The code to run
     * @param Closure|Throwable|null $handling
     *   - If null, handler will throw ErrorException
     *   - If Throwable $t, throw $t with ErrorException attached as previous
     *   - If callable, will invoke callback with ErrorException as argument
     * @param int $levels Error levels to catch, all errors by default
     * @return mixed Return what $callback returns, or what $handling retuns on error
     */
    public function withAll(callable $callback, Closure|Throwable|null $handling = null, int $levels = E_ALL): mixed
    {
        $errors = [];
        $this->set(function (ErrorException $e) use (&$errors) {
            $errors[] = $e;
        }, $levels);
        $result = $callback();
        $this->restore();
        $error = empty($errors) ? null : $this->handle($handling, $errors, $result);
        return $error ?? $result;
    }


    /* ----------------- Private helpers --------------------------------------------- */

    // Get handler function
    private function getHandler(Closure|Throwable|null $handling): Closure
    {
        return function ($severity, $message, $file, $line) use ($handling) {
            $error = new ErrorException($message, 0, $severity, $file, $line);
            $this->handle($handling, $error);
        };
    }

    /**
     * Handle error according to $handlig type
     * @param Closure|Throwable|null $handling
     * @param ErrorException|non-empty-array<ErrorException> $error
     * @mixed $result
     * @return mixed
     * @throws Throwable
     */
    private function handle(Closure|Throwable|null $handling, ErrorException|array $error, mixed $result = null): mixed
    {
        if (is_callable($handling)) {
            return $handling($error, $result);
        }
        if (is_array($error)) {
            $error = array_shift($error);
        }
        if ($handling instanceof Throwable) {
            try {
                /* @phpstan-ignore finally.exitPoint */
                throw $error;
            } finally {
                /* @phpstan-ignore finally.exitPoint */
                throw $handling;
            }
        }
        throw $error;
    }
}
