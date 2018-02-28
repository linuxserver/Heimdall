<?php namespace App\SupportedApps;

class Nextcloud implements Contracts\Applications {
    public function defaultColour()
    {
        return '#585e52';
    }
    public function icon()
    {
        return 'supportedapps/gitea.png';
    }
}