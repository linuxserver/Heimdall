<?php namespace App\SupportedApps;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Sonarr implements Contracts\Applications, Contracts\Livestats {
    public function defaultColour()
    {
        return '#163740';
    }
    public function icon()
    {
        return 'supportedapps/sonarr.png';
    }
    public function configDetails()
    {
        return 'sonarr';
    }
    public function testConfig()
    {
        $res = $this->getStatus()();
        $wanted = json_decode($res->getBody());
        if(isset($wanted->version))
        {
            echo 'Successfully connected to the API';
        }
        else if(isset($wanted->error))
        {
            echo 'Error: '. $wanted->error;
        }
        else
        {
             echo 'Something went wrong';
        }
    }
    public function executeConfig()
    {
        $html = '';
        $active = 'active';
        $wantedRes = $this->getWanted();
        $queueRes = $this->getQueue();
        $wanted = json_decode($wantedRes->getBody());
        $queue = json_decode($queueRes->getBody());
        $wantedCount = $wanted->totalRecords;
        $queueCount = sizeof($queue);
        $html = '
        <ul class="livestats">
            <li><span class="title">Wanted: '.$wantedCount[1].'</span></li>
            <li><span class="title">Activity: '.$queueCount[1].'</span></li>
        </ul>
        ';
        return json_encode(['status' => $active, 'html' => $html]);
    }
    public function getStatus()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url.'/api/system/status?apikey='.$config->apiKey;
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;
    }
    public function getWanted()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url.'/api/wanted/missing?apikey='.$config->apiKey.'&pageSize=1';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;
    }
    public function getQueue()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url.'/api/queue?apikey='.$config->apiKey.'&pageSize=1';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;
    }
}
