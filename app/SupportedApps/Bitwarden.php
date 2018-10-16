<?php namespace App\SupportedApps;

class Bitwarden implements Contracts\Applications {
    public function defaultColour()
    {
        return '#3c8dbc';
    }
    public function icon()
    {
        return 'supportedapps/bitwarden.png';
    }
}