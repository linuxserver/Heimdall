<?php

namespace Spatie\ErrorSolutions\Support\Laravel;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\LivewireManager;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class LivewireComponentParser
{
    /** @var class-string<Component> */
    protected string $componentClass;

    /** @var ReflectionClass<Component> */
    protected ReflectionClass $reflectionClass;

    public static function create(string $componentAlias): self
    {
        return new self($componentAlias);
    }

    public function __construct(protected string $componentAlias)
    {
        $this->componentClass = app(LivewireManager::class)->getClass($this->componentAlias);
        $this->reflectionClass = new ReflectionClass($this->componentClass);
    }

    public function getComponentClass(): string
    {
        return $this->componentClass;
    }

    /** @return Collection<int, string> */
    public function getPropertyNamesLike(string $similar): Collection
    {
        $properties = collect($this->reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC))
            ->reject(fn (ReflectionProperty $reflectionProperty) => $reflectionProperty->class !== $this->reflectionClass->name)
            ->map(fn (ReflectionProperty $reflectionProperty) => $reflectionProperty->name);

        $computedProperties = collect($this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(fn (ReflectionMethod $reflectionMethod) => $reflectionMethod->class !== $this->reflectionClass->name)
            ->filter(fn (ReflectionMethod $reflectionMethod) => str_starts_with($reflectionMethod->name, 'get') && str_ends_with($reflectionMethod->name, 'Property'))
            ->map(fn (ReflectionMethod $reflectionMethod) => lcfirst(Str::of($reflectionMethod->name)->after('get')->before('Property')));

        return $this->filterItemsBySimilarity(
            $properties->merge($computedProperties),
            $similar
        );
    }

    /** @return Collection<int, string> */
    public function getMethodNamesLike(string $similar): Collection
    {
        $methods = collect($this->reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC))
            ->reject(fn (ReflectionMethod $reflectionMethod) => $reflectionMethod->class !== $this->reflectionClass->name)
            ->map(fn (ReflectionMethod $reflectionMethod) => $reflectionMethod->name);

        return $this->filterItemsBySimilarity($methods, $similar);
    }

    /**
     * @param Collection<int, string> $items
     *
     * @return Collection<int, string>
     */
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
