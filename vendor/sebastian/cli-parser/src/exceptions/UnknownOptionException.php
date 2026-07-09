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

final class UnknownOptionException extends RuntimeException implements Exception
{
    /**
     * @param array<string> $similarOptions
     */
    public function __construct(string $option, array $similarOptions)
    {
        $message = sprintf(
            'Unknown option "%s"',
            $option,
        );

        if ($similarOptions !== []) {
            $message = sprintf(
                'Unknown option "%s". Most similar options are %s',
                $option,
                implode(', ', $similarOptions),
            );
        }

        parent::__construct($message);
    }
}
