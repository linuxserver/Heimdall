<?php namespace App\SupportedApps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Traefik implements Contracts\Applications, Contracts\Livestats
{

    public function defaultColour()
    {
        return '#28434a';
    }

    public function icon()
    {
        return 'supportedapps/traefik.png';
    }

    public function configDetails()
    {
        return 'traefik';
    }

    public function testConfig()
    {
        $res = $this->sendRequest();
        if ($res == null) {
            echo 'Traefik connection failed';
            return;
        }
        switch($res->getStatusCode()) {
        case 200:
            $data = json_decode($res->getBody());
            echo "Successfully connected with status: ".$data->result."\n";
            break;
        case 404:
            echo 'Failed: Please make sure your URL is correct and includes the port';
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
        $res = $this->sendRequest();
        $data = json_decode($res->getBody());
        if ($data) {
            $avg_response_time = $data->average_response_time_sec;
            $time = $avg_response_time*1000;
            $time_output = number_format($time, 2);
            $active = ($time > 0) ? 'active' : 'inactive';
            $html = '
                <ul class="livestats">
                    <li><span class="title">Avg. Response Time</span><sub><i class="fas fa-heartbeat"></i> '.$time_output.' ms</sub></li>
                </ul>
            ';
        }
        return json_encode(['status' => $active, 'html' => $html]);
    }

    public function sendRequest()
    {
        $config = $this->config;
        $url = $config->url;

        $url = rtrim($url, '/');
        $api_url = $url.'/health';

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);

        return $res;
    }
}
