<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Nzbget implements Contracts\Applications, Contracts\Livestats {

    public $config;

    public function defaultColour()
    {
        return '#124019';
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
            default:
                throw new MyException("Invalid response from api...");
                break;
        }
    }
    public function executeConfig()
    {
        $res = $this->buildRequest('status');
        $data = json_decode($res->getBody());

        $queue_size = $data->result->RemainingSizeMB;
        $current_speed = $data->result->DownloadRate;

        $output = '
        <ul class="livestats">
            <li><span>Remaining</span><strong>'.$queue_size.'</strong></li>
            <li><span>Speed</span><strong>'.$current_speed.'</strong></li>
        </ul>
        ';
        return $output;
    }
    public function buildRequest($endpoint)
    {
        $config = $this->config;
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;

        $rebuild_url = str_replace('http://', 'http://'.$username.':'.$password.'@', $url);
        $rebuild_url = str_replace('https://', 'https://'.$username.':'.$password.'@', $rebuild_url);

        $api_url = $rebuild_url.'jsonrpc/'.$endpoint;

        $client = new Client(['http_errors' => false]);
        $res = $client->request('GET', $api_url);
        return $res;

    }
   
}