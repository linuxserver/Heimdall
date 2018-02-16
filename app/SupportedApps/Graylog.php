<?php namespace App\SupportedApps;

class Graylog implements Contracts\Applications {
    public function defaultColour()
    {
        return '#158';
    }
    public function icon()
    {
        return 'supportedapps/graylog.png';
    }
}