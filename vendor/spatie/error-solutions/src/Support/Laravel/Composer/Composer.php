<?php

namespace Spatie\ErrorSolutions\Support\Laravel\Composer;

interface Composer
{
    /** @return array<string, mixed> */
    public function getClassMap(): array;

    /** @return array<string, mixed> */
    public function getPrefixes(): array;

    /** @return array<string, mixed> */
    public function getPrefixesPsr4(): array;
}
