<?php

declare(strict_types=1);

namespace Doctrine\Deprecations;

use Psr\Log\LoggerInterface;

use function array_key_exists;
use function array_reduce;
use function debug_backtrace;
use function sprintf;
use function strpos;
use function strrpos;
use function substr;
use function trigger_error;

use const DEBUG_BACKTRACE_IGNORE_ARGS;
use const DIRECTORY_SEPARATOR;
use const E_USER_DEPRECATED;

/**
 * Manages Deprecation logging in different ways.
 *
 * By default triggered exceptions are not logged.
 *
 * To enable different deprecation logging mechanisms you can call the
 * following methods:
 *
 *  - Minimal collection of deprecations via getTriggeredDeprecations()
 *    \Doctrine\Deprecations\Deprecation::enableTrackingDeprecations();
 *
 *  - Uses @trigger_error with E_USER_DEPRECATED
 *    \Doctrine\Deprecations\Deprecation::enableWithTriggerError();
 *
 *  - Sends deprecation messages via a PSR-3 logger
 *    \Doctrine\Deprecations\Deprecation::enableWithPsrLogger($logger);
 *
 * Packages that trigger deprecations should use the `trigger()` or
 * `triggerIfCalledFromOutside()` methods.
 */
class Deprecation
{
    private const TYPE_NONE               = 0;
    private const TYPE_TRACK_DEPRECATIONS = 1;
    private const TYPE_TRIGGER_ERROR      = 2;
    private const TYPE_PSR_LOGGER         = 4;

    /** @var int */
    private static $type = self::TYPE_NONE;

    /** @var LoggerInterface|null */
    private static $logger;

    /** @var array<string,bool> */
    private static $ignoredPackages = [];

    /** @var array<string,int> */
    private static $ignoredLinks = [];

    /** @var bool */
    private static $deduplication = true;

    /**
     * Trigger a deprecation for the given package and identfier.
     *
     * The link should point to a Github issue or Wiki entry detailing the
     * deprecation. It is additionally used to de-duplicate the trigger of the
     * same deprecation during a request.
     *
     * @param mixed $args
     */
    public static function trigger(string $package, string $link, string $message, ...$args): void
    {
        if (self::$type === self::TYPE_NONE) {
            return;
        }

        if (array_key_exists($link, self::$ignoredLinks)) {
            self::$ignoredLinks[$link]++;
        } else {
            self::$ignoredLinks[$link] = 1;
        }

        if (self::$deduplication === true && self::$ignoredLinks[$link] > 1) {
            return;
        }

        if (isset(self::$ignoredPackages[$package])) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $message = sprintf($message, ...$args);

        self::delegateTriggerToBackend($message, $backtrace, $link, $package);
    }

    /**
     * Trigger a deprecation for the given package and identifier when called from outside.
     *
     * "Outside" means we assume that $package is currently installed as a
     * dependency and the caller is not a file in that package. When $package
     * is installed as a root package then deprecations triggered from the
     * tests folder are also considered "outside".
     *
     * This deprecation method assumes that you are using Composer to install
     * the dependency and are using the default /vendor/ folder and not a
     * Composer plugin to change the install location. The assumption is also
     * that $package is the exact composer packge name.
     *
     * Compared to {@link trigger()} this method causes some overhead when
     * deprecation tracking is enabled even during deduplication, because it
     * needs to call {@link debug_backtrace()}
     *
     * @param mixed $args
     */
    public static function triggerIfCalledFromOutside(string $package, string $link, string $message, ...$args): void
    {
        if (self::$type === self::TYPE_NONE) {
            return;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        // first check that the caller is not from a tests folder, in which case we always let deprecations pass
        if (strpos($backtrace[1]['file'], DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR) === false) {
            $path = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $package . DIRECTORY_SEPARATOR;

            if (strpos($backtrace[0]['file'], $path) === false) {
                return;
            }

            if (strpos($backtrace[1]['file'], $path) !== false) {
                return;
            }
        }

        if (array_key_exists($link, self::$ignoredLinks)) {
            self::$ignoredLinks[$link]++;
        } else {
            self::$ignoredLinks[$link] = 1;
        }

        if (self::$deduplication === true && self::$ignoredLinks[$link] > 1) {
            return;
        }

        if (isset(self::$ignoredPackages[$package])) {
            return;
        }

        $message = sprintf($message, ...$args);

        self::delegateTriggerToBackend($message, $backtrace, $link, $package);
    }

    /**
     * @param array<mixed> $backtrace
     */
    private static function delegateTriggerToBackend(string $message, array $backtrace, string $link, string $package): void
    {
        if ((self::$type & self::TYPE_PSR_LOGGER) > 0) {
            $context = [
                'file' => $backtrace[0]['file'],
                'line' => $backtrace[0]['line'],
                'package' => $package,
                'link' => $link,
            ];

            self::$logger->notice($message, $context);
        }

        if (! ((self::$type & self::TYPE_TRIGGER_ERROR) > 0)) {
            return;
        }

        $message .= sprintf(
            ' (%s:%d called by %s:%d, %s, package %s)',
            self::basename($backtrace[0]['file']),
            $backtrace[0]['line'],
            self::basename($backtrace[1]['file']),
            $backtrace[1]['line'],
            $link,
            $package
        );

        @trigger_error($message, E_USER_DEPRECATED);
    }

    /**
     * A non-local-aware version of PHPs basename function.
     */
    private static function basename(string $filename): string
    {
        $pos = strrpos($filename, DIRECTORY_SEPARATOR);

        if ($pos === false) {
            return $filename;
        }

        return substr($filename, $pos + 1);
    }

    public static function enableTrackingDeprecations(): void
    {
        self::$type |= self::TYPE_TRACK_DEPRECATIONS;
    }

    public static function enableWithTriggerError(): void
    {
        self::$type |= self::TYPE_TRIGGER_ERROR;
    }

    public static function enableWithPsrLogger(LoggerInterface $logger): void
    {
        self::$type  |= self::TYPE_PSR_LOGGER;
        self::$logger = $logger;
    }

    public static function withoutDeduplication(): void
    {
        self::$deduplication = false;
    }

    public static function disable(): void
    {
        self::$type          = self::TYPE_NONE;
        self::$logger        = null;
        self::$deduplication = true;

        foreach (self::$ignoredLinks as $link => $count) {
            self::$ignoredLinks[$link] = 0;
        }
    }

    public static function ignorePackage(string $packageName): void
    {
        self::$ignoredPackages[$packageName] = true;
    }

    public static function ignoreDeprecations(string ...$links): void
    {
        foreach ($links as $link) {
            self::$ignoredLinks[$link] = 0;
        }
    }

    public static function getUniqueTriggeredDeprecationsCount(): int
    {
        return array_reduce(self::$ignoredLinks, static function (int $carry, int $count) {
            return $carry + $count;
        }, 0);
    }

    /**
     * Returns each triggered deprecation link identifier and the amount of occurrences.
     *
     * @return array<string,int>
     */
    public static function getTriggeredDeprecations(): array
    {
        return self::$ignoredLinks;
    }
}
