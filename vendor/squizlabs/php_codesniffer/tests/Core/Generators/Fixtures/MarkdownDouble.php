<?php
/**
 * Test double for the Markdown doc generator.
 *
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\Generators\Fixtures;

use PHP_CodeSniffer\Generators\Markdown;

class MarkdownDouble extends Markdown
{

    /**
     * Format the markdown footer without the date or version nr to make the expectation fixtures stable.
     *
     * @return string
     */
    protected function getFormattedFooter()
    {
        $output     = PHP_EOL.'Documentation generated on *REDACTED*';
        $output    .= ' by [PHP_CodeSniffer *VERSION*](https://github.com/PHPCSStandards/PHP_CodeSniffer)'.PHP_EOL;

        return $output;
    }

    /**
     * Retrieve the _real_ footer of the markdown page.
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
