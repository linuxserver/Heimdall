<?php

namespace Facade\FlareClient\Context;

class ConsoleContext implements ContextInterface
{
    /** @var array */
    private $arguments = [];

    public function __construct(array $arguments = [])
    {
        $this->arguments = $arguments;
    }

    public function toArray(): array
    {
        return [
            'arguments' => $this->arguments,
        ];
    }
}
