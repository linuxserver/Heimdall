<?php declare(strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CliParser;

use function array_map;
use function array_merge;
use function array_shift;
use function array_slice;
use function assert;
use function count;
use function current;
use function explode;
use function is_array;
use function is_int;
use function key;
use function levenshtein;
use function next;
use function preg_replace;
use function reset;
use function rtrim;
use function sort;
use function str_ends_with;
use function str_starts_with;
use function strlen;
use function strstr;
use function substr;
use function usort;

final readonly class Parser
{
    /**
     * @param list<string> $argv
     * @param list<string> $longOptions
     *
     * @throws AmbiguousOptionException
     * @throws OptionDoesNotAllowArgumentException
     * @throws RequiredOptionArgumentMissingException
     * @throws UnknownOptionException
     *
     * @return array{0: list<array{0: string, 1: ?string}>, 1: list<string>}
     */
    public function parse(array $argv, string $shortOptions, ?array $longOptions = null): array
    {
        if ($argv === []) {
            return [[], []];
        }

        $options    = [];
        $nonOptions = [];

        if ($longOptions !== null) {
            sort($longOptions);
        }

        if (isset($argv[0][0]) && $argv[0][0] !== '-') {
            array_shift($argv);
        }

        reset($argv);

        $argv = array_map('trim', $argv);

        while (false !== $arg = current($argv)) {
            $i = key($argv);

            assert(is_int($i));

            next($argv);

            if ($arg === '') {
                continue;
            }

            if ($arg === '--') {
                $nonOptions = array_merge($nonOptions, array_slice($argv, $i + 1));

                break;
            }

            if ($arg[0] !== '-' || (strlen($arg) > 1 && $arg[1] === '-' && $longOptions === null)) {
                $nonOptions[] = $arg;

                continue;
            }

            if (strlen($arg) > 1 && $arg[1] === '-' && is_array($longOptions)) {
                $this->parseLongOption(
                    substr($arg, 2),
                    $longOptions,
                    $options,
                    $argv,
                );

                continue;
            }

            $this->parseShortOption(
                substr($arg, 1),
                $shortOptions,
                $options,
                $argv,
            );
        }

        return [$options, $nonOptions];
    }

    /**
     * @param list<array{0: string, 1: ?string}> $options
     * @param list<string>                       $argv
     *
     * @throws RequiredOptionArgumentMissingException
     */
    private function parseShortOption(string $argument, string $shortOptions, array &$options, array &$argv): void
    {
        $argumentLength = strlen($argument);

        for ($i = 0; $i < $argumentLength; $i++) {
            $option         = $argument[$i];
            $optionArgument = null;

            if ($argument[$i] === ':' || ($spec = strstr($shortOptions, $option)) === false) {
                throw new UnknownOptionException('-' . $option, []);
            }

            if (strlen($spec) > 1 && $spec[1] === ':') {
                if ($i + 1 < $argumentLength) {
                    $options[] = [$option, substr($argument, $i + 1)];

                    break;
                }

                if (!(strlen($spec) > 2 && $spec[2] === ':')) {
                    $optionArgument = current($argv);

                    if ($optionArgument === false) {
                        throw new RequiredOptionArgumentMissingException('-' . $option);
                    }

                    next($argv);
                }
            }

            $options[] = [$option, $optionArgument];
        }
    }

    /**
     * @param list<string>                       $longOptions
     * @param list<array{0: string, 1: ?string}> $options
     * @param list<string>                       $argv
     *
     * @throws AmbiguousOptionException
     * @throws OptionDoesNotAllowArgumentException
     * @throws RequiredOptionArgumentMissingException
     * @throws UnknownOptionException
     */
    private function parseLongOption(string $argument, array $longOptions, array &$options, array &$argv): void
    {
        $count          = count($longOptions);
        $list           = explode('=', $argument);
        $option         = $list[0];
        $optionLength   = strlen($option);
        $similarOptions = [];
        $optionArgument = null;

        if (count($list) > 1) {
            $optionArgument = $list[1];
        }

        foreach ($longOptions as $i => $longOption) {
            $similarOptions[] = [
                levenshtein($longOption, $option),
                '--' . rtrim($longOption, '='),
            ];

            $opt_start = substr($longOption, 0, $optionLength);

            if ($opt_start !== $option) {
                continue;
            }

            $opt_rest = substr($longOption, $optionLength);

            if ($opt_rest !== '' &&
                $i + 1 < $count &&
                $option[0] !== '=' &&
                /** @phpstan-ignore offsetAccess.notFound */
                str_starts_with($longOptions[$i + 1], $option)) {
                $candidates = [];

                foreach ($longOptions as $aLongOption) {
                    if (str_starts_with($aLongOption, $option)) {
                        $candidates[] = '--' . rtrim($aLongOption, '=');
                    }
                }

                throw new AmbiguousOptionException('--' . $option, $candidates);
            }

            if (str_ends_with($longOption, '=')) {
                if (!str_ends_with($longOption, '==') && (string) $optionArgument === '') {
                    if (false === $optionArgument = current($argv)) {
                        throw new RequiredOptionArgumentMissingException('--' . $option);
                    }

                    next($argv);
                }
            } elseif ($optionArgument !== null) {
                throw new OptionDoesNotAllowArgumentException('--' . $option);
            }

            $fullOption = '--' . preg_replace('/={1,2}$/', '', $longOption);
            $options[]  = [$fullOption, $optionArgument];

            return;
        }

        throw new UnknownOptionException('--' . $option, $this->formatSimilarOptions($similarOptions));
    }

    /**
     * @param list<array{int, string}> $similarOptions
     *
     * @return array<string>
     */
    private function formatSimilarOptions(array $similarOptions): array
    {
        usort(
            $similarOptions,
            static function (array $a, array $b): int
            {
                return $a[0] <=> $b[0];
            },
        );

        $similarFormatted = [];

        foreach (array_slice($similarOptions, 0, 5) as [$distance, $label]) {
            $similarFormatted[] = $label;
        }

        return $similarFormatted;
    }
}
