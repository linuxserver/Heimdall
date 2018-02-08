<?php namespace App\SupportedApps;

class Pihole implements Contracts\Applications {
    public function defaultColour()
    {
        return '#222';
    }
    public function icon()
    {
        return 'supportedapps/pihole.png';
    }
}