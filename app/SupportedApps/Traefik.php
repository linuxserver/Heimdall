<?php namespace App\SupportedApps;

class Traefik implements Contracts\Applications {
    public function defaultColour()
    {
        return '#427d8c';
    }
    public function icon()
    {
        return 'supportedapps/traefik.png';
    }
}