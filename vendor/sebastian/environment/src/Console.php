<?php
/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\Environment;

final class Console
{
    /**
     * @var int
     */
    const STDIN  = 0;

    /**
     * @var int
     */
    const STDOUT = 1;

    /**
     * @var int
     */
    const STDERR = 2;

    /**
     * Returns true if STDOUT supports colorization.
     *
     * This code has been copied and adapted from
     * Symfony\Component\Console\Output\OutputStream.
     */
    public function hasColorSupport(): bool
    {
        if ($this->isWindows()) {
            // @codeCoverageIgnoreStart
            return false !== \getenv('ANSICON') || 'ON' === \getenv('ConEmuANSI') || 'xterm' === \getenv('TERM');
            // @codeCoverageIgnoreEnd
        }

        if (!\defined('STDOUT')) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

        return $this->isInteractive(STDOUT);
    }

    /**
     * Returns the number of columns of the terminal.
     *
     * @codeCoverageIgnore
     */
    public function getNumberOfColumns(): int
    {
        if ($this->isWindows()) {
            return $this->getNumberOfColumnsWindows();
        }

        if (!$this->isInteractive(self::STDIN)) {
            return 80;
        }

        return $this->getNumberOfColumnsInteractive();
    }

    /**
     * Returns if the file descriptor is an interactive terminal or not.
     *
     * @param int|resource $fileDescriptor
     */
    public function isInteractive($fileDescriptor = self::STDOUT): bool
    {
        return \function_exists('posix_isatty') && @\posix_isatty($fileDescriptor);
    }

    private function isWindows(): bool
    {
        return DIRECTORY_SEPARATOR === '\\';
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsInteractive(): int
    {
        if (\function_exists('shell_exec') && \preg_match('#\d+ (\d+)#', \shell_exec('stty size') ?? '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        if (\function_exists('shell_exec') && \preg_match('#columns = (\d+);#', \shell_exec('stty') ?? '', $match) === 1) {
            if ((int) $match[1] > 0) {
                return (int) $match[1];
            }
        }

        return 80;
    }

    /**
     * @codeCoverageIgnore
     */
    private function getNumberOfColumnsWindows(): int
    {
        $ansicon = \getenv('ANSICON');
        $columns = 80;

        if (\is_string($ansicon) && \preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', \trim($ansicon), $matches)) {
            $columns = $matches[1];
        } elseif (\function_exists('proc_open')) {
            $process = \proc_open(
                'mode CON',
                [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w']
                ],
                $pipes,
                null,
                null,
                ['suppress_errors' => true]
            );

            if (\is_resource($process)) {
                $info = \stream_get_contents($pipes[1]);

                \fclose($pipes[1]);
                \fclose($pipes[2]);
                \proc_close($process);

                if (\preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches)) {
                    $columns = $matches[2];
                }
            }
        }

        return $columns - 1;
    }
}
