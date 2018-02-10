<?php namespace App\SupportedApps;

class Nextcloud implements Contracts\Applications {
    public function defaultColour()
    {
        return '#2f83c6';
    }
    public function icon()
    {
        return 'supportedapps/nextcloud.png';
    }
}