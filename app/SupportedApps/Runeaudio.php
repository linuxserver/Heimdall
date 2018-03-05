<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Runeaudio implements Contracts\Applications, Contracts\Livestats {
    public function defaultColour()
    {
        return '#05A';
    }
    public function icon()
    {
        return 'supportedapps/runeaudio.png';
    }

    public function configDetails()
    {
        return 'runeaudio';
    }

    public function testConfig()
    {
        $res = $this->buildRequest('status');
        switch($res->getStatusCode()) {
            case 200:
                echo 'Successfully connected to the API';
                break;
            case 401:
                echo 'Failed: Invalid credentials';
                break;
            case 404:
                echo 'Failed: Please make sure your URL is correct and that there is a trailing slash';
                break;
            default:
                echo 'Something went wrong... Code: '.$res->getStatusCode();
                break;
        }
    }

    public function executeConfig()
    {
        $output = '';
        $active = 'active';
        $artist = '';
        $song_title = '';
        $res = $this->buildRequest('currentsong');
        $array = explode("\n", $res->getBody());
        foreach($array as $item) {
            $item_array = explode(": ", $item);
            if ($item_array[0] == 'Artist') {
                $artist = $item_array[1];
            } elseif ($item_array[0] == 'Title') {
                $song_title = $item_array[1];
            }
        }

        $output = '<ul class="livestats">';

        if (strlen($artist) > 12) {
            $output = $output.'<li><span class="title-marquee"><span>'.$artist.'</span></span></li>';
        } else {
            $output = $output.'<li><span class="title">'.$artist.'</span></li>';
        }

        $output = $output.'</ul><ul class="livestats">';

        if (strlen($song_title) > 12) {
            $output = $output.'<li><span class="title-marquee"><span>'.$song_title.'</span></span></li>';
        } else {
            $output = $output.'<li><span class="title">'.$song_title.'</span></li>';
        }

        $output = $output.'</ul>';

        return json_encode(['status' => $active, 'html' => $output]);
    }

    public function buildRequest($endpoint)
    {
        $config = $this->config;
        $url = $config->url;

        $url = rtrim($url, '/');

        $api_url = $url.'/command/?cmd='.$endpoint;
        //die( $api_url.' --- ');

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;

    }


}
