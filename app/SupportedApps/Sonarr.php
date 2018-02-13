<?php namespace App\SupportedApps;

class Sonarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#5AF';
    }
    public function icon()
    {
        return 'supportedapps/sonarr.png';
    }
}