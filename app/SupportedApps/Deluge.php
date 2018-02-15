<?php namespace App\SupportedApps;

class Deluge implements Contracts\Applications {
    public function defaultColour()
    {
        return '#357';
    }
    public function icon()
    {
        return 'supportedapps/deluge.png';
    }
}