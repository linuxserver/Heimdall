<?php

namespace Facade\FlareClient\Truncation;

interface TruncationStrategy
{
    public function execute(array $payload): array;
}
