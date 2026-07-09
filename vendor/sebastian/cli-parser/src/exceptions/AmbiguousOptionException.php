<?php declare(strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CliParser;

use function implode;
use function sprintf;
use RuntimeException;

final class AmbiguousOptionException extends RuntimeException implements Exception
{
    /**
     * @param array<string> $candiates
     */
    public function __construct(string $option, array $candiates)
    {
        parent::__construct(
            sprintf(
                'Option "%s" is ambiguous. Similar options are: %s',
                $option,
                implode(', ', $candiates),
            ),
        );
    }
}
