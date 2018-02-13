<?php namespace App\SupportedApps;

class Sonarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#0CA0E0';
    }
    public function icon()
    {
        return 'supportedapps/sonarr.png';
    }
}