
<?php namespace App\SupportedApps;
class AirSonic implements Contracts\Applications {
    public function defaultColour()
    {
        return '#5AF';
    }
    public function icon()
    {
        return 'supportedapps/airsonic.png';
    }
}
