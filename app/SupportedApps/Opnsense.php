<?php namespace App\SupportedApps;

class Opnsense implements Contracts\Applications {
    public function defaultColour()
    {
        return '#211914';
    }
    public function icon()
    {
        return 'supportedapps/opnsense.png';
    }
}