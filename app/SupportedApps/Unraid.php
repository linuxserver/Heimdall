<?php namespace App\SupportedApps;

class Unraid implements Contracts\Applications {
    public function defaultColour()
    {
        return '#BF4C23';
    }
    public function icon()
    {
        return 'supportedapps/unraid.png';
    }
}
