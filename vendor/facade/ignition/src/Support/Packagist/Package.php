<?php

namespace Facade\Ignition\Support\Packagist;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Package
{
    /** @var string */
    public $name;

    /** @var string */
    public $url;

    /** @var string */
    public $repository;

    public function __construct(array $properties)
    {
        $this->name = $properties['name'];

        $this->url = $properties['url'];

        $this->repository = $properties['repository'];
    }

    public function hasNamespaceThatContainsClassName(string $className): bool
    {
        return $this->getNamespaces()->contains(function ($namespace) use ($className) {
            return Str::startsWith(strtolower($className), strtolower($namespace));
        });
    }

    protected function getNamespaces(): Collection
    {
        $details = json_decode(file_get_contents("https://packagist.org/packages/{$this->name}.json"), true);

        return collect($details['package']['versions'])
            ->map(function ($version) {
                return collect($version['autoload'] ?? [])
                    ->map(function ($autoload) {
                        return array_keys($autoload);
                    })
                    ->flatten();
            })
            ->flatten()
            ->unique();
    }
}
