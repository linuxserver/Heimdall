<?php namespace App\SupportedApps;

class Pfsense implements Contracts\Applications {
    public function defaultColour()
    {
        return '#4e4742';
    }
    public function icon()
    {
        return 'supportedapps/pfsense.png';
    }
}