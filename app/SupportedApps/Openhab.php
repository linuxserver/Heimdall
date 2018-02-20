<?php namespace App\SupportedApps;

class Openhab implements Contracts\Applications {
    public function defaultColour()
    {
        return '#2b2525';
    }
    public function icon()
    {
        return 'supportedapps/openhab.png';
    }
}