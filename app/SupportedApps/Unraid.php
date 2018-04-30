<?php namespace App\SupportedApps;

class Unraid implements Contracts\Applications {
    public function defaultColour()
    {
        return '#3B5E1F';
    }
    public function icon()
    {
        return 'supportedapps/unraid.png';
    }
}
