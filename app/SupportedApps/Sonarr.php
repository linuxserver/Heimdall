<?php namespace App\SupportedApps;

class Sonarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#163740';
    }
    public function icon()
    {
        return 'supportedapps/sonarr.png';
    }
}