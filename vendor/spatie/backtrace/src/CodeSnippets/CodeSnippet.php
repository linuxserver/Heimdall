<?php

namespace Spatie\Backtrace\CodeSnippets;

use RuntimeException;

class CodeSnippet
{
    /** @var int */
    protected $surroundingLine = 1;

    /** @var int */
    protected $snippetLineCount = 9;

    public function surroundingLine(int $surroundingLine): self
    {
        $this->surroundingLine = $surroundingLine;

        return $this;
    }

    public function snippetLineCount(int $snippetLineCount): self
    {
        $this->snippetLineCount = $snippetLineCount;

        return $this;
    }

    public function get(SnippetProvider $provider): array
    {
        try {
            [$startLineNumber, $endLineNumber] = $this->getBounds($provider->numberOfLines());

            $code = [];

            $line = $provider->getLine($startLineNumber);

            $currentLineNumber = $startLineNumber;

            while ($currentLineNumber <= $endLineNumber) {
                $code[$currentLineNumber] = rtrim(substr($line, 0, 250));

                $line = $provider->getNextLine();
                $currentLineNumber++;
            }

            return $code;
        } catch (RuntimeException $exception) {
            return [];
        }
    }

    public function getAsString(SnippetProvider $provider): string
    {
        $snippet = $this->get($provider);

        $snippetStrings = array_map(function (string $line, string $number) {
            return "{$number} {$line}";
        }, $snippet, array_keys($snippet));

        return implode(PHP_EOL, $snippetStrings);
    }

    protected function getBounds(int $totalNumberOfLineInFile): array
    {
        $startLine = max($this->surroundingLine - floor($this->snippetLineCount / 2), 1);

        $endLine = $startLine + ($this->snippetLineCount - 1);

        if ($endLine > $totalNumberOfLineInFile) {
            $endLine = $totalNumberOfLineInFile;
            $startLine = max($endLine - ($this->snippetLineCount - 1), 1);
        }

        return [$startLine, $endLine];
    }
}
