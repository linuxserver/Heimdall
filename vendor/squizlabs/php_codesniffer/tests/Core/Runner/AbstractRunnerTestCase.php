<?php
/**
 * Base class to use for tests invoking the Runner class.
 *
 * As those tests will use the _real_ Config class instead of the ConfigDouble, we need to ensure
 * this doesn't negatively impact other tests, what with the Config using static properties.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Runner;

use PHP_CodeSniffer\Tests\Core\Config\AbstractRealConfigTestCase;

abstract class AbstractRunnerTestCase extends AbstractRealConfigTestCase
{

}//end class
