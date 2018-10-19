<?php namespace App\SupportedApps;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Sonarr implements Contracts\Applications, Contracts\Livestats
{

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
        $res = $this->getStatus();
        $status = json_decode($res->getBody());
        if (isset($status->version))
        {
            echo 'Successfully connected to Sonarr API version ' . $status->version;
        }
        else if (isset($status->error))
        {
            if ($status->error == "Unauthorized")
            {
                echo 'Incorrect API Key. You can find it in Settings > General';
            }
            else
            {
                echo 'Error: ' . $status->error;
            }
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
        $html = '
        <ul class="livestats">
            <li><span class="title">Wanted: ' . $this->getWanted() . '</span></li>
            <li><span class="title">Activity: ' . $this->getQueue() . '</span></li>
        </ul>
        ';
        return json_encode(['status' => $active, 'html' => $html]);
    }

    public function getStatus()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url . '/api/system/status?apikey=' . $config->apiKey;
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('GET', $api_url);
        return $res;
    }

    public function getWanted()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url . '/api/wanted/missing?apikey=' . $config->apiKey . '&pageSize=1';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = json_decode($client->request('GET', $api_url));
        $wanted = $res->totalRecords;
        if (empty($wanted))
        {
            $wanted = 0;
        }
        return $wanted;
    }

    public function getQueue()
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url . '/api/queue?apikey=' . $config->apiKey . '&pageSize=1';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $queue = sizeof($client->request('GET', $api_url));
        return $queue;
    }
}
