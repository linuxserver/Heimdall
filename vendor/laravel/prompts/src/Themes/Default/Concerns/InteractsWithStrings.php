<?php

namespace Laravel\Prompts\Themes\Default\Concerns;

trait InteractsWithStrings
{
    /**
     * Get the length of the longest line.
     *
     * @param  array<string>  $lines
     */
    protected function longest(array $lines, int $padding = 0): int
    {
        return max(
            $this->minWidth,
            count($lines) > 0 ? max(array_map(fn ($line) => mb_strwidth($this->stripEscapeSequences($line)) + $padding, $lines)) : null
        );
    }

    /**
     * Pad text ignoring ANSI escape sequences.
     */
    protected function pad(string $text, int $length, string $char = ' '): string
    {
        $rightPadding = str_repeat($char, max(0, $length - mb_strwidth($this->stripEscapeSequences($text))));

        return "{$text}{$rightPadding}";
    }

    /**
     * Strip ANSI escape sequences from the given text.
     */
    protected function stripEscapeSequences(string $text): string
    {
        // Strip ANSI escape sequences.
        $text = preg_replace("/\e[^m]*m/", '', $text);

        // Strip Symfony named style tags.
        $text = preg_replace("/<(info|comment|question|error)>(.*?)<\/\\1>/", '$2', $text);

        // Strip Symfony inline style tags.
        return preg_replace("/<(?:(?:[fb]g|options)=[a-z,;]+)+>(.*?)<\/>/i", '$1', $text);
    }
}
