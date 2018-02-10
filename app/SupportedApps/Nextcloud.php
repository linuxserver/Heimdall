<?php namespace App\SupportedApps;

class Nextcloud implements Contracts\Applications {
    public function defaultColour()
    {
        return '#0e2c3e';
    }
    public function icon()
    {
        return 'supportedapps/nextcloud.png';
    }
}