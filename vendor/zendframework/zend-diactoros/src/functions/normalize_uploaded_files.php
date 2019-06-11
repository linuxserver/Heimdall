<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use Psr\Http\Message\UploadedFileInterface;

use function is_array;

/**
 * Normalize uploaded files
 *
 * Transforms each value into an UploadedFile instance, and ensures that nested
 * arrays are normalized.
 *
 * @return UploadedFileInterface[]
 * @throws Exception\InvalidArgumentException for unrecognized values
 */
function normalizeUploadedFiles(array $files) : array
{
    /**
     * Traverse a nested tree of uploaded file specifications.
     *
     * @param string[]|array[] $tmpNameTree
     * @param int[]|array[] $sizeTree
     * @param int[]|array[] $errorTree
     * @param string[]|array[]|null $nameTree
     * @param string[]|array[]|null $typeTree
     * @return UploadedFile[]|array[]
     */
    $recursiveNormalize = function (
        array $tmpNameTree,
        array $sizeTree,
        array $errorTree,
        array $nameTree = null,
        array $typeTree = null
    ) use (&$recursiveNormalize) : array {
        $normalized = [];
        foreach ($tmpNameTree as $key => $value) {
            if (is_array($value)) {
                // Traverse
                $normalized[$key] = $recursiveNormalize(
                    $tmpNameTree[$key],
                    $sizeTree[$key],
                    $errorTree[$key],
                    isset($nameTree[$key]) ? $nameTree[$key] : null,
                    isset($typeTree[$key]) ? $typeTree[$key] : null
                );
                continue;
            }
            $normalized[$key] = createUploadedFile([
                'tmp_name' => $tmpNameTree[$key],
                'size' => $sizeTree[$key],
                'error' => $errorTree[$key],
                'name' => isset($nameTree[$key]) ? $nameTree[$key] : null,
                'type' => isset($typeTree[$key]) ? $typeTree[$key] : null
            ]);
        }
        return $normalized;
    };

    /**
     * Normalize an array of file specifications.
     *
     * Loops through all nested files (as determined by receiving an array to the
     * `tmp_name` key of a `$_FILES` specification) and returns a normalized array
     * of UploadedFile instances.
     *
     * This function normalizes a `$_FILES` array representing a nested set of
     * uploaded files as produced by the php-fpm SAPI, CGI SAPI, or mod_php
     * SAPI.
     *
     * @param array $files
     * @return UploadedFile[]
     */
    $normalizeUploadedFileSpecification = function (array $files = []) use (&$recursiveNormalize) : array {
        if (! isset($files['tmp_name']) || ! is_array($files['tmp_name'])
            || ! isset($files['size']) || ! is_array($files['size'])
            || ! isset($files['error']) || ! is_array($files['error'])
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                '$files provided to %s MUST contain each of the keys "tmp_name",'
                . ' "size", and "error", with each represented as an array;'
                . ' one or more were missing or non-array values',
                __FUNCTION__
            ));
        }

        return $recursiveNormalize(
            $files['tmp_name'],
            $files['size'],
            $files['error'],
            isset($files['name']) ? $files['name'] : null,
            isset($files['type']) ? $files['type'] : null
        );
    };

    $normalized = [];
    foreach ($files as $key => $value) {
        if ($value instanceof UploadedFileInterface) {
            $normalized[$key] = $value;
            continue;
        }

        if (is_array($value) && isset($value['tmp_name']) && is_array($value['tmp_name'])) {
            $normalized[$key] = $normalizeUploadedFileSpecification($value);
            continue;
        }

        if (is_array($value) && isset($value['tmp_name'])) {
            $normalized[$key] = createUploadedFile($value);
            continue;
        }

        if (is_array($value)) {
            $normalized[$key] = normalizeUploadedFiles($value);
            continue;
        }

        throw new Exception\InvalidArgumentException('Invalid value in files specification');
    }
    return $normalized;
}
