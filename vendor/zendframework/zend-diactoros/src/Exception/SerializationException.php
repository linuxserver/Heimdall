<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Exception;

use UnexpectedValueException;

class SerializationException extends UnexpectedValueException implements ExceptionInterface
{
    public static function forInvalidRequestLine() : self
    {
        return new self('Invalid request line detected');
    }

    public static function forInvalidStatusLine() : self
    {
        return new self('No status line detected');
    }
}
