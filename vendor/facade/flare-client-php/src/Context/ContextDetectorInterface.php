<?php

namespace Facade\FlareClient\Context;

interface ContextDetectorInterface
{
    public function detectCurrentContext(): ContextInterface;
}
