<?php

namespace Facade\FlareClient\Glows;

use Facade\FlareClient\Concerns\UsesTime;
use Facade\FlareClient\Enums\MessageLevels;

class Glow
{
    use UsesTime;

    /** @var string */
    private $name;

    /** @var array */
    private $metaData;

    /** @var string */
    private $messageLevel;

    /** @var float */
    private $microtime;

    public function __construct(string $name, string $messageLevel = MessageLevels::INFO, array $metaData = [], ?float $microtime = null)
    {
        $this->name = $name;
        $this->messageLevel = $messageLevel;
        $this->metaData = $metaData;
        $this->microtime = $microtime ?? microtime(true);
    }

    public function toArray()
    {
        return [
            'time' => $this->getCurrentTime(),
            'name' => $this->name,
            'message_level' => $this->messageLevel,
            'meta_data' => $this->metaData,
            'microtime' => $this->microtime,
        ];
    }
}
