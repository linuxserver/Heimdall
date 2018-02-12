<?php namespace App\SupportedApps;

class Jdownloader implements Contracts\Applications {
    public function defaultColour()
    {
        return '#2b494f';
    }
    public function icon()
    {
        return 'supportedapps/jdownloader.png';
    }
}