<?php

namespace Inertia\Testing\Concerns;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Assert as PHPUnit;

trait Matching
{
    public function whereAll(array $bindings): self
    {
        foreach ($bindings as $key => $value) {
            $this->where($key, $value);
        }

        return $this;
    }

    public function where(string $key, $expected): self
    {
        $this->has($key);

        $actual = $this->prop($key);

        if ($expected instanceof Closure) {
            PHPUnit::assertTrue(
                $expected(is_array($actual) ? Collection::make($actual) : $actual),
                sprintf('Inertia property [%s] was marked as invalid using a closure.', $this->dotPath($key))
            );

            return $this;
        }

        if ($expected instanceof Arrayable) {
            $expected = $expected->toArray();
        } elseif ($expected instanceof ResourceResponse || $expected instanceof JsonResource) {
            $expected = json_decode(json_encode($expected->toResponse(request())->getData()), true);
        }

        $this->ensureSorted($expected);
        $this->ensureSorted($actual);

        PHPUnit::assertSame(
            $expected,
            $actual,
            sprintf('Inertia property [%s] does not match the expected value.', $this->dotPath($key))
        );

        return $this;
    }

    protected function ensureSorted(&$value): void
    {
        if (! is_array($value)) {
            return;
        }

        foreach ($value as &$arg) {
            $this->ensureSorted($arg);
        }

        ksort($value);
    }

    abstract protected function dotPath(string $key): string;

    abstract protected function prop(string $key = null);

    abstract public function has(string $key, $value = null, Closure $scope = null);
}
