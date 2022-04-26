<?php

namespace Facade\Ignition\Views\Compilers;

use ErrorException;
use Illuminate\View\Compilers\BladeCompiler;

class BladeSourceMapCompiler extends BladeCompiler
{
    public function detectLineNumber(string $filename, int $exceptionLineNumber): int
    {
        try {
            $map = $this->compileString(file_get_contents($filename));
        } catch (ErrorException $e) {
            return 1;
        }

        $map = explode("\n", $map);

        $line = $map[$exceptionLineNumber - 1] ?? $exceptionLineNumber;
        $pattern = '/\|---LINE:([0-9]+)---\|/m';

        if (preg_match($pattern, (string)$line, $matches)) {
            return (int)$matches[1];
        }

        return $exceptionLineNumber;
    }

    public function compileString($value)
    {
        try {
            $value = $this->addEchoLineNumbers($value);

            $value = $this->addStatementLineNumbers($value);

            $value = parent::compileString($value);

            return $this->trimEmptyLines($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    protected function addEchoLineNumbers(string $value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);

        if (preg_match_all($pattern, $value, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    protected function addStatementLineNumbers(string $value)
    {
        $shouldInsertLineNumbers = preg_match_all(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            $value,
            $matches,
            PREG_OFFSET_CAPTURE
        );

        if ($shouldInsertLineNumbers) {
            foreach (array_reverse($matches[0]) as $match) {
                $position = mb_strlen(substr($value, 0, $match[1]));

                $value = $this->insertLineNumberAtPosition($position, $value);
            }
        }

        return $value;
    }

    protected function insertLineNumberAtPosition(int $position, string $value)
    {
        $before = mb_substr($value, 0, $position);
        $lineNumber = count(explode("\n", $before));

        return mb_substr($value, 0, $position)."|---LINE:{$lineNumber}---|".mb_substr($value, $position);
    }

    protected function trimEmptyLines(string $value)
    {
        $value = preg_replace('/^\|---LINE:([0-9]+)---\|$/m', '', $value);

        return ltrim($value, PHP_EOL);
    }
}
