<?php namespace App\SupportedApps;

class Radarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#3e3726';
    }
    public function icon()
    {
        return 'supportedapps/radarr.png';
    }
}