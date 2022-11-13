<?php

namespace Facade\Ignition;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class IgnitionConfig implements Arrayable
{
    /** @var array */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $this->mergeWithDefaultConfig($options);
    }

    public function getEditor(): ?string
    {
        return Arr::get($this->options, 'editor');
    }

    public function getRemoteSitesPath(): ?string
    {
        return Arr::get($this->options, 'remote_sites_path');
    }

    public function getLocalSitesPath(): ?string
    {
        return Arr::get($this->options, 'local_sites_path');
    }

    public function getTheme(): ?string
    {
        return Arr::get($this->options, 'theme');
    }

    public function getEnableShareButton(): bool
    {
        if (! app()->isBooted()) {
            return false;
        }

        return Arr::get($this->options, 'enable_share_button', true);
    }

    public function getEnableRunnableSolutions(): bool
    {
        $enabled = Arr::get($this->options, 'enable_runnable_solutions', null);

        if ($enabled === null) {
            $enabled = config('app.debug');
        }

        return $enabled ?? false;
    }

    public function toArray(): array
    {
        return [
            'editor' => $this->getEditor(),
            'remoteSitesPath' => $this->getRemoteSitesPath(),
            'localSitesPath' => $this->getLocalSitesPath(),
            'theme' => $this->getTheme(),
            'enableShareButton' => $this->getEnableShareButton(),
            'enableRunnableSolutions' => $this->getEnableRunnableSolutions(),
            'directorySeparator' => DIRECTORY_SEPARATOR,
        ];
    }

    protected function mergeWithDefaultConfig(array $options = []): array
    {
        return array_merge(config('ignition') ?: include __DIR__.'/../config/ignition.php', $options);
    }
}
