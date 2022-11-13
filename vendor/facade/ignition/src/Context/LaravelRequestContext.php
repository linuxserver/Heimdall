<?php

namespace Facade\Ignition\Context;

use Facade\FlareClient\Context\RequestContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Throwable;

class LaravelRequestContext extends RequestContext
{
    /** @var \Illuminate\Http\Request */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUser(): array
    {
        try {
            $user = $this->request->user();

            if (! $user) {
                return [];
            }
        } catch (Throwable $e) {
            return [];
        }

        try {
            if (method_exists($user, 'toFlare')) {
                return $user->toFlare();
            }

            if (method_exists($user, 'toArray')) {
                return $user->toArray();
            }
        } catch (Throwable $e) {
            return [];
        }

        return [];
    }

    public function getRoute(): array
    {
        $route = $this->request->route();

        return [
            'route' => optional($route)->getName(),
            'routeParameters' => $this->getRouteParameters(),
            'controllerAction' => optional($route)->getActionName(),
            'middleware' => array_values(optional($route)->gatherMiddleware() ?? []),
        ];
    }

    protected function getRouteParameters(): array
    {
        try {
            return collect(optional($this->request->route())->parameters ?? [])
                ->map(function ($parameter) {
                    return $parameter instanceof Model ? $parameter->withoutRelations() : $parameter;
                })
                ->map(function ($parameter) {
                    return method_exists($parameter, 'toFlare') ? $parameter->toFlare() : $parameter;
                })
                ->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function toArray(): array
    {
        $properties = parent::toArray();

        $properties['route'] = $this->getRoute();

        $properties['user'] = $this->getUser();

        return $properties;
    }
}
