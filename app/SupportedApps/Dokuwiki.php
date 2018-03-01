<?php namespace App\SupportedApps;

class Dokuwiki implements Contracts\Applications {
    public function defaultColour()
    {
        return '#9d7056';
    }
    public function icon()
    {
        return 'supportedapps/dokuwiki.png';
    }
}