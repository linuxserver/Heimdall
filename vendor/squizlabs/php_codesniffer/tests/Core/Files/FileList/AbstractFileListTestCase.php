<?php
/**
 * Abstract testcase class for testing FileList methods.
 *
 * @copyright 2025 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Files\FileList;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Base functionality and utilities for testing FileList methods.
 */
abstract class AbstractFileListTestCase extends TestCase
{

    /**
     * The Config object.
     *
     * @var \PHP_CodeSniffer\Config
     */
    protected static $config;

    /**
     * The Ruleset object.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    protected static $ruleset;


    /**
     * Initialize the config and ruleset objects only once.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function initializeConfigAndRuleset()
    {
        // Wrapped in an `isset()` as the properties may have been set already (via a call to this method from a dataprovider).
        if (isset(self::$ruleset) === false) {
            self::$config         = new ConfigDouble();
            self::$config->filter = __DIR__.'/FilterDouble.php';
            self::$ruleset        = new Ruleset(self::$config);
        }

    }//end initializeConfigAndRuleset()


}//end class
