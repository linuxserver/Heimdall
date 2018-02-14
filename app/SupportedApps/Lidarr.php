<?php namespace App\SupportedApps;

class Lidarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#040';
    }
    public function icon()
    {
        return 'supportedapps/lidarr.png';
    }
}