<?php
namespace Nexmo\Insights;

class Advanced extends Standard
{
    public function getValidNumber()
    {
        return $this['valid_number'];
    }

    public function getReachable()
    {
        return $this['reachable'];
    }
}