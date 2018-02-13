<?php namespace App\SupportedApps;

class Radarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#CE9100';
    }
    public function icon()
    {
        return 'supportedapps/radarr.png';
    }
}