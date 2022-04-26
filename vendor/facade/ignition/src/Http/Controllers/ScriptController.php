<?php

namespace Facade\Ignition\Http\Controllers;

use Facade\Ignition\Ignition;
use Illuminate\Http\Request;

class ScriptController
{
    public function __invoke(Request $request)
    {
        if (!isset(Ignition::scripts()[$request->script])) {
            abort(404, 'Script not found');
        }
        return response(
            file_get_contents(
                Ignition::scripts()[$request->script]
            ),
            200,
            ['Content-Type' => 'application/javascript']
        );
    }
}
