<?php namespace App\SupportedApps;

class Ttrss implements Contracts\Applications {
    public function defaultColour()
    {
        return '#9d704c';
    }
    public function icon()
    {
        return 'supportedapps/tt-rss.png';
    }
}