<?php namespace App\SupportedApps;

class Portainer implements Contracts\Applications {
    public function defaultColour()
    {
        return '#283f44';
    }
    public function icon()
    {
        return 'supportedapps/portainer.png';
    }
}