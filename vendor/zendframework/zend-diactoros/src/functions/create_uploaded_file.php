<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Diactoros;

use InvalidArgumentException;

/**
 * Create an uploaded file instance from an array of values.
 *
 * @param array $spec A single $_FILES entry.
 * @return UploadedFile
 * @throws InvalidArgumentException if one or more of the tmp_name, size,
 *     or error keys are missing from $spec.
 */
function createUploadedFile(array $spec)
{
    if (! isset($spec['tmp_name'])
        || ! isset($spec['size'])
        || ! isset($spec['error'])
    ) {
        throw new InvalidArgumentException(sprintf(
            '$spec provided to %s MUST contain each of the keys "tmp_name",'
            . ' "size", and "error"; one or more were missing',
            __FUNCTION__
        ));
    }

    return new UploadedFile(
        $spec['tmp_name'],
        $spec['size'],
        $spec['error'],
        isset($spec['name']) ? $spec['name'] : null,
        isset($spec['type']) ? $spec['type'] : null
    );
}
