<?php declare(strict_types=1);
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

use const INI_SCANNER_NORMAL;
use const PHP_BINARY;
use const PHP_SAPI;
use const PHP_VERSION;
use function array_key_exists;
use function array_map;
use function array_merge;
use function assert;
use function escapeshellarg;
use function explode;
use function extension_loaded;
use function in_array;
use function ini_get;
use function ini_get_all;
use function is_array;
use function is_int;
use function is_string;
use function json_decode;
use function parse_ini_file;
use function php_ini_loaded_file;
use function php_ini_scanned_files;
use function phpversion;
use function proc_close;
use function proc_open;
use function sprintf;
use function stream_get_contents;
use function strrpos;
use function version_compare;
use function xdebug_info;

final class Runtime
{
    /**
     * @var ?array<string, string>
     */
    private static ?array $compiledDefaults = null;

    /**
     * Returns true when Xdebug or PCOV is available or
     * the runtime used is PHPDBG.
     */
    public function canCollectCodeCoverage(): bool
    {
        if ($this->hasPHPDBGCodeCoverage()) {
            return true;
        }

        if ($this->hasPCOV()) {
            return true;
        }

        if (!$this->hasXdebug()) {
            return false;
        }

        $xdebugVersion = phpversion('xdebug');

        assert($xdebugVersion !== false);

        if (version_compare($xdebugVersion, '3', '<')) {
            return true;
        }

        $xdebugMode = xdebug_info('mode');

        assert(is_array($xdebugMode));

        if (in_array('coverage', $xdebugMode, true)) {
            return true;
        }

        return false;
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to discard comments.
     */
    public function discardsComments(): bool
    {
        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (ini_get('opcache.save_comments') !== '0') {
            return false;
        }

        return true;
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to perform just-in-time compilation.
     */
    public function performsJustInTimeCompilation(): bool
    {
        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (ini_get('opcache.jit_buffer_size') === '0') {
            return false;
        }

        $jit = (string) ini_get('opcache.jit');

        if (($jit === 'disable') || ($jit === 'off')) {
            return false;
        }

        if (strrpos($jit, '0') === 3) {
            return false;
        }

        return true;
    }

    /**
     * Returns the raw path to the binary of the current runtime.
     *
     * @deprecated
     */
    public function getRawBinary(): string
    {
        return PHP_BINARY;
    }

    /**
     * Returns the escaped path to the binary of the current runtime.
     *
     * @deprecated
     */
    public function getBinary(): string
    {
        return escapeshellarg(PHP_BINARY);
    }

    public function getNameWithVersion(): string
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    public function getNameWithVersionAndCodeCoverageDriver(): string
    {
        if ($this->hasPCOV()) {
            $version = phpversion('pcov');

            assert($version !== false);

            return sprintf(
                '%s with PCOV %s',
                $this->getNameWithVersion(),
                $version,
            );
        }

        if ($this->hasXdebug()) {
            $version = phpversion('xdebug');

            assert($version !== false);

            return sprintf(
                '%s with Xdebug %s',
                $this->getNameWithVersion(),
                $version,
            );
        }

        return $this->getNameWithVersion();
    }

    public function getName(): string
    {
        if ($this->isPHPDBG()) {
            // @codeCoverageIgnoreStart
            return 'PHPDBG';
            // @codeCoverageIgnoreEnd
        }

        return 'PHP';
    }

    public function getVendorUrl(): string
    {
        return 'https://www.php.net/';
    }

    public function getVersion(): string
    {
        return PHP_VERSION;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     */
    public function hasXdebug(): bool
    {
        return $this->isPHP() && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     */
    public function isPHP(): bool
    {
        return !$this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     */
    public function isPHPDBG(): bool
    {
        return PHP_SAPI === 'phpdbg';
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     */
    public function hasPHPDBGCodeCoverage(): bool
    {
        return $this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with PCOV loaded and enabled.
     */
    public function hasPCOV(): bool
    {
        return $this->isPHP() && extension_loaded('pcov') && ini_get('pcov.enabled') === '1';
    }

    /**
     * Parses the loaded php.ini file (if any) as well as all
     * additional php.ini files from the additional ini dir into a
     * single merged map of settings, and also obtains the compiled-in
     * defaults by spawning a `php -n` child once per process.
     * Then checks for each setting passed via the `$values` parameter
     * whether the runtime value (`ini_get()`) differs from what the
     * ini files specified or, when a setting is not configured in any
     * ini file, from the compiled-in default. Returns an array of
     * `key=value` strings for the changed settings.
     *
     * A setting whose runtime value is the empty string but that is
     * absent from both the ini files and the compiled-in defaults is
     * left alone: there is no evidence that it was overridden, so it
     * is not forwarded. This avoids spurious empty overrides for
     * settings of extensions that are not visible to the compiled-in
     * defaults probe (e.g. extensions loaded only via php.ini), which
     * would otherwise break child processes (see
     * https://github.com/sebastianbergmann/environment/issues/99).
     *
     * @param list<string> $values
     *
     * @return array<string, string>
     */
    public function getCurrentSettings(array $values): array
    {
        $iniFileValues    = $this->parseLoadedIniFiles();
        $compiledDefaults = self::compiledDefaults();
        $diff             = [];

        foreach ($values as $value) {
            $set = ini_get($value);

            if ($set === false) {
                continue;
            }

            if (isset($iniFileValues[$value]) && $iniFileValues[$value] === $set) {
                continue;
            }

            if (!isset($iniFileValues[$value]) && ($compiledDefaults[$value] ?? null) === $set) {
                continue;
            }

            if ($set === '' &&
                !isset($iniFileValues[$value]) &&
                !array_key_exists($value, $compiledDefaults)) {
                continue;
            }

            $diff[$value] = sprintf('%s=%s', $value, $set);
        }

        return $diff;
    }

    /**
     * Returns INI settings that cannot be changed via ini_set()
     * (PHP_INI_SYSTEM and PHP_INI_PERDIR) and whose current value
     * differs from the value configured in INI files.
     *
     * These settings can only have been changed via CLI -d flags
     * and must be forwarded as -d flags to child processes because
     * ini_set() cannot change them at runtime.
     *
     * @return array<string, string>
     */
    public function getSettingsNotChangeableAtRuntime(): array
    {
        $allSettings = ini_get_all(null, true);

        assert($allSettings !== false);

        $nonRuntimeSettable = [];

        foreach ($allSettings as $key => $info) {
            assert(is_array($info));
            assert(isset($info['access']));
            assert(is_int($info['access']));

            /**
             * Only consider settings that cannot be changed via ini_set().
             *
             * PHP_INI_USER = 1
             * PHP_INI_PERDIR = 2
             * PHP_INI_SYSTEM = 4
             * PHP_INI_ALL = 7
             */
            if (($info['access'] & 1) !== 0) {
                continue;
            }

            $nonRuntimeSettable[] = $key;
        }

        return $this->getCurrentSettings($nonRuntimeSettable);
    }

    public function isOpcacheActive(): bool
    {
        if (!extension_loaded('Zend OPcache')) {
            return false;
        }

        if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && ini_get('opcache.enable_cli') === '1') {
            return true;
        }

        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' && ini_get('opcache.enable') === '1') {
            return true;
        }

        return false;
    }

    /**
     * @return array<array-key, string>
     */
    private function parseLoadedIniFiles(): array
    {
        $files = [];

        $file = php_ini_loaded_file();

        if ($file !== false) {
            $files[] = $file;
        }

        $scanned = php_ini_scanned_files();

        if ($scanned !== false) {
            $files = array_merge(
                $files,
                array_map(
                    'trim',
                    explode(",\n", $scanned),
                ),
            );
        }

        $merged = [];

        foreach ($files as $ini) {
            $parsed = parse_ini_file($ini, false, INI_SCANNER_NORMAL);

            if ($parsed === false) {
                continue;
            }

            foreach ($parsed as $key => $val) {
                if (is_string($val)) {
                    $merged[$key] = $val;
                }
            }
        }

        return $merged;
    }

    /**
     * Returns the compiled-in default values of every ini setting by
     * spawning a `php -n` child process once and caching the result
     * for the lifetime of the PHP process. When the child cannot be
     * launched or its output is unusable, an empty array is returned
     * and cached so the failure is not retried.
     *
     * @return array<string, string>
     */
    private static function compiledDefaults(): array
    {
        if (self::$compiledDefaults !== null) {
            return self::$compiledDefaults;
        }

        self::$compiledDefaults = [];

        $process = proc_open(
            [PHP_BINARY, '-n', '-r', 'echo json_encode(ini_get_all(null, true));'],
            [1 => ['pipe', 'w']],
            $pipes,
        );

        if ($process === false) {
            return self::$compiledDefaults;
        }

        assert(isset($pipes[1]));

        $stdout = stream_get_contents($pipes[1]);

        proc_close($process);

        if (!is_string($stdout) || $stdout === '') {
            return self::$compiledDefaults;
        }

        $parsed = json_decode($stdout, true);

        if (!is_array($parsed)) {
            return self::$compiledDefaults;
        }

        foreach ($parsed as $key => $info) {
            if (!is_string($key)) {
                continue;
            }

            if (!is_array($info)) {
                continue;
            }

            if (!isset($info['global_value']) || !is_string($info['global_value'])) {
                continue;
            }

            self::$compiledDefaults[$key] = $info['global_value'];
        }

        return self::$compiledDefaults;
    }
}
