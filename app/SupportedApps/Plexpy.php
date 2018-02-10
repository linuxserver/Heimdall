<?php namespace App\SupportedApps;

class Plexpy implements Contracts\Applications {
    public function defaultColour()
    {
        return '#2d2208';
    }
    public function icon()
    {
        return 'supportedapps/plexpy.png';
    }
}