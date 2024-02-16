<?php
/**
 * Marks a sniff as deprecated.
 *
 * Implementing this interface allows for marking a sniff as deprecated and
 * displaying information about the deprecation to the end-user.
 *
 * A sniff will still need to implement the `PHP_CodeSniffer\Sniffs\Sniff` interface
 * as well, or extend an abstract sniff which does, to be recognized as a valid sniff.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards Contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Sniffs;

interface DeprecatedSniff
{


    /**
     * Provide the version number in which the sniff was deprecated.
     *
     * Recommended format for PHPCS native sniffs: "v3.3.0".
     * Recommended format for external sniffs: "StandardName v3.3.0".
     *
     * @return string
     */
    public function getDeprecationVersion();


    /**
     * Provide the version number in which the sniff will be removed.
     *
     * Recommended format for PHPCS native sniffs: "v3.3.0".
     * Recommended format for external sniffs: "StandardName v3.3.0".
     *
     * If the removal version is not yet known, it is recommended to set
     * this to: "a future version".
     *
     * @return string
     */
    public function getRemovalVersion();


    /**
     * Optionally provide an arbitrary custom message to display with the deprecation.
     *
     * Typically intended to allow for displaying information about what to
     * replace the deprecated sniff with.
     * Example: "Use the Stnd.Cat.SniffName sniff instead."
     * Multi-line messages (containing new line characters) are supported.
     *
     * An empty string can be returned if there is no replacement/no need
     * for a custom message.
     *
     * @return string
     */
    public function getDeprecationMessage();


}//end interface
