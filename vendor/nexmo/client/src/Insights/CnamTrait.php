<?php
namespace Nexmo\Insights;

trait CnamTrait {

    public function getCallerName()
    {
        return $this['caller_name'];
    }

    public function getFirstName()
    {
        return $this['first_name'];
    }

    public function getLastName()
    {
        return $this['last_name'];
    }

    public function getCallerType()
    {
        return $this['caller_type'];
    }
}