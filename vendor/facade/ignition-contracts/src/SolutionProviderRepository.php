<?php

namespace Facade\IgnitionContracts;

use Throwable;

interface SolutionProviderRepository
{
    public function registerSolutionProvider(string $solutionProviderClass): self;

    public function registerSolutionProviders(array $solutionProviderClasses): self;

    /**
     * @param Throwable $throwable
     * @return \Facade\IgnitionContracts\Solution[]
     */
    public function getSolutionsForThrowable(Throwable $throwable): array;

    public function getSolutionForClass(string $solutionClass): ?Solution;
}
