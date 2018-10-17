<?php namespace App\SupportedApps;

class Webmin implements Contracts\Applications {
    public function defaultColour()
    {
        return '#161b1f';
    }
    public function icon()
    {
        return 'supportedapps/webmin.svg';
    }
}