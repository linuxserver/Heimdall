<?php
/**
 * Test handling of broken ruleset files.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Ruleset;

use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Tests\ConfigDouble;
use PHP_CodeSniffer\Tests\Core\Ruleset\AbstractRulesetTestCase;

/**
 * Test handling of broken ruleset files.
 *
 * Note: these tests need to run in separate processes as otherwise they run into
 * some weirdness with the libxml_get_errors()/libxml_clear_errors() functions
 * (duplicate error messages).
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 *
 * @group Windows
 *
 * @covers \PHP_CodeSniffer\Ruleset::processRuleset
 */
final class ProcessRulesetBrokenRulesetTest extends AbstractRulesetTestCase
{


    /**
     * Test displaying an informative error message when an empty XML ruleset file is encountered.
     *
     * @return void
     */
    public function testBrokenRulesetEmptyFile()
    {
        $standard = __DIR__.'/ProcessRulesetBrokenRulesetEmptyFileTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $regex  = '`^ERROR: Ruleset \S+ProcessRulesetBrokenRulesetEmptyFileTest\.xml is not valid\R';
        $regex .= '(- On line 1, column 1: Document is empty\R)?$`';

        $this->expectRuntimeExceptionRegex($regex);

        new Ruleset($config);

    }//end testBrokenRulesetEmptyFile()


    /**
     * Test displaying an informative error message for a broken XML ruleset with a single XML error.
     *
     * @return void
     */
    public function testBrokenRulesetSingleError()
    {
        $standard = __DIR__.'/ProcessRulesetBrokenRulesetSingleErrorTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $regex  = '`^ERROR: Ruleset \S+ProcessRulesetBrokenRulesetSingleErrorTest\.xml is not valid\R';
        $regex .= '- On line 3, column 1: (Premature end of data in tag ruleset line 2|EndTag: \'</\' not found)\R$`';

        $this->expectRuntimeExceptionRegex($regex);

        new Ruleset($config);

    }//end testBrokenRulesetSingleError()


    /**
     * Test displaying an informative error message for a broken XML ruleset with multiple XML errors.
     *
     * @return void
     */
    public function testBrokenRulesetMultiError()
    {
        $standard = __DIR__.'/ProcessRulesetBrokenRulesetMultiErrorTest.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);

        $regex  = '`^ERROR: Ruleset \S+ProcessRulesetBrokenRulesetMultiErrorTest\.xml is not valid\R';
        $regex .= '- On line 8, column 12: Opening and ending tag mismatch: property line 7 and rule\R';
        $regex .= '- On line 10, column 11: Opening and ending tag mismatch: properties line [57] and ruleset\R';
        $regex .= '(- On line 11, column 1: (Premature end of data in tag rule(set)? line [24]|EndTag: \'</\' not found)\R)*$`';

        $this->expectRuntimeExceptionRegex($regex);

        new Ruleset($config);

    }//end testBrokenRulesetMultiError()


}//end class
