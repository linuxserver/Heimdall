<?php

namespace Laravel\Prompts;

use Closure;
use Laravel\Prompts\Output\ConsoleOutput;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Prompt
{
    use Concerns\Colors;
    use Concerns\Cursor;
    use Concerns\Erase;
    use Concerns\Events;
    use Concerns\FakesInputOutput;
    use Concerns\Fallback;
    use Concerns\Interactivity;
    use Concerns\Themes;

    /**
     * The current state of the prompt.
     */
    public string $state = 'initial';

    /**
     * The error message from the validator.
     */
    public string $error = '';

    /**
     * The previously rendered frame.
     */
    protected string $prevFrame = '';

    /**
     * How many new lines were written by the last output.
     */
    protected int $newLinesWritten = 1;

    /**
     * Whether user input is required.
     */
    public bool|string $required;

    /**
     * The validator callback or rules.
     */
    public mixed $validate;

    /**
     * The cancellation callback.
     */
    protected static Closure $cancelUsing;

    /**
     * Indicates if the prompt has been validated.
     */
    protected bool $validated = false;

    /**
     * The custom validation callback.
     */
    protected static ?Closure $validateUsing;

    /**
     * The output instance.
     */
    protected static OutputInterface $output;

    /**
     * The terminal instance.
     */
    protected static Terminal $terminal;

    /**
     * Get the value of the prompt.
     */
    abstract public function value(): mixed;

    /**
     * Render the prompt and listen for input.
     */
    public function prompt(): mixed
    {
        try {
            $this->capturePreviousNewLines();

            if (static::shouldFallback()) {
                return $this->fallback();
            }

            static::$interactive ??= stream_isatty(STDIN);

            if (! static::$interactive) {
                return $this->default();
            }

            $this->checkEnvironment();

            try {
                static::terminal()->setTty('-icanon -isig -echo');
            } catch (Throwable $e) {
                static::output()->writeln("<comment>{$e->getMessage()}</comment>");
                static::fallbackWhen(true);

                return $this->fallback();
            }

            $this->hideCursor();
            $this->render();

            while (($key = static::terminal()->read()) !== null) {
                $continue = $this->handleKeyPress($key);

                $this->render();

                if ($continue === false || $key === Key::CTRL_C) {
                    if ($key === Key::CTRL_C) {
                        if (isset(static::$cancelUsing)) {
                            return (static::$cancelUsing)();
                        } else {
                            static::terminal()->exit();
                        }
                    }

                    return $this->value();
                }
            }
        } finally {
            $this->clearListeners();
        }
    }

    /**
     * Register a callback to be invoked when a user cancels a prompt.
     */
    public static function cancelUsing(Closure $callback): void
    {
        static::$cancelUsing = $callback;
    }

    /**
     * How many new lines were written by the last output.
     */
    public function newLinesWritten(): int
    {
        return $this->newLinesWritten;
    }

    /**
     * Capture the number of new lines written by the last output.
     */
    protected function capturePreviousNewLines(): void
    {
        $this->newLinesWritten = method_exists(static::output(), 'newLinesWritten')
            ? static::output()->newLinesWritten()
            : 1;
    }

    /**
     * Set the output instance.
     */
    public static function setOutput(OutputInterface $output): void
    {
        self::$output = $output;
    }

    /**
     * Get the current output instance.
     */
    protected static function output(): OutputInterface
    {
        return self::$output ??= new ConsoleOutput();
    }

    /**
     * Write output directly, bypassing newline capture.
     */
    protected static function writeDirectly(string $message): void
    {
        match (true) {
            method_exists(static::output(), 'writeDirectly') => static::output()->writeDirectly($message),
            method_exists(static::output(), 'getOutput') => static::output()->getOutput()->write($message),
            default => static::output()->write($message),
        };
    }

    /**
     * Get the terminal instance.
     */
    public static function terminal(): Terminal
    {
        return static::$terminal ??= new Terminal();
    }

    /**
     * Set the custom validation callback.
     */
    public static function validateUsing(Closure $callback): void
    {
        static::$validateUsing = $callback;
    }

    /**
     * Render the prompt.
     */
    protected function render(): void
    {
        $frame = $this->renderTheme();

        if ($frame === $this->prevFrame) {
            return;
        }

        if ($this->state === 'initial') {
            static::output()->write($frame);

            $this->state = 'active';
            $this->prevFrame = $frame;

            return;
        }

        $this->resetCursorPosition();

        // Ensure that the full frame is buffered so subsequent output can see how many trailing newlines were written.
        if ($this->state === 'submit') {
            $this->eraseDown();
            static::output()->write($frame);

            $this->prevFrame = '';

            return;
        }

        $diff = $this->diffLines($this->prevFrame, $frame);

        if (count($diff) === 1) { // Update the single line that changed.
            $diffLine = $diff[0];
            $this->moveCursor(0, $diffLine);
            $this->eraseLines(1);
            $lines = explode(PHP_EOL, $frame);
            static::output()->write($lines[$diffLine]);
            $this->moveCursor(0, count($lines) - $diffLine - 1);
        } elseif (count($diff) > 1) { // Re-render everything past the first change
            $diffLine = $diff[0];
            $this->moveCursor(0, $diffLine);
            $this->eraseDown();
            $lines = explode(PHP_EOL, $frame);
            $newLines = array_slice($lines, $diffLine);
            static::output()->write(implode(PHP_EOL, $newLines));
        }

        $this->prevFrame = $frame;
    }

    /**
     * Submit the prompt.
     */
    protected function submit(): void
    {
        $this->validate($this->value());

        if ($this->state !== 'error') {
            $this->state = 'submit';
        }
    }

    /**
     * Reset the cursor position to the beginning of the previous frame.
     */
    private function resetCursorPosition(): void
    {
        $lines = count(explode(PHP_EOL, $this->prevFrame)) - 1;

        $this->moveCursor(-999, $lines * -1);
    }

    /**
     * Get the difference between two strings.
     *
     * @return array<int>
     */
    private function diffLines(string $a, string $b): array
    {
        if ($a === $b) {
            return [];
        }

        $aLines = explode(PHP_EOL, $a);
        $bLines = explode(PHP_EOL, $b);
        $diff = [];

        for ($i = 0; $i < max(count($aLines), count($bLines)); $i++) {
            if (! isset($aLines[$i]) || ! isset($bLines[$i]) || $aLines[$i] !== $bLines[$i]) {
                $diff[] = $i;
            }
        }

        return $diff;
    }

    /**
     * Handle a key press and determine whether to continue.
     */
    private function handleKeyPress(string $key): bool
    {
        if ($this->state === 'error') {
            $this->state = 'active';
        }

        $this->emit('key', $key);

        if ($this->state === 'submit') {
            return false;
        }

        if ($key === Key::CTRL_C) {
            $this->state = 'cancel';

            return false;
        }

        if ($this->validated) {
            $this->validate($this->value());
        }

        return true;
    }

    /**
     * Validate the input.
     */
    private function validate(mixed $value): void
    {
        $this->validated = true;

        if ($this->required !== false && $this->isInvalidWhenRequired($value)) {
            $this->state = 'error';
            $this->error = is_string($this->required) && strlen($this->required) > 0 ? $this->required : 'Required.';

            return;
        }

        if (! isset($this->validate) && ! isset(static::$validateUsing)) {
            return;
        }

        $error = match (true) {
            is_callable($this->validate) => ($this->validate)($value),
            isset(static::$validateUsing) => (static::$validateUsing)($this),
            default => throw new RuntimeException('The validation logic is missing.'),
        };

        if (! is_string($error) && ! is_null($error)) {
            throw new RuntimeException('The validator must return a string or null.');
        }

        if (is_string($error) && strlen($error) > 0) {
            $this->state = 'error';
            $this->error = $error;
        }
    }

    /**
     * Determine whether the given value is invalid when the prompt is required.
     */
    protected function isInvalidWhenRequired(mixed $value): bool
    {
        return $value === '' || $value === [] || $value === false || $value === null;
    }

    /**
     * Check whether the environment can support the prompt.
     */
    private function checkEnvironment(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            throw new RuntimeException('Prompts is not currently supported on Windows. Please use WSL or configure a fallback.');
        }
    }

    /**
     * Restore the cursor and terminal state.
     */
    public function __destruct()
    {
        $this->restoreCursor();

        static::terminal()->restoreTty();
    }
}
