<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Sabnzbd implements Contracts\Applications, Contracts\Livestats {

    public $config;

    public function defaultColour()
    {
        return '#3e3924';
    }
    public function icon()
    {
        return 'supportedapps/sabnzbd.png';
    }
    public function configDetails()
    {
        return 'sabnzbd';
    }
    public function testConfig()
    {
        $res = $this->buildRequest('queue');
        switch($res->getStatusCode()) {
            case 200:
                $data = json_decode($res->getBody());
                if(isset($data->error) && !empty($data->error)) {
                    echo 'Failed: '.$data->error;
                } else {
                    echo 'Successfully connected to the API';
                }
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
        $res = $this->buildRequest('queue');
        $data = json_decode($res->getBody());
        //$data->result->RemainingSizeMB = '10000000';
        //$data->result->DownloadRate = '100000000';
        if($data) {
            $size = $data->queue->mbleft;
            $rate = $data->queue->kbpersec;
            $queue_size = format_bytes($size*1000*1000, false, ' <span>', '</span>');
            $current_speed = format_bytes($rate*1000, false, ' <span>');

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
        $apikey = $config->apikey;

        //print_r($config);
        //die();

        $url = rtrim($url, '/');

        $api_url = $url.'/api?output=json&apikey='.$apikey.'&mode='.$endpoint;
        //die( $api_url.' --- ');

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;

    }
   
}