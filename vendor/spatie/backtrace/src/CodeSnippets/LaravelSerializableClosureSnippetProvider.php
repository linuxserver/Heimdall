<?php

namespace Spatie\Backtrace\CodeSnippets;

class LaravelSerializableClosureSnippetProvider implements SnippetProvider
{
    /** @var array<string> */
    protected $lines;

    /** @var int */
    protected $counter = 0;

    public function __construct(string $snippet)
    {
        $this->lines = preg_split("/\r\n|\n|\r/", $snippet);

        $this->cleanupLines();
    }

    public function numberOfLines(): int
    {
        return count($this->lines);
    }

    public function getLine(?int $lineNumber = null): string
    {
        if (is_null($lineNumber)) {
            return $this->getNextLine();
        }

        $this->counter = $lineNumber - 1;

        return $this->lines[$lineNumber - 1];
    }

    public function getNextLine(): string
    {
        $this->counter++;

        if ($this->counter >= count($this->lines)) {
            return '';
        }

        return $this->lines[$this->counter];
    }

    protected function cleanupLines(): void
    {
        $spacesOrTabsToRemove = PHP_INT_MAX;

        for ($i = 1; $i < count($this->lines); $i++) {
            if (empty($this->lines[$i])) {
                continue;
            }

            $spacesOrTabsToRemove = min(strspn($this->lines[$i], " \t"), $spacesOrTabsToRemove);
        }

        if ($spacesOrTabsToRemove === PHP_INT_MAX) {
            $spacesOrTabsToRemove = 0;
        }

        for ($i = 1; $i < count($this->lines); $i++) {
            $this->lines[$i] = substr($this->lines[$i], $spacesOrTabsToRemove);
        }
    }
}
