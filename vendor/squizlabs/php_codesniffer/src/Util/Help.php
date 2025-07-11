<?php
/**
 * Print the Help information.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is intended for internal use only and is not part of the public API.
 * This also means that it has no promise of backward compatibility. Use at your own risk.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Util;

use InvalidArgumentException;
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Util\Common;

final class Help
{


    /**
     * Short options which are available for both the `phpcs` as well as the `phpcbf` command.
     *
     * @var string
     */
    const DEFAULT_SHORT_OPTIONS = '-hilnpqvw';

    /**
     * Long options which are available for both the `phpcs` as well as the `phpcbf` command.
     *
     * {@internal This should be a constant array, but those aren't supported until PHP 5.6.}
     *
     * @var string Comma-separated list of the option names.
     */
    const DEFAULT_LONG_OPTIONS = 'basepath,bootstrap,colors,encoding,error-severity,exclude,extensions,file,file-list,filter,ignore,ignore-annotations,no-colors,parallel,php-ini,report-width,runtime-set,severity,sniffs,standard,stdin-path,tab-width,version,vv,vvv,warning-severity';

    /**
     * Minimum screen width.
     *
     * The help info needs room to display, so this is the minimum acceptable width.
     *
     * @var integer
     */
    const MIN_WIDTH = 60;

    /**
     * Indent option lines.
     *
     * @var string
     */
    const INDENT = '  ';

    /**
     * Gutter spacing for between the option argument info and the option description.
     *
     * @var string
     */
    const GUTTER = ' ';

    /**
     * The current PHPCS Configuration.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private $config;

    /**
     * The options which should be shown for this help screen.
     *
     * @var array<string>
     */
    private $requestedOptions = [];

    /**
     * Active options per category (after filtering).
     *
     * @var array<string, array<string, array<string, string>>>
     */
    private $activeOptions = [];

    /**
     * Width of the indent for option lines.
     *
     * @var integer
     */
    private $indentWidth = 0;

    /**
     * Width of the gutter spacing.
     *
     * @var integer
     */
    private $gutterWidth = 0;

    /**
     * Width of longest option argument info entry.
     *
     * @var integer
     */
    private $maxOptionNameLength = 0;


    /**
     * Constructor.
     *
     * @param \PHP_CodeSniffer\Config $config       Configuration object.
     * @param array<string>           $longOptions  The long options which should be shown.
     * @param string                  $shortOptions The short options which should be shown.
     *
     * @throws \InvalidArgumentException When $shortOptions is not a string.
     */
    public function __construct(Config $config, array $longOptions, $shortOptions='')
    {
        if (is_string($shortOptions) === false) {
            throw new InvalidArgumentException('The $shortOptions parameter must be a string');
        }

        $this->config           = $config;
        $this->requestedOptions = array_merge($longOptions, str_split($shortOptions));

        $this->filterOptions();

        $this->indentWidth = strlen(self::INDENT);
        $this->gutterWidth = strlen(self::GUTTER);

        $this->setMaxOptionNameLength();

    }//end __construct()


    /**
     * Display the help info.
     *
     * @return void
     */
    public function display()
    {
        $this->printUsage();
        $this->printCategories();

    }//end display()


    /**
     * Filter the available options based on the requested options.
     *
     * @return void
     */
    private function filterOptions()
    {
        $filteredOptions = $this->getAllOptions();

        foreach ($filteredOptions as $category => $options) {
            // Initial state set to "true" to prevent a spacer at the start of an array.
            $lastWasSpacer = true;
            $spacerCount   = 0;

            foreach ($options as $name => $option) {
                if ($lastWasSpacer !== true && strpos($name, 'blank-line') === 0) {
                    ++$spacerCount;
                    $lastWasSpacer = true;
                    continue;
                }

                if (in_array($name, $this->requestedOptions, true) === false) {
                    unset($filteredOptions[$category][$name]);
                    continue;
                }

                $lastWasSpacer = false;
            }

            // Make sure the final array doesn't contain a spacer at the end.
            if (empty($filteredOptions[$category]) === false) {
                end($filteredOptions[$category]);
                $key = key($filteredOptions[$category]);
                if (strpos($key, 'blank-line') === 0) {
                    unset($filteredOptions[$category][$key]);
                    --$spacerCount;
                }
            }

            // Remove categories now left empty.
            if (empty($filteredOptions[$category]) === true || count($filteredOptions[$category]) === $spacerCount) {
                unset($filteredOptions[$category]);
            }
        }//end foreach

        $this->activeOptions = $filteredOptions;

    }//end filterOptions()


    /**
     * Determine the length of the longest option argument and store it.
     *
     * @return void
     */
    private function setMaxOptionNameLength()
    {
        $lengths = [];
        foreach ($this->activeOptions as $category => $options) {
            foreach ($options as $option) {
                if (isset($option['argument']) === false) {
                    continue;
                }

                $lengths[] = strlen($option['argument']);
            }
        }

        if (empty($lengths) === false) {
            $this->maxOptionNameLength = max($lengths);
        }

    }//end setMaxOptionNameLength()


    /**
     * Get the maximum width which can be used to display the help info.
     *
     * Independently of user preference/auto-determined width of the current screen,
     * a minimum width is needed to display information, so don't allow this to get too low.
     *
     * @return int
     */
    private function getMaxWidth()
    {
        return max(self::MIN_WIDTH, $this->config->reportWidth);

    }//end getMaxWidth()


    /**
     * Get the maximum width for the text in the option description column.
     *
     * @return int
     */
    private function getDescriptionColumnWidth()
    {
        return ($this->getMaxWidth() - $this->maxOptionNameLength - $this->indentWidth - $this->gutterWidth);

    }//end getDescriptionColumnWidth()


    /**
     * Get the length of the indentation needed for follow up lines when the description does not fit on one line.
     *
     * @return int
     */
    private function getDescriptionFollowupLineIndentLength()
    {
        return ($this->maxOptionNameLength + $this->indentWidth + $this->gutterWidth);

    }//end getDescriptionFollowupLineIndentLength()


    /**
     * Print basic usage information to the screen.
     *
     * @return void
     */
    private function printUsage()
    {
        $command = 'phpcs';
        if (defined('PHP_CODESNIFFER_CBF') === true && PHP_CODESNIFFER_CBF === true) {
            $command = 'phpcbf';
        }

        $this->printCategoryHeader('Usage');

        echo self::INDENT.$command.' [options] <file|directory>'.PHP_EOL;

    }//end printUsage()


    /**
     * Print details of all the requested options to the screen, sorted by category.
     *
     * @return void
     */
    private function printCategories()
    {
        foreach ($this->activeOptions as $category => $options) {
            $this->printCategoryHeader($category);
            $this->printCategoryOptions($options);
        }

    }//end printCategories()


    /**
     * Print a category header.
     *
     * @param string $header The header text.
     *
     * @return void
     */
    private function printCategoryHeader($header)
    {
        $header .= ':';
        if ($this->config->colors === true) {
            $header = "\033[33m{$header}\033[0m";
        }

        echo PHP_EOL.$header.PHP_EOL;

    }//end printCategoryHeader()


    /**
     * Print the options for a category.
     *
     * @param array<string, array<string, string>> $options The options to display.
     *
     * @return void
     */
    private function printCategoryOptions(array $options)
    {
        $maxDescriptionWidth = $this->getDescriptionColumnWidth();
        $maxTextWidth        = ($this->getMaxWidth() - $this->indentWidth);
        $secondLineIndent    = str_repeat(' ', $this->getDescriptionFollowupLineIndentLength());

        $output = '';
        foreach ($options as $option) {
            if (isset($option['spacer']) === true) {
                $output .= PHP_EOL;
            }

            if (isset($option['text']) === true) {
                $text    = wordwrap($option['text'], $maxTextWidth, "\n");
                $output .= self::INDENT.implode(PHP_EOL.self::INDENT, explode("\n", $text)).PHP_EOL;
            }

            if (isset($option['argument'], $option['description']) === true) {
                $argument = str_pad($option['argument'], $this->maxOptionNameLength);
                $argument = $this->colorizeVariableInput($argument);
                $output  .= self::INDENT."\033[32m{$argument}\033[0m";
                $output  .= self::GUTTER;

                $description = wordwrap($option['description'], $maxDescriptionWidth, "\n");
                $output     .= implode(PHP_EOL.$secondLineIndent, explode("\n", $description)).PHP_EOL;
            }
        }

        if ($this->config->colors === false) {
            $output = Common::stripColors($output);
        }

        echo $output;

    }//end printCategoryOptions()


    /**
     * Colorize "variable" input in the option argument info.
     *
     * For the purposes of this method, "variable" input is text between <> brackets.
     * The regex allows for multiple tags and nested tags.
     *
     * @param string $text The text to process.
     *
     * @return string
     */
    private function colorizeVariableInput($text)
    {
        return preg_replace('`(<(?:(?>[^<>]+)|(?R))*>)`', "\033[36m".'$1'."\033[32m", $text);

    }//end colorizeVariableInput()


    /**
     * Retrieve the help details for all supported CLI arguments per category.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    private function getAllOptions()
    {
        $options = [];

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- Readability is more important.
        $options['Scan targets'] = [
            'file'       => [
                'argument'    => '<file|directory>',
                'description' => 'One or more files and/or directories to check, space separated.',
            ],
            '-'          => [
                'argument'    => '-',
                'description' => 'Check STDIN instead of local files and directories.',
            ],
            'stdin-path' => [
                'argument'    => '--stdin-path=<stdinPath>',
                'description' => 'If processing STDIN, the file path that STDIN will be processed as.',
            ],
            'file-list'  => [
                'argument'    => '--file-list=<fileList>',
                'description' => 'Check the files and/or directories which are defined in the file to which the path is provided (one per line).',
            ],
            'filter'     => [
                'argument'    => '--filter=<filter>',
                'description' => 'Check based on a predefined file filter. Use either the "GitModified" or "GitStaged" filter, or specify the path to a custom filter class.',
            ],
            'ignore'     => [
                'argument'    => '--ignore=<patterns>',
                'description' => 'Ignore files based on a comma-separated list of patterns matching files and/or directories.',
            ],
            'extensions' => [
                'argument'    => '--extensions=<extensions>',
                'description' => 'Check files with the specified file extensions (comma-separated list). Defaults to php,inc/php,js,css.'."\n"
                    .'The type of the file can be specified using: ext/type; e.g. module/php,es/js.',
            ],
            'l'          => [
                'argument'    => '-l',
                'description' => 'Check local directory only, no recursion.',
            ],
        ];

        $options['Rule Selection Options'] = [
            'standard'   => [
                'argument'    => '--standard=<standard>',
                'description' => 'The name of, or the path to, the coding standard to use. Can be a comma-separated list specifying multiple standards. If no standard is specified, PHP_CodeSniffer will look for a [.]phpcs.xml[.dist] custom ruleset file in the current directory and those above it.',
            ],
            'sniffs'     => [
                'argument'    => '--sniffs=<sniffs>',
                'description' => 'A comma-separated list of sniff codes to limit the scan to. All sniffs must be part of the standard in use.',
            ],
            'exclude'    => [
                'argument'    => '--exclude=<sniffs>',
                'description' => 'A comma-separated list of sniff codes to exclude from the scan. All sniffs must be part of the standard in use.',
            ],
            'blank-line' => ['spacer' => ''],

            'i'          => [
                'argument'    => '-i',
                'description' => 'Show a list of installed coding standards.',
            ],
            'e'          => [
                'argument'    => '-e',
                'description' => 'Explain a standard by showing the names of all the sniffs it includes.',
            ],
            'generator'  => [
                'argument'    => '--generator=<generator>',
                'description' => 'Show documentation for a standard. Use either the "HTML", "Markdown" or "Text" generator.',
            ],
        ];

        $options['Run Options'] = [
            'a'          => [
                'argument'    => '-a',
                'description' => 'Run in interactive mode, pausing after each file.',
            ],
            'bootstrap'  => [
                'argument'    => '--bootstrap=<bootstrap>',
                'description' => 'Run the specified file(s) before processing begins. A list of files can be provided, separated by commas.',
            ],
            'cache'      => [
                'argument'    => '--cache[=<cacheFile>]',
                'description' => 'Cache results between runs. Optionally, <cacheFile> can be provided to use a specific file for caching. Otherwise, a temporary file is used.',
            ],
            'no-cache'   => [
                'argument'    => '--no-cache',
                'description' => 'Do not cache results between runs (default).',
            ],
            'parallel'   => [
                'argument'    => '--parallel=<processes>',
                'description' => 'The number of files to be checked simultaneously. Defaults to 1 (no parallel processing).'."\n"
                    .'If enabled, this option only takes effect if the PHP PCNTL (Process Control) extension is available.',
            ],
            'suffix'     => [
                'argument'    => '--suffix=<suffix>',
                'description' => 'Write modified files to a filename using this suffix ("diff" and "patch" are not used in this mode).',
            ],
            'blank-line' => ['spacer' => ''],

            'php-ini'    => [
                'argument'    => '-d <key[=value]>',
                'description' => 'Set the [key] php.ini value to [value] or set to [true] if value is omitted.'."\n"
                    .'Note: only php.ini settings which can be changed at runtime are supported.',
            ],
        ];

        $options['Reporting Options'] = [
            'report'             => [
                'argument'    => '--report=<report(s)>',
                'description' => 'A comma-separated list of reports to print. Available reports: "full", "xml", "checkstyle", "csv", "json", "junit", "emacs", "source", "summary", "diff", "svnblame", "gitblame", "hgblame", "notifysend" or "performance".'."\n"
                    .'Or specify the path to a custom report class. By default, the "full" report is displayed.',
            ],
            'report-file'        => [
                'argument'    => '--report-file=<reportFile>',
                'description' => 'Write the report to the specified file path.',
            ],
            'report-report'      => [
                'argument'    => '--report-<report>=<reportFile>',
                'description' => 'Write the report specified in <report> to the specified file path.',
            ],
            'report-width'       => [
                'argument'    => '--report-width=<reportWidth>',
                'description' => 'How many columns wide screen reports should be. Set to "auto" to use current screen width, where supported.',
            ],
            'basepath'           => [
                'argument'    => '--basepath=<basepath>',
                'description' => 'Strip a path from the front of file paths inside reports.',
            ],
            'blank-line-1'       => ['spacer' => ''],

            'w'                  => [
                'argument'    => '-w',
                'description' => 'Include both warnings and errors (default).',
            ],
            'n'                  => [
                'argument'    => '-n',
                'description' => 'Do not include warnings. Shortcut for "--warning-severity=0".',
            ],
            'severity'           => [
                'argument'    => '--severity=<severity>',
                'description' => 'The minimum severity required to display an error or warning. Defaults to 5.',
            ],
            'error-severity'     => [
                'argument'    => '--error-severity=<severity>',
                'description' => 'The minimum severity required to display an error. Defaults to 5.',
            ],
            'warning-severity'   => [
                'argument'    => '--warning-severity=<severity>',
                'description' => 'The minimum severity required to display a warning. Defaults to 5.',
            ],
            'blank-line-2'       => ['spacer' => ''],

            's'                  => [
                'argument'    => '-s',
                'description' => 'Show sniff error codes in all reports.',
            ],
            'ignore-annotations' => [
                'argument'    => '--ignore-annotations',
                'description' => 'Ignore all "phpcs:..." annotations in code comments.',
            ],
            'colors'             => [
                'argument'    => '--colors',
                'description' => 'Use colors in screen output.',
            ],
            'no-colors'          => [
                'argument'    => '--no-colors',
                'description' => 'Do not use colors in screen output (default).',
            ],
            'p'                  => [
                'argument'    => '-p',
                'description' => 'Show progress of the run.',
            ],
            'q'                  => [
                'argument'    => '-q',
                'description' => 'Quiet mode; disables progress and verbose output.',
            ],
            'm'                  => [
                'argument'    => '-m',
                'description' => 'Stop error messages from being recorded. This saves a lot of memory but stops many reports from being used.',
            ],
        ];

        $options['Configuration Options'] = [
            'encoding'       => [
                'argument'    => '--encoding=<encoding>',
                'description' => 'The encoding of the files being checked. Defaults to "utf-8".',
            ],
            'tab-width'      => [
                'argument'    => '--tab-width=<tabWidth>',
                'description' => 'The number of spaces each tab represents.',
            ],
            'blank-line'     => ['spacer' => ''],

            'config-explain' => [
                'text' => 'Default values for a selection of options can be stored in a user-specific CodeSniffer.conf configuration file.'."\n"
                    .'This applies to the following options: "default_standard", "report_format", "tab_width", "encoding", "severity", "error_severity", "warning_severity", "show_warnings", "report_width", "show_progress", "quiet", "colors", "cache", "parallel", "installed_paths", "php_version", "ignore_errors_on_exit", "ignore_warnings_on_exit".',
            ],
            'config-show'    => [
                'argument'    => '--config-show',
                'description' => 'Show the configuration options which are currently stored in the applicable CodeSniffer.conf file.',
            ],
            'config-set'     => [
                'argument'    => '--config-set <key> <value>',
                'description' => 'Save a configuration option to the CodeSniffer.conf file.',
            ],
            'config-delete'  => [
                'argument'    => '--config-delete <key>',
                'description' => 'Delete a configuration option from the CodeSniffer.conf file.',
            ],
            'runtime-set'    => [
                'argument'    => '--runtime-set <key> <value>',
                'description' => 'Set a configuration option to be applied to the current scan run only.',
            ],
        ];

        $options['Miscellaneous Options'] = [
            'h'       => [
                'argument'    => '-h, -?, --help',
                'description' => 'Print this help message.',
            ],
            'version' => [
                'argument'    => '--version',
                'description' => 'Print version information.',
            ],
            'v'       => [
                'argument'    => '-v',
                'description' => 'Verbose output: Print processed files.',
            ],
            'vv'      => [
                'argument'    => '-vv',
                'description' => 'Verbose output: Print ruleset and token output.',
            ],
            'vvv'     => [
                'argument'    => '-vvv',
                'description' => 'Verbose output: Print sniff processing information.',
            ],
        ];
        // phpcs:enable

        return $options;

    }//end getAllOptions()


}//end class
