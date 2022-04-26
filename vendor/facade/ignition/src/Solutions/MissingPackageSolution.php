<?php

namespace Facade\Ignition\Solutions;

use Facade\Ignition\Support\Packagist\Package;
use Facade\IgnitionContracts\Solution;

class MissingPackageSolution implements Solution
{
    /** @var Package */
    protected $possiblePackage;

    public function __construct(Package $possiblePackage)
    {
        $this->possiblePackage = $possiblePackage;
    }

    public function getSolutionTitle(): string
    {
        return 'A composer dependency is missing';
    }

    public function getSolutionDescription(): string
    {
        $output = [
            'You might be missing a composer dependency.',
            'A possible package that was found is `'.$this->possiblePackage->name.'`.',
            '',
            'See if this is the package that you need and install it via `composer require '.$this->possiblePackage->name.'`.',
        ];

        return implode(PHP_EOL, $output);
    }

    public function getDocumentationLinks(): array
    {
        return [
            'Git repository' => $this->possiblePackage->repository,
            'Package on Packagist' => $this->possiblePackage->url,
        ];
    }
}
