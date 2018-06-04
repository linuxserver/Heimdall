<?php namespace App\SupportedApps;

class LibreNMS implements Contracts\Applications {
    public function defaultColour()
    {
        return '#e77';
    }
    public function icon()
    {
        return 'supportedapps/librenms.png';
    }
}
