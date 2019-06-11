<?php
/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros;

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

use function fopen;
use function fwrite;
use function get_resource_type;
use function is_resource;
use function rewind;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createStream(string $content = '') : StreamInterface
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);
        rewind($resource);

        return $this->createStreamFromResource($resource);
    }

    /**
     * {@inheritDoc}
     */
    public function createStreamFromFile(string $file, string $mode = 'r') : StreamInterface
    {
        return new Stream($file, $mode);
    }

    /**
     * {@inheritDoc}
     */
    public function createStreamFromResource($resource) : StreamInterface
    {
        if (! is_resource($resource) || 'stream' !== get_resource_type($resource)) {
            throw new Exception\InvalidArgumentException(
                'Invalid stream provided; must be a stream resource'
            );
        }
        return new Stream($resource);
    }
}
