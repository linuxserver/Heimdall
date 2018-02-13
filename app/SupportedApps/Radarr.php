<?php namespace App\SupportedApps;

class Radarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#AA5';
    }
    public function icon()
    {
        return 'supportedapps/radarr.png';
    }
}