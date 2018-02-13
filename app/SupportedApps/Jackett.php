<?php namespace App\SupportedApps;

class Jackett implements Contracts\Applications {
    public function defaultColour()
    {
        return '#484814';
    }
    public function icon()
    {
        return 'supportedapps/jackett.png';
    }
}