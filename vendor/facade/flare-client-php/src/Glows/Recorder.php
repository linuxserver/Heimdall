<?php

namespace Facade\FlareClient\Glows;

class Recorder
{
    public const GLOW_LIMIT = 30;

    private $glows = [];

    public function record(Glow $glow)
    {
        $this->glows[] = $glow;

        $this->glows = array_slice($this->glows, static::GLOW_LIMIT * -1, static::GLOW_LIMIT);
    }

    public function glows(): array
    {
        return $this->glows;
    }

    public function reset()
    {
        $this->glows = [];
    }
}
