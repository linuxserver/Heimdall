<?php namespace App\SupportedApps;

class MusicBrainz implements Contracts\Applications {
    public function defaultColour()
    {
        return '#a0a';
    }
    public function icon()
    {
        return 'supportedapps/musicbrainz.png';
    }
}
