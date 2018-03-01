<?php namespace App\SupportedApps;

class Sickrage implements Contracts\Applications {
    public function defaultColour()
    {
        return '#6185a6';
    }
    public function icon()
    {
        return 'supportedapps/sickrage.png';
    }
}