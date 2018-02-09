<?php namespace App\SupportedApps;

class Plexpy implements Contracts\Applications {
    public function defaultColour()
    {
        return '#e6b453';
    }
    public function icon()
    {
        return 'supportedapps/plexpy.png';
    }
}