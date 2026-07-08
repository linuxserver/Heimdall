<?php

namespace Spatie\LaravelIgnition\ContextProviders;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Livewire\LivewireManager;

class LaravelLivewireRequestContextProvider extends LaravelRequestContextProvider
{
    public function __construct(
        Request $request,
        protected LivewireManager $livewireManager
    ) {
        parent::__construct($request);
    }

    protected function getComponentClass(string $componentName): ?string
    {
        // Livewire v4
        if (class_exists(\Livewire\Finder\Finder::class)) {
            return app(\Livewire\Finder\Finder::class)
                ->resolveClassComponentClassName($componentName);
        }

        // Livewire v3
        return app(\Livewire\Mechanisms\ComponentRegistry::class)
            ->getClass($componentName);
    }

    /** @return array<string, string> */
    public function getRequest(): array
    {
        $properties = parent::getRequest();

        $properties['method'] = $this->livewireManager->originalMethod();
        $properties['url'] = $this->livewireManager->originalUrl();

        return $properties;
    }

    /** @return array<int|string, mixed> */
    public function toArray(): array
    {
        $properties = parent::toArray();

        $properties['livewire'] = $this->getLivewireInformation();

        return $properties;
    }

    /** @return array<int, mixed> */
    protected function getLivewireInformation(): array
    {
        if ($this->request->has('components')) {
            $data = [];

            $components = $this->request->input('components');

            if (! is_array($components)) {
                return [];
            }

            foreach ($components as $component) {
                $snapshot = json_decode($component['snapshot'], true);

                $class = $this->getComponentClass($snapshot['memo']['name']);

                $data[] = [
                    'component_class' => $class ?? null,
                    'data' => $snapshot['data'],
                    'memo' => $snapshot['memo'],
                    'updates' => $this->resolveUpdates($component['updates']),
                    'calls' => $component['calls'],
                ];
            }

            return $data;
        }

        /** @phpstan-ignore-next-line */
        $componentId = $this->request->input('fingerprint.id');

        /** @phpstan-ignore-next-line */
        $componentAlias = $this->request->input('fingerprint.name');

        if ($componentAlias === null) {
            return [];
        }

        try {
            $componentClass = $this->getComponentClass($componentAlias);
        } catch (Exception $e) {
            $componentClass = null;
        }

        /** @phpstan-ignore-next-line */
        $updates = $this->request->input('updates') ?? [];

        /** @phpstan-ignore-next-line */
        $updates = $this->request->input('updates') ?? [];

        return [
            [
                'component_class' => $componentClass,
                'component_alias' => $componentAlias,
                'component_id' => $componentId,
                'data' => $this->resolveData(),
                'updates' => $this->resolveUpdates($updates),
            ],
        ];
    }

    /** @return array<string, mixed> */
    protected function resolveData(): array
    {
        /** @phpstan-ignore-next-line */
        $data = $this->request->input('serverMemo.data') ?? [];

        /** @phpstan-ignore-next-line */
        $dataMeta = $this->request->input('serverMemo.dataMeta') ?? [];

        foreach ($dataMeta['modelCollections'] ?? [] as $key => $value) {
            $data[$key] = array_merge($data[$key] ?? [], $value);
        }

        foreach ($dataMeta['models'] ?? [] as $key => $value) {
            $data[$key] = array_merge($data[$key] ?? [], $value);
        }

        return $data;
    }

    /** @return array<string, mixed> */
    protected function resolveUpdates(array $updates): array
    {
        /** @phpstan-ignore-next-line */
        $updates = $this->request->input('updates') ?? [];

        return array_map(function (array $update) {
            $update['payload'] = Arr::except($update['payload'] ?? [], ['id']);

            return $update;
        }, $updates);
    }
}
