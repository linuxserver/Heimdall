<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Proxmox implements Contracts\Applications, Contracts\Livestats {
    public function defaultColour()
    {
        return '#542e0a';
    }
    public function icon()
    {
        return 'supportedapps/proxmox.png';
    }

    public function configDetails()
    {
        //return 'proxmox';
        return null;
    }

    public function testConfig()
    {
        /*$res = $this->buildRequest();
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
        }*/
        return null;
    }

    public function executeConfig()
    {
        /*
        $output = '';
        $res = $this->buildRequest();
        $data = json_decode($res->getBody());

            $output = '
            <ul class="livestats">
                <li><span class="title">Domains<br />Blocked</span><strong>'.$data->domains_being_blocked.'</strong></li>
                <li><span class="title">Blocked<br />Today</span><strong>'.$data->ads_blocked_today.'</span></strong></li>
            </ul>
            ';
        return $output;
        */
        return null;
    }

    public function buildRequest($endpoint='')
    {
        $config = $this->config;

        $username = $config->username;
        $password = $config->password;

        $url = $config->url;
        $url = rtrim($url, '/');

        $api_url = $url.'/api2/json/'.$endpoint.'?username='.$username.'&password='.$password;
        //die( $api_url.' --- ');

        $client = new Client(['http_errors' => false, 'verify' => false ]);
        $res = $client->request('GET', $api_url);
        return $res;

    }


}