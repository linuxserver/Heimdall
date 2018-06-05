<?php namespace App\SupportedApps;

class Mylar implements Contracts\Applications {
    public function defaultColour()
    {
        return '#aa0';
    }
    public function icon()
    {
        return 'supportedapps/mylar.png';
    }
}
