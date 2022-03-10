<?php

namespace Inertia;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        if (config('app.asset_url')) {
            return md5(config('app.asset_url'));
        }

        if (file_exists($manifest = public_path('mix-manifest.json'))) {
            return md5_file($manifest);
        }

        return null;
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return [
            'errors' => function () use ($request) {
                return $this->resolveValidationErrors($request);
            },
        ];
    }

    /**
     * Sets the root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @param  Request  $request
     * @return string
     */
    public function rootView(Request $request)
    {
        return $this->rootView;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        Inertia::version(function () use ($request) {
            return $this->version($request);
        });

        Inertia::share($this->share($request));

        Inertia::setRootView($this->rootView($request));

        $response = $next($request);
        $response = $this->checkVersion($request, $response);

        return $this->changeRedirectCode($request, $response);
    }

    /**
     * In the event that the assets change, initiate a
     * client-side location visit to force an update.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return Response
     */
    public function checkVersion(Request $request, Response $response)
    {
        if ($request->header('X-Inertia') &&
            $request->method() === 'GET' &&
            $request->header('X-Inertia-Version', '') !== Inertia::getVersion()
        ) {
            if ($request->hasSession()) {
                $request->session()->reflash();
            }

            return Inertia::location($request->fullUrl());
        }

        return $response;
    }

    /**
     * Changes the status code during redirects, ensuring they are made as
     * GET requests, preventing "MethodNotAllowedHttpException" errors.
     *
     * @param  Request  $request
     * @param  Response  $response
     * @return Response
     */
    public function changeRedirectCode(Request $request, Response $response)
    {
        if ($request->header('X-Inertia') &&
            $response->getStatusCode() === 302 &&
            in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])
        ) {
            $response->setStatusCode(303);
        }

        return $response;
    }

    /**
     * Resolves and prepares validation errors in such
     * a way that they are easier to use client-side.
     *
     * @param  Request  $request
     * @return object
     */
    public function resolveValidationErrors(Request $request)
    {
        if (! $request->session()->has('errors')) {
            return (object) [];
        }

        return (object) collect($request->session()->get('errors')->getBags())->map(function ($bag) {
            return (object) collect($bag->messages())->map(function ($errors) {
                return $errors[0];
            })->toArray();
        })->pipe(function ($bags) use ($request) {
            if ($bags->has('default') && $request->header('x-inertia-error-bag')) {
                return [$request->header('x-inertia-error-bag') => $bags->get('default')];
            }

            if ($bags->has('default')) {
                return $bags->get('default');
            }

            return $bags->toArray();
        });
    }
}
