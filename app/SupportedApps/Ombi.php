<?php namespace App\SupportedApps;

class Ombi implements Contracts\Applications {
    public function defaultColour()
    {
        return '#150f09';
    }
    public function icon()
    {
        return 'supportedapps/ombi.png';
    }
}