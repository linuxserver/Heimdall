<?php

namespace Facade\FlareClient\Concerns;

trait HasContext
{
    /** @var string|null */
    private $messageLevel;

    /** @var string|null */
    private $stage;

    /** @var array */
    private $userProvidedContext = [];

    public function stage(?string $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    public function messageLevel(?string $messageLevel)
    {
        $this->messageLevel = $messageLevel;

        return $this;
    }

    public function getGroup(string $groupName = 'context', $default = []): array
    {
        return $this->userProvidedContext[$groupName] ?? $default;
    }

    public function context($key, $value)
    {
        return $this->group('context', [$key => $value]);
    }

    public function group(string $groupName, array $properties)
    {
        $group = $this->userProvidedContext[$groupName] ?? [];

        $this->userProvidedContext[$groupName] = array_merge_recursive_distinct(
            $group,
            $properties
        );

        return $this;
    }
}
