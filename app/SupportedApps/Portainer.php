<?php namespace App\SupportedApps;

class Portainer implements Contracts\Applications {
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/portainer.png';
    }
    public function configDetails()
    {
        return null;
    }
}