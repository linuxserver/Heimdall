<?php namespace App\SupportedApps;
class AirSonic implements Contracts\Applications {
    public function defaultColour()
    {
        return '#08F';
    }
    public function icon()
    {
        return 'supportedapps/airsonic.png';
    }
}
