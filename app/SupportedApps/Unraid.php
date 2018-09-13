<?php namespace App\SupportedApps;

class Unraid implements Contracts\Applications {
    public function defaultColour()
    {
        return '#A12624';
    }
    public function icon()
    {
        return 'supportedapps/unraid.png';
    }
}
