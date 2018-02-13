<?php namespace App\SupportedApps;

class Duplicati implements Contracts\Applications {
    public function defaultColour()
    {
        return '#2c3744';
    }
    public function icon()
    {
        return 'supportedapps/duplicati.png';
    }
}