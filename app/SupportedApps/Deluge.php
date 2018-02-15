<?php namespace App\SupportedApps;

class Deluge implements Contracts\Applications {
    public function defaultColour()
    {
        return '#98b0cc';
    }
    public function icon()
    {
        return 'supportedapps/deluge.png';
    }
}