<?php namespace App\SupportedApps;

class Gitea implements Contracts\Applications {
    public function defaultColour()
    {
        return '#585e52';
    }
    public function icon()
    {
        return 'supportedapps/gitea.png';
    }
}