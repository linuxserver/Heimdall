<?php namespace App\SupportedApps;

class Openhab implements Contracts\Applications {
    public function defaultColour()
    {
        return '#b7b7b7';
    }
    public function icon()
    {
        return 'supportedapps/openhab.png';
    }
}