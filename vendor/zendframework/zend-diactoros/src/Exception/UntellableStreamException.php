<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Exception;

use RuntimeException;

class UntellableStreamException extends RuntimeException implements ExceptionInterface
{
    public static function dueToMissingResource() : self
    {
        return new self('No resource available; cannot tell position');
    }

    public static function dueToPhpError() : self
    {
        return new self('Error occurred during tell operation');
    }

    public static function forCallbackStream() : self
    {
        return new self('Callback streams cannot tell position');
    }
}
