<?php

namespace Facade\Ignition\Actions;

use Exception;
use Facade\FlareClient\Http\Client;
use Facade\FlareClient\Truncation\ReportTrimmer;
use Facade\Ignition\Exceptions\UnableToShareErrorException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ShareReportAction
{
    /** @var array */
    protected $tabs;

    /** @var \Facade\FlareClient\Http\Client */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(array $report, array $tabs, ?string $lineSelection = null)
    {
        $this->tabs = $tabs;

        $report = $this->filterReport($report);

        try {
            return $this->client->post('public-reports', [
                'report' => $this->trimReport($report),
                'tabs' => $tabs,
                'lineSelection' => $lineSelection,
            ]);
        } catch (Exception $exception) {
            throw new UnableToShareErrorException($exception->getMessage());
        }
    }

    public function filterReport(array $report): array
    {
        if (! $this->hasTab('stackTraceTab')) {
            $report['stacktrace'] = array_slice($report['stacktrace'], 0, 1);
        }

        if (! $this->hasTab('debugTab')) {
            $report['glows'] = [];
        }

        $report['context'] = $this->filterContextItems($report['context']);

        return $report;
    }

    protected function hasTab(string $tab): bool
    {
        return in_array($tab, $this->tabs);
    }

    protected function filterContextItems(array $contextItems): array
    {
        if (! $this->hasTab('requestTab')) {
            $contextItems = $this->removeRequestInformation($contextItems);
        }

        if (! $this->hasTab('appTab')) {
            $contextItems = $this->removeAppInformation($contextItems);
        }

        if (! $this->hasTab('userTab')) {
            $contextItems = $this->removeUserInformation($contextItems);
        }

        if (! $this->hasTab('contextTab')) {
            $contextItems = $this->removeContextInformation($contextItems);
        }

        if (! $this->hasTab('debugTab')) {
            $contextItems = $this->removeDebugInformation($contextItems);
        }

        return $contextItems;
    }

    protected function removeRequestInformation(array $contextItems): array
    {
        Arr::forget($contextItems, 'request');
        Arr::forget($contextItems, 'request_data');
        Arr::forget($contextItems, 'headers');
        Arr::forget($contextItems, 'session');
        Arr::forget($contextItems, 'cookies');

        return $contextItems;
    }

    protected function removeAppInformation(array $contextItems): array
    {
        Arr::forget($contextItems, 'view');
        Arr::forget($contextItems, 'route');

        return $contextItems;
    }

    protected function removeUserInformation(array $contextItems): array
    {
        Arr::forget($contextItems, 'user');
        Arr::forget($contextItems, 'request.ip');
        Arr::forget($contextItems, 'request.useragent');

        return $contextItems;
    }

    protected function removeContextInformation(array $contextItems): array
    {
        Arr::forget($contextItems, 'env');
        Arr::forget($contextItems, 'git');
        Arr::forget($contextItems, 'context');

        Arr::forget($contextItems, $this->getCustomContextGroups($contextItems));

        return $contextItems;
    }

    protected function removeDebugInformation(array $contextItems): array
    {
        Arr::forget($contextItems, 'dumps');
        Arr::forget($contextItems, 'glows');
        Arr::forget($contextItems, 'logs');
        Arr::forget($contextItems, 'queries');

        return $contextItems;
    }

    protected function getCustomContextGroups(array $contextItems): array
    {
        $predefinedContextItemGroups = [
            'request',
            'request_data',
            'headers',
            'session',
            'cookies',
            'view',
            'queries',
            'route',
            'user',
            'env',
            'git',
            'context',
            'logs',
            'dumps',
            'exception',
        ];

        return Collection::make($contextItems)
            ->reject(function ($_value, $group) use ($predefinedContextItemGroups) {
                return in_array($group, $predefinedContextItemGroups);
            })
            ->keys()
            ->toArray();
    }

    protected function trimReport(array $report): array
    {
        return (new ReportTrimmer())->trim($report);
    }
}
