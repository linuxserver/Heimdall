<?php
/**
 * Test double for the HTML doc generator.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Generators\Fixtures;

use PHP_CodeSniffer\Generators\HTML;

class HTMLDouble extends HTML
{

    /**
     * Format the footer of the HTML page without the date or version nr to make the expectation fixtures stable.
     *
     * @return string
     */
    protected function getFormattedFooter()
    {
        $output ='  <div class="tag-line">Documentation generated on #REDACTED# by <a href="https://github.com/PHPCSStandards/PHP_CodeSniffer">PHP_CodeSniffer #VERSION#</a></div>
 </body>
</html>';

        // Use the correct line endings based on the OS.
        return str_replace("\n", PHP_EOL, $output).PHP_EOL;
    }

    /**
     * Retrieve the _real_ footer of the HTML page.
     *
     * @return string
     */
    public function getRealFooter()
    {
        return parent::getFormattedFooter();
    }

    /**
     * [VISIBILITY WIDENING ONLY] Print the header of the HTML page.
     *
     * @return void
     */
    public function printHeader()
    {
        parent::printHeader();
    }

    /**
     * [VISIBILITY WIDENING ONLY] Print the table of contents for the standard.
     *
     * @return void
     */
    public function printToc()
    {
        parent::printToc();
    }

    /**
     * [VISIBILITY WIDENING ONLY] Print the footer of the HTML page.
     *
     * @return void
     */
    public function printFooter()
    {
        parent::printFooter();
    }
}
