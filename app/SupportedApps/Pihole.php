<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Pihole implements Contracts\Applications, Contracts\Livestats {
    public function defaultColour()
    {
        return '#352222';
    }
    public function icon()
    {
        return 'supportedapps/pihole.png';
    }

    public function configDetails()
    {
        return 'pihole';
    }

    public function testConfig()
    {
        $res = $this->buildRequest();
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
        $html = '';
        $active = 'active';
        $res = $this->buildRequest();
        $data = json_decode($res->getBody());

        $html = '
            <ul class="livestats">
                <li><span class="title">Domains<br />Blocked</span><strong>'.$data->domains_being_blocked.'</strong></li>
                <li><span class="title">Blocked<br />Today</span><strong>'.$data->ads_blocked_today.'</span></strong></li>
            </ul>
            ';
        return json_encode(['status' => $active, 'html' => $html]);
    }

    public function buildRequest()
    {
        $config = $this->config;
        $url = $config->url;

        $url = rtrim($url, '/');

        $api_url = $url.'/api.php';
        //die( $api_url.' --- ');

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;

    }


}