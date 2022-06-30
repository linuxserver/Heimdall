<?php

namespace Facade\Ignition\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\LivewireManager;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class LivewireComponentParser
{
    /** @var string */
    protected $componentAlias;

    /** @var string */
    protected $componentClass;

    /** @var ReflectionClass */
    protected $reflectionClass;

    public static function create(string $componentAlias): self
    {
        return new self($componentAlias);
    }

    public function __construct(string $componentAlias)
    {
        $this->componentAlias = $componentAlias;
        $this->componentClass = app(LivewireManager::class)->getClass($this->componentAlias);
        $this->reflectionClass = new ReflectionClass($this->componentClass);
    }

    public function getComponentClass(): string
    {
        return $this->componentClass;
    }

    public function getPropertyNamesLike(string $similar): Collection
    {
        $properties = collect($this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC))
            ->reject(function (ReflectionProperty $reflectionProperty) {
                return $reflectionProperty->class !== $this->reflectionClass->name;
            })
            ->map(function (ReflectionProperty $reflectionProperty) {
                return $reflectionProperty->name;
            });

        $computedProperties = collect($this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->class !== $this->reflectionClass->name;
            })
            ->filter(function (ReflectionMethod $reflectionMethod) {
                return str_starts_with($reflectionMethod->name, 'get') && str_ends_with($reflectionMethod->name, 'Property');
            })
            ->map(function (ReflectionMethod $reflectionMethod) {
                return lcfirst(Str::of($reflectionMethod->name)->after('get')->before('Property'));
            });

        return $this->filterItemsBySimilarity(
            $properties->merge($computedProperties),
            $similar
        );
    }

    public function getMethodNamesLike(string $similar): Collection
    {
        $methods = collect($this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->class !== $this->reflectionClass->name;
            })
            ->map(function (ReflectionMethod $reflectionMethod) {
                return $reflectionMethod->name;
            });

        return $this->filterItemsBySimilarity($methods, $similar);
    }

    protected function filterItemsBySimilarity(Collection $items, string $similar): Collection
    {
        return $items
            ->map(function (string $name) use ($similar) {
                similar_text($similar, $name, $percentage);

                return ['match' => $percentage, 'value' => $name];
            })
            ->sortByDesc('match')
            ->filter(function (array $item) {
                return $item['match'] > 40;
            })
            ->map(function (array $item) {
                return $item['value'];
            })
            ->values();
    }
}
