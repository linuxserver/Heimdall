<?php

use Facade\FlareClient\Flare;

if (! function_exists('ddd')) {
    function ddd()
    {
        $args = func_get_args();

        if (count($args) === 0) {
            throw new Exception('You should pass at least 1 argument to `ddd`');
        }

        call_user_func_array('dump', $args);

        $handler = app(\Facade\Ignition\ErrorPage\ErrorPageHandler::class);

        $client = app()->make(Flare::class);

        $report = $client->createReportFromMessage('Dump, Die, Debug', 'info');

        $handler->handleReport($report, 'DebugTab', [
            'dump' => true,
            'glow' => false,
            'log' => false,
            'query' => false,
        ]);

        die();
    }
}
