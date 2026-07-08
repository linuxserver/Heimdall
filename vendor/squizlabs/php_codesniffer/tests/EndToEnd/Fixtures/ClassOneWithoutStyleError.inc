<?php

/**
 * Class containing no style errors according to the end-to-end tests phpcs.xml.dist.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\EndToEnd\Fixtures;

class ClassOneWithoutStyleError
{
    private function foo()
    {
        return 'bar';
    }
}
