<?php

namespace Spatie\ErrorSolutions\Solutions\Concerns;

trait IsProvidedByFlare
{
    public function solutionProvidedByName(): string
    {
        return 'Flare';
    }

    public function solutionProvidedByLink(): string
    {
        return 'https://flareapp.io';
    }
}
