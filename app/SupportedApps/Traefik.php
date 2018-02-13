<?php namespace App\SupportedApps;

class Traefik implements Contracts\Applications {
    public function defaultColour()
    {
        return '#28434a';
    }
    public function icon()
    {
        return 'supportedapps/traefik.png';
    }
}