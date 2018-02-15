<?php namespace App\SupportedApps;

class Medusa implements Contracts\Applications {
    public function defaultColour()
    {
        return '#4b5e55';
    }
    public function icon()
    {
        return 'supportedapps/medusa.png';
    }
}