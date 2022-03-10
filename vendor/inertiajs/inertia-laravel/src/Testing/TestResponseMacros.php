<?php

namespace Inertia\Testing;

use Closure;
use Illuminate\Testing\Fluent\AssertableJson;

class TestResponseMacros
{
    public function assertInertia()
    {
        return function (Closure $callback = null) {
            if (class_exists(AssertableJson::class)) {
                $assert = AssertableInertia::fromTestResponse($this);
            } else {
                $assert = Assert::fromTestResponse($this);
            }

            if (is_null($callback)) {
                return $this;
            }

            $callback($assert);

            return $this;
        };
    }

    public function inertiaPage()
    {
        return function () {
            if (class_exists(AssertableJson::class)) {
                return AssertableInertia::fromTestResponse($this)->toArray();
            }

            return Assert::fromTestResponse($this)->toArray();
        };
    }
}
