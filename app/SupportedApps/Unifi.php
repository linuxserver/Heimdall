<?php namespace App\SupportedApps;

class Unifi implements Contracts\Applications {
    public function defaultColour()
    {
        return '#363840';
    }
    public function icon()
    {
        return 'supportedapps/unifi.png';
    }
}