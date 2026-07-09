<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use function sprintf;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class XdebugVersionNotSupportedException extends RuntimeException implements Exception
{
    /**
     * @param non-empty-string $version
     */
    public function __construct(string $version)
    {
        parent::__construct(
            sprintf(
                'Version %s of the Xdebug extension is not supported',
                $version,
            ),
        );
    }
}
