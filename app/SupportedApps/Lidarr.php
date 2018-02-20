<?php namespace App\SupportedApps;

class Lidarr implements Contracts\Applications {
    public function defaultColour()
    {
        return '#183c18';
    }
    public function icon()
    {
        return 'supportedapps/lidarr.png';
    }
}
