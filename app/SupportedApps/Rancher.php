<?php namespace App\SupportedApps;

class Rancher implements Contracts\Applications {
    public function defaultColour()
    {
        return '#78c9cf';
    }
    public function icon()
    {
        return 'supportedapps/rancher.png';
    }
}
