<?php namespace App\SupportedApps;

class Plex implements Contracts\Applications {
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/plex.png';
    }
}