<?php namespace App\SupportedApps;

class Mcmyadmin implements Contracts\Applications {
    public function defaultColour()
    {
        return '#30404b';
    }
    public function icon()
    {
        return 'supportedapps/mcmyadmin.png';
    }
}