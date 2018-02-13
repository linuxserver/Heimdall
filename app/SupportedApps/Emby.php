<?php namespace App\SupportedApps;

class Emby implements Contracts\Applications {
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/emby.png';
    }
}