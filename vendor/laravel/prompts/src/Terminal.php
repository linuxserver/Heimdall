<?php

namespace Laravel\Prompts;

use ReflectionClass;
use RuntimeException;
use Symfony\Component\Console\Terminal as SymfonyTerminal;

class Terminal
{
    /**
     * The initial TTY mode.
     */
    protected ?string $initialTtyMode;

    /**
     * The Symfony Terminal instance.
     */
    protected SymfonyTerminal $terminal;

    /**
     * Create a new Terminal instance.
     */
    public function __construct()
    {
        $this->terminal = new SymfonyTerminal;
    }

    /**
     * Read a line from the terminal.
     */
    public function read(): string
    {
        $input = fread(STDIN, 1024);

        return $input !== false ? $input : '';
    }

    /**
     * Set the TTY mode.
     */
    public function setTty(string $mode): void
    {
        $this->initialTtyMode ??= $this->exec('stty -g');

        $this->exec("stty $mode");
    }

    /**
     * Restore the initial TTY mode.
     */
    public function restoreTty(): void
    {
        if (isset($this->initialTtyMode)) {
            $this->exec("stty {$this->initialTtyMode}");

            $this->initialTtyMode = null;
        }
    }

    /**
     * Get the number of columns in the terminal.
     */
    public function cols(): int
    {
        return $this->terminal->getWidth();
    }

    /**
     * Get the number of lines in the terminal.
     */
    public function lines(): int
    {
        return $this->terminal->getHeight();
    }

    /**
     * (Re)initialize the terminal dimensions.
     */
    public function initDimensions(): void
    {
        (new ReflectionClass($this->terminal))
            ->getMethod('initDimensions')
            ->invoke($this->terminal);
    }

    /**
     * Exit the interactive session.
     */
    public function exit(): void
    {
        exit(1);
    }

    /**
     * Execute the given command and return the output.
     */
    protected function exec(string $command): string
    {
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (! $process) {
            throw new RuntimeException('Failed to create process.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        $code = proc_close($process);

        if ($code !== 0 || $stdout === false) {
            throw new RuntimeException(trim($stderr ?: "Unknown error (code: $code)"), $code);
        }

        return $stdout;
    }
}
