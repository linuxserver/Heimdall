<?php namespace App\SupportedApps;

class Pfsense implements Contracts\Applications {
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/pfsense.png';
    }
    public function configDetails()
    {
        return null;
    }
}