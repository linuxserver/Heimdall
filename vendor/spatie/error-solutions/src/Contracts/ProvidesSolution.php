<?php

namespace Spatie\ErrorSolutions\Contracts;

/**
 * Interface to be used on exceptions that provide their own solution.
 */
interface ProvidesSolution
{
    public function getSolution(): Solution;
}
