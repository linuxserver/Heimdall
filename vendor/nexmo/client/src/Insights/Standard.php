<?php
namespace Nexmo\Insights;

class Standard extends Basic
{
    public function getCurrentCarrier()
    {
        return $this['current_carrier'];
    }

    public function getOriginalCarrier()
    {
        return $this['original_carrier'];
    }

    public function getPorted()
    {
        return $this['ported'];
    }

    public function getRoaming()
    {
        return $this['roaming'];
    }
}