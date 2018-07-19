<?php namespace App\SupportedApps;

class Pfsense implements Contracts\Applications {
    public function defaultColour()
    {
        return '#243699';
    }
    public function icon()
    {
        return 'supportedapps/pfsense.png';
    }
}
