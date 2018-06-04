<?php namespace App\SupportedApps;

class Headphones implements Contracts\Applications {
    public function defaultColour()
    {
        return '#185';
    }
    public function icon()
    {
        return 'supportedapps/headphones.png';
    }
}
