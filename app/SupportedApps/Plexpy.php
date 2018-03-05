<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Plexpy implements Contracts\Applications, Contracts\Livestats {

    public $config;

    public function defaultColour()
    {
        return '#2d2208';
    }
    public function icon()
    {
        return 'supportedapps/plexpy.png';
    }
    public function configDetails()
    {
        return 'plexpy';
    }
    public function testConfig()
    {
        $res = $this->buildRequest('arnold');
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
        $active = 'active';
        $res = $this->buildRequest('get_activity');
        $data = json_decode($res->getBody());
        $stream_count = $data->response->data->stream_count;

        $html = '
        <ul class="livestats">
            <li><span class="title">Stream Count</span><strong>'.$stream_count.'</strong></li>
        </ul>
        ';

        return json_encode(['status' => $active, 'html' => $html]);
    }
    public function buildRequest($endpoint)
    {
        $config = $this->config;
        $url = $config->url;
        $apikey = $config->apikey;

        $url = rtrim($url, '/');

        $api_url = $url.'/api/v2?apikey='.$apikey.'&cmd='.$endpoint;

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;

    }
   
}
