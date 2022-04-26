<?php

namespace Facade\Ignition\Http\Controllers;

use Facade\Ignition\Ignition;
use Illuminate\Http\Request;

class StyleController
{
    public function __invoke(Request $request)
    {
        return response(
            file_get_contents(Ignition::styles()[$request->style]),
            200,
            ['Content-Type' => 'text/css']
        );
    }
}
