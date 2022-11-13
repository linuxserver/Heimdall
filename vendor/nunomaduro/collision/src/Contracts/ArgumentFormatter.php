<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts;

/**
 * @internal
 */
interface ArgumentFormatter
{
    /**
     * Formats the provided array of arguments into
     * an understandable description.
     */
    public function format(array $arguments, bool $recursive = true): string;
}
