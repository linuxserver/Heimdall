<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Exception;

use RuntimeException;
use Throwable;

class UploadedFileAlreadyMovedException extends RuntimeException implements ExceptionInterface
{
    public function __construct(
        string $message = 'Cannot retrieve stream after it has already moved',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
