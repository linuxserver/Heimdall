<?php namespace App\SupportedApps;

class Netdata implements Contracts\Applications {
    public function defaultColour()
    {
        return '#543737';
    }
    public function icon()
    {
        return 'supportedapps/netdata.png';
    }
}