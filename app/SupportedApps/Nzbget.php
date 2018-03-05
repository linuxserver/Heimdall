<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Nzbget implements Contracts\Applications, Contracts\Livestats {

    public $config;

    public function defaultColour()
    {
        return '#253827';
    }
    public function icon()
    {
        return 'supportedapps/nzbget.png';
    }
    public function configDetails()
    {
        return 'nzbget';
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
        $html = '';
        $active = 'inactive';
        $res = $this->buildRequest('status');
        $data = json_decode($res->getBody());
        //$data->result->RemainingSizeMB = '10000000';
        //$data->result->DownloadRate = '100000000';
        if($data) {
            $size = $data->result->RemainingSizeMB;
            $rate = $data->result->DownloadRate;
            $queue_size = format_bytes($size*1000*1000, false, ' <span>', '</span>');
            $current_speed = format_bytes($rate, false, ' <span>');

            $active = ($size > 0 || $rate > 0) ? 'active' : 'inactive';
            $html = '
            <ul class="livestats">
                <li><span class="title">Queue</span><strong>'.$queue_size.'</strong></li>
                <li><span class="title">Speed</span><strong>'.$current_speed.'/s</span></strong></li>
            </ul>
            ';
        }
        return json_encode(['status' => $active, 'html' => $html]);
    }
    public function buildRequest($endpoint)
    {
        $config = $this->config;
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;

        $rebuild_url = str_replace('http://', 'http://'.$username.':'.$password.'@', $url);
        $rebuild_url = str_replace('https://', 'https://'.$username.':'.$password.'@', $rebuild_url);
        $rebuild_url = rtrim($rebuild_url, '/');


        $api_url = $rebuild_url.'/jsonrpc/'.$endpoint;

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;

    }
   
}