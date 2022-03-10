<?php

namespace Inertia\Testing;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;

class Assert implements Arrayable
{
    use Concerns\Has,
        Concerns\Matching,
        Concerns\Debugging,
        Concerns\PageObject,
        Concerns\Interaction,
        Macroable;

    /** @var string */
    private $component;

    /** @var array */
    private $props;

    /** @var string */
    private $url;

    /** @var string|null */
    private $version;

    /** @var string */
    private $path;

    protected function __construct(string $component, array $props, string $url, string $version = null, string $path = null)
    {
        echo "\033[0;31mInertia's built-in 'Assert' library will be removed in a future version of inertia-laravel:\033[0m\n";
        echo "\033[0;31m - If you are seeing this error while using \$response->assertInertia(...), please upgrade to Laravel 8.32.0 or higher.\033[0m\n";
        echo "\033[0;31m - If you are using the 'Assert' class directly, please adapt your tests to use the 'AssertableInertia' class instead.\033[0m\n";
        echo "\033[0;31mFor more information and questions, please see https://github.com/inertiajs/inertia-laravel/pull/338 \033[0m\n\n";
        @trigger_error("Inertia's built-in 'Assert' library will be removed in a future version of inertia-laravel: https://github.com/inertiajs/inertia-laravel/pull/338", \E_USER_DEPRECATED);

        $this->path = $path;

        $this->component = $component;
        $this->props = $props;
        $this->url = $url;
        $this->version = $version;
    }

    protected function dotPath(string $key): string
    {
        if (is_null($this->path)) {
            return $key;
        }

        return implode('.', [$this->path, $key]);
    }

    protected function scope(string $key, Closure $callback): self
    {
        $props = $this->prop($key);
        $path = $this->dotPath($key);

        PHPUnit::assertIsArray($props, sprintf('Inertia property [%s] is not scopeable.', $path));

        $scope = new self($this->component, $props, $this->url, $this->version, $path);
        $callback($scope);
        $scope->interacted();

        return $this;
    }

    public static function fromTestResponse($response): self
    {
        try {
            $response->assertViewHas('page');
            $page = json_decode(json_encode($response->viewData('page')), true);

            PHPUnit::assertIsArray($page);
            PHPUnit::assertArrayHasKey('component', $page);
            PHPUnit::assertArrayHasKey('props', $page);
            PHPUnit::assertArrayHasKey('url', $page);
            PHPUnit::assertArrayHasKey('version', $page);
        } catch (AssertionFailedError $e) {
            PHPUnit::fail('Not a valid Inertia response.');
        }

        return new self($page['component'], $page['props'], $page['url'], $page['version']);
    }
}
