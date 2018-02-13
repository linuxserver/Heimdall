<?php namespace App\SupportedApps;

class Jackett implements Contracts\Applications {
    public function defaultColour()
    {
        return '#AA5';
    }
    public function icon()
    {
        return 'supportedapps/jackett.png';
    }
}