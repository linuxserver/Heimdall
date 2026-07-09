<?php

/**
 * Class containing a simple style error that phpcbf can fix.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\EndToEnd\Fixtures;

class ClassWithStyleError
{
    /**
     * The bracket for this function is misaligned and this can be automatically fixed by phpcbf.
     *
     * @return string
     */
    private function foo() {
        return 'bar';
    }
}
