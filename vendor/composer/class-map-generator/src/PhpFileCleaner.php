<?php declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\ClassMapGenerator;

use Composer\Pcre\Preg;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @internal
 */
class PhpFileCleaner
{
    /** @var array<array{name: string, length: int, pattern: non-empty-string}> */
    private static $typeConfig;

    /** @var non-empty-string */
    private static $rejectChars;

    /**
     * @readonly
     * @var string
     */
    private $contents;

    /**
     * @readonly
     * @var int
     */
    private $len;

    /**
     * @readonly
     * @var int
     */
    private $maxMatches;

    /** @var int */
    private $index = 0;

    /**
     * @param string[] $types
     */
    public static function setTypeConfig(array $types): void
    {
        foreach ($types as $type) {
            self::$typeConfig[$type[0]] = [
                'name' => $type,
                'length' => \strlen($type),
                'pattern' => '{.\b(?<![\$:>])'.$type.'\s++[a-zA-Z_\x7f-\xff:][a-zA-Z0-9_\x7f-\xff:\-]*+}Ais',
            ];
        }

        self::$rejectChars = '?"\'</'.implode('', array_keys(self::$typeConfig));
    }

    public function __construct(string $contents, int $maxMatches)
    {
        $this->contents = $contents;
        $this->len = \strlen($this->contents);
        $this->maxMatches = $maxMatches;
    }

    public function clean(): string
    {
        $clean = '';

        while ($this->index < $this->len) {
            $this->skipToPhp();
            $clean .= '<?';

            while ($this->index < $this->len) {
                $char = $this->contents[$this->index];
                if ($char === '?' && $this->peek('>')) {
                    $clean .= '?>';
                    $this->index += 2;
                    continue 2;
                }

                if ($char === '"') {
                    $this->skipString('"');
                    $clean .= 'null';
                    continue;
                }

                if ($char === "'") {
                    $this->skipString("'");
                    $clean .= 'null';
                    continue;
                }

                if ($char === "<" && $this->peek('<') && $this->match('{<<<[ \t]*+([\'"]?)([a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*+)\\1(?:\r\n|\n|\r)}A', $match)) {
                    $this->index += \strlen($match[0]);
                    $this->skipHeredoc($match[2]);
                    $clean .= 'null';
                    continue;
                }

                if ($char === '/') {
                    if ($this->peek('/')) {
                        $this->skipToNewline();
                        continue;
                    }

                    if ($this->peek('*')) {
                        $this->skipComment();
                        continue;
                    }
                }

                if ($this->maxMatches === 1 && isset(self::$typeConfig[$char])) {
                    $type = self::$typeConfig[$char];
                    if (
                        substr($this->contents, $this->index, $type['length']) === $type['name']
                        && Preg::isMatch($type['pattern'], $this->contents, $match, 0, $this->index - 1)
                    ) {
                        return $clean . $match[0];
                    }
                }

                $this->index += 1;
                $skip = strcspn($this->contents, self::$rejectChars, $this->index);
                if ($skip > 0) {
                    $clean .= $char . substr($this->contents, $this->index, $skip);
                    $this->index += $skip;
                } else {
                    $clean .= $char;
                }
            }
        }

        return $clean;
    }

    private function skipToPhp(): void
    {
        while ($this->index < $this->len) {
            if ($this->contents[$this->index] === '<' && $this->peek('?')) {
                $this->index += 2;
                break;
            }

            $this->index += 1;
        }
    }

    private function skipString(string $delimiter): void
    {
        $rejectChars = '\\' . $delimiter;
        $this->index += 1;
        while ($this->index < $this->len) {
            $this->index += strcspn($this->contents, $rejectChars, $this->index);
            if ($this->index >= $this->len) {
                break;
            }

            if ($this->contents[$this->index] === '\\' && ($this->peek('\\') || $this->peek($delimiter))) {
                $this->index += 2;
                continue;
            }

            if ($this->contents[$this->index] === $delimiter) {
                $this->index += 1;
                break;
            }

            $this->index += 1;
        }
    }

    private function skipComment(): void
    {
        $this->index += 2;
        while ($this->index < $this->len) {
            $this->index += strcspn($this->contents, '*', $this->index);

            if ($this->peek('/')) {
                $this->index += 2;
                break;
            }

            $this->index += 1;
        }
    }

    private function skipToNewline(): void
    {
        $this->index += strcspn($this->contents, "\r\n", $this->index);
    }

    private function skipHeredoc(string $delimiter): void
    {
        $firstDelimiterChar = $delimiter[0];
        $delimiterLength = \strlen($delimiter);
        $delimiterPattern = '{'.preg_quote($delimiter).'(?![a-zA-Z0-9_\x80-\xff])}A';

        while ($this->index < $this->len) {
            // check if we find the delimiter after some spaces/tabs
            switch ($this->contents[$this->index]) {
                case "\t":
                case " ":
                    $this->index += 1;
                    continue 2;
                case $firstDelimiterChar:
                    if (
                        substr($this->contents, $this->index, $delimiterLength) === $delimiter
                        && $this->match($delimiterPattern)
                    ) {
                        $this->index += $delimiterLength;

                        return;
                    }

                    break;
            }

            // skip the rest of the line
            $this->skipToNewline();

            // skip newlines
            $this->index += strspn($this->contents, "\r\n", $this->index);
        }
    }

    private function peek(string $char): bool
    {
        return $this->index + 1 < $this->len && $this->contents[$this->index + 1] === $char;
    }

    /**
     * @param non-empty-string $regex
     * @param null|array<mixed> $match
     * @param-out array<int|string, string> $match
     */
    private function match(string $regex, ?array &$match = null): bool
    {
        return Preg::isMatchStrictGroups($regex, $this->contents, $match, 0, $this->index);
    }
}
