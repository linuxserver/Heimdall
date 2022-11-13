<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts;

/**
 * @internal
 */
interface Highlighter
{
    /**
     * Highlights the provided content.
     */
    public function highlight(string $content, int $line): string;
}
