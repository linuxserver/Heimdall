<?php namespace App\SupportedApps;

class Monica implements Contracts\Applications {
    public function defaultColour()
    {
        return '#fafbfc';
    }
    public function icon()
    {
        return 'supportedapps/monica.png';
    }
}