<?php namespace App\SupportedApps;

class Grafana implements Contracts\Applications {
    public function defaultColour()
    {
        return '#a56e4d';
    }
    public function icon()
    {
        return 'supportedapps/grafana.png';
    }
}
