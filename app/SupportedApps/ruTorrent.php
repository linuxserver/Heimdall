<?php namespace App\SupportedApps;

class ruTorrent implements Contracts\Applications {
    public function defaultColour()
    {
        return '#004';
    }
    public function icon()
    {
        return 'supportedapps/rutorrent.png';
    }
}
