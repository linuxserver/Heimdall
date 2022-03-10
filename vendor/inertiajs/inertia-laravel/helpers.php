<?php

if (! function_exists('inertia')) {
    /**
     * Inertia helper.
     *
     * @param  null|string  $component
     * @param  array|\Illuminate\Contracts\Support\Arrayable  $props
     * @return \Inertia\ResponseFactory|\Inertia\Response
     */
    function inertia($component = null, $props = [])
    {
        $instance = \Inertia\Inertia::getFacadeRoot();

        if ($component) {
            return $instance->render($component, $props);
        }

        return $instance;
    }
}
