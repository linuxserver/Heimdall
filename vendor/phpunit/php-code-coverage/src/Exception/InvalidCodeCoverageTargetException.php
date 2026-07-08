<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Test\Target;

use function sprintf;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Exception;

final class InvalidCodeCoverageTargetException extends RuntimeException implements Exception
{
    public function __construct(Target $target)
    {
        parent::__construct(
            sprintf(
                '%s is not a valid target for code coverage',
                $target->description(),
            ),
        );
    }
}
