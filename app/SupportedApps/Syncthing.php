<?php namespace App\SupportedApps;
class Syncthing implements Contracts\Applications {
    public function defaultColour()
    {
        return '#888';
    }
    public function icon()
    {
        return 'supportedapps/syncthing.png';
    }
}
