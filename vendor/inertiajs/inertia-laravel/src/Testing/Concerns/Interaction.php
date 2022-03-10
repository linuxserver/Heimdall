<?php

namespace Inertia\Testing\Concerns;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;

trait Interaction
{
    /** @var array */
    protected $interacted = [];

    protected function interactsWith(string $key): void
    {
        $prop = Str::before($key, '.');

        if (! in_array($prop, $this->interacted, true)) {
            $this->interacted[] = $prop;
        }
    }

    public function interacted(): void
    {
        PHPUnit::assertSame(
            [],
            array_diff(array_keys($this->prop()), $this->interacted),
            $this->path
                ? sprintf('Unexpected Inertia properties were found in scope [%s].', $this->path)
                : 'Unexpected Inertia properties were found on the root level.'
        );
    }

    public function etc(): self
    {
        $this->interacted = array_keys($this->prop());

        return $this;
    }

    abstract protected function prop(string $key = null);
}
