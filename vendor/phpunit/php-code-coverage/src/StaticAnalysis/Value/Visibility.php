<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

/**
 * @internal This enumeration is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
enum Visibility: string
{
    case Public    = 'public';
    case Protected = 'protected';
    case Private   = 'private';
}
