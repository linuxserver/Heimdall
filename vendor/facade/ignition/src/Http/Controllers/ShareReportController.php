<?php

namespace Facade\Ignition\Http\Controllers;

use Facade\Ignition\Actions\ShareReportAction;
use Facade\Ignition\Exceptions\UnableToShareErrorException;
use Facade\Ignition\Http\Requests\ShareReportRequest;

class ShareReportController
{
    public function __invoke(ShareReportRequest $request, ShareReportAction $shareReportAction)
    {
        try {
            return $shareReportAction->handle(json_decode($request->get('report'), true), $request->get('tabs'), $request->get('lineSelection'));
        } catch (UnableToShareErrorException $exception) {
            abort(500, 'Unable to share the error '.$exception->getMessage());
        }
    }
}
