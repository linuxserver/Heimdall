<?php
/**
 * A doc generator that outputs documentation in Markdown format.
 *
 * @author    Stefano Kowalke <blueduck@gmx.net>
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2014 Arroba IT
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Generators;

use DOMElement;
use DOMNode;
use PHP_CodeSniffer\Config;

class Markdown extends Generator
{


    /**
     * Generates the documentation for a standard.
     *
     * @return void
     * @see    processSniff()
     */
    public function generate()
    {
        if (empty($this->docFiles) === true) {
            return;
        }

        ob_start();
        parent::generate();

        $content = ob_get_contents();
        ob_end_clean();

        if (trim($content) !== '') {
            echo $this->getFormattedHeader();
            echo $content;
            echo $this->getFormattedFooter();
        }

    }//end generate()


    /**
     * Print the markdown header.
     *
     * @deprecated 3.12.0 Use Markdown::getFormattedHeader() instead.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function printHeader()
    {
        trigger_error(
            'The '.__METHOD__.'() method is deprecated. Use "echo '.__CLASS__.'::getFormattedHeader()" instead.',
            E_USER_DEPRECATED
        );

        echo $this->getFormattedHeader();

    }//end printHeader()


    /**
     * Format the markdown header.
     *
     * @since 3.12.0 Replaces the deprecated Markdown::printHeader() method.
     *
     * @return string
     */
    protected function getFormattedHeader()
    {
        $standard = $this->ruleset->name;

        return "# $standard Coding Standard".PHP_EOL;

    }//end getFormattedHeader()


    /**
     * Print the markdown footer.
     *
     * @deprecated 3.12.0 Use Markdown::getFormattedFooter() instead.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function printFooter()
    {
        trigger_error(
            'The '.__METHOD__.'() method is deprecated. Use "echo '.__CLASS__.'::getFormattedFooter()" instead.',
            E_USER_DEPRECATED
        );

        echo $this->getFormattedFooter();

    }//end printFooter()


    /**
     * Format the markdown footer.
     *
     * @since 3.12.0 Replaces the deprecated Markdown::printFooter() method.
     *
     * @return string
     */
    protected function getFormattedFooter()
    {
        // Turn off errors so we don't get timezone warnings if people
        // don't have their timezone set.
        $errorLevel = error_reporting(0);
        $output     = PHP_EOL.'Documentation generated on '.date('r');
        $output    .= ' by [PHP_CodeSniffer '.Config::VERSION.'](https://github.com/PHPCSStandards/PHP_CodeSniffer)'.PHP_EOL;
        error_reporting($errorLevel);

        return $output;

    }//end getFormattedFooter()


    /**
     * Process the documentation for a single sniff.
     *
     * @param \DOMNode $doc The DOMNode object for the sniff.
     *                      It represents the "documentation" tag in the XML
     *                      standard file.
     *
     * @return void
     */
    protected function processSniff(DOMNode $doc)
    {
        $content = '';
        foreach ($doc->childNodes as $node) {
            if ($node->nodeName === 'standard') {
                $content .= $this->getFormattedTextBlock($node);
            } else if ($node->nodeName === 'code_comparison') {
                $content .= $this->getFormattedCodeComparisonBlock($node);
            }
        }

        if (trim($content) !== '') {
            $title = $this->getTitle($doc);
            echo PHP_EOL."## $title".PHP_EOL.PHP_EOL;
            echo $content;
        }

    }//end processSniff()


    /**
     * Print a text block found in a standard.
     *
     * @param \DOMNode $node The DOMNode object for the text block.
     *
     * @deprecated 3.12.0 Use Markdown::getFormattedTextBlock() instead.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function printTextBlock(DOMNode $node)
    {
        trigger_error(
            'The '.__METHOD__.'() method is deprecated. Use "echo '.__CLASS__.'::getFormattedTextBlock()" instead.',
            E_USER_DEPRECATED
        );

        echo $this->getFormattedTextBlock($node);

    }//end printTextBlock()


    /**
     * Format a text block found in a standard.
     *
     * @param \DOMNode $node The DOMNode object for the text block.
     *
     * @since 3.12.0 Replaces the deprecated Markdown::printTextBlock() method.
     *
     * @return string
     */
    protected function getFormattedTextBlock(DOMNode $node)
    {
        $content = $node->nodeValue;
        if (empty($content) === true) {
            return '';
        }

        $content = trim($content);
        $content = htmlspecialchars($content, (ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401));
        $content = str_replace('&lt;em&gt;', '*', $content);
        $content = str_replace('&lt;/em&gt;', '*', $content);

        $nodeLines = explode("\n", $content);
        $lineCount = count($nodeLines);
        $lines     = [];

        for ($i = 0; $i < $lineCount; $i++) {
            $currentLine = trim($nodeLines[$i]);
            if ($currentLine === '') {
                // The text contained a blank line. Respect this.
                $lines[] = '';
                continue;
            }

            // Check if the _next_ line is blank.
            if (isset($nodeLines[($i + 1)]) === false
                || trim($nodeLines[($i + 1)]) === ''
            ) {
                // Next line is blank, just add the line.
                $lines[] = $currentLine;
            } else {
                // Ensure that line breaks are respected in markdown.
                $lines[] = $currentLine.'  ';
            }
        }

        return implode(PHP_EOL, $lines).PHP_EOL;

    }//end getFormattedTextBlock()


    /**
     * Print a code comparison block found in a standard.
     *
     * @param \DOMNode $node The DOMNode object for the code comparison block.
     *
     * @deprecated 3.12.0 Use Markdown::getFormattedCodeComparisonBlock() instead.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    protected function printCodeComparisonBlock(DOMNode $node)
    {
        trigger_error(
            'The '.__METHOD__.'() method is deprecated. Use "echo '.__CLASS__.'::getFormattedCodeComparisonBlock()" instead.',
            E_USER_DEPRECATED
        );

        echo $this->getFormattedCodeComparisonBlock($node);

    }//end printCodeComparisonBlock()


    /**
     * Format a code comparison block found in a standard.
     *
     * @param \DOMNode $node The DOMNode object for the code comparison block.
     *
     * @since 3.12.0 Replaces the deprecated Markdown::printCodeComparisonBlock() method.
     *
     * @return string
     */
    protected function getFormattedCodeComparisonBlock(DOMNode $node)
    {
        $codeBlocks    = $node->getElementsByTagName('code');
        $firstCodeElm  = $codeBlocks->item(0);
        $secondCodeElm = $codeBlocks->item(1);

        if (isset($firstCodeElm, $secondCodeElm) === false) {
            // Missing at least one code block.
            return '';
        }

        $firstTitle = $this->formatCodeTitle($firstCodeElm);
        $first      = $this->formatCodeSample($firstCodeElm);

        $secondTitle = $this->formatCodeTitle($secondCodeElm);
        $second      = $this->formatCodeSample($secondCodeElm);

        $titleRow = '';
        if ($firstTitle !== '' || $secondTitle !== '') {
            $titleRow .= '   <tr>'.PHP_EOL;
            $titleRow .= "    <th>$firstTitle</th>".PHP_EOL;
            $titleRow .= "    <th>$secondTitle</th>".PHP_EOL;
            $titleRow .= '   </tr>'.PHP_EOL;
        }

        $codeRow = '';
        if ($first !== '' || $second !== '') {
            $codeRow .= '   <tr>'.PHP_EOL;
            $codeRow .= '<td>'.PHP_EOL.PHP_EOL;
            $codeRow .= "    $first".PHP_EOL.PHP_EOL;
            $codeRow .= '</td>'.PHP_EOL;
            $codeRow .= '<td>'.PHP_EOL.PHP_EOL;
            $codeRow .= "    $second".PHP_EOL.PHP_EOL;
            $codeRow .= '</td>'.PHP_EOL;
            $codeRow .= '   </tr>'.PHP_EOL;
        }

        $output = '';
        if ($titleRow !== '' || $codeRow !== '') {
            $output .= '  <table>'.PHP_EOL;
            $output .= $titleRow;
            $output .= $codeRow;
            $output .= '  </table>'.PHP_EOL;
        }

        return $output;

    }//end getFormattedCodeComparisonBlock()


    /**
     * Retrieve a code block title and prepare it for output as HTML.
     *
     * @param \DOMElement $codeElm The DOMElement object for a code block.
     *
     * @since 3.12.0
     *
     * @return string
     */
    private function formatCodeTitle(DOMElement $codeElm)
    {
        $title = trim($codeElm->getAttribute('title'));
        return str_replace('  ', '&nbsp;&nbsp;', $title);

    }//end formatCodeTitle()


    /**
     * Retrieve a code block contents and prepare it for output as HTML.
     *
     * @param \DOMElement $codeElm The DOMElement object for a code block.
     *
     * @since 3.12.0
     *
     * @return string
     */
    private function formatCodeSample(DOMElement $codeElm)
    {
        $code = (string) $codeElm->nodeValue;
        $code = trim($code);
        $code = str_replace("\n", PHP_EOL.'    ', $code);
        $code = str_replace(['<em>', '</em>'], '', $code);

        return $code;

    }//end formatCodeSample()


}//end class
