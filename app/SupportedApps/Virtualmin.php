<?php namespace App\SupportedApps;

class Virtualmin implements Contracts\Applications {
    public function defaultColour()
    {
        return '#161b1f';
    }
    public function icon()
    {
        return 'supportedapps/virtualmin.svg';
    }
}