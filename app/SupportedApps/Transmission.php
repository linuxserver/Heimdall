<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Transmission implements Contracts\Applications, Contracts\Livestats
{

    private $_client;
    private $_clientOptions = array();

    public function __construct()
    {
        $body = array();
        $body["method"] = "torrent-get";
        $body["arguments"] = array("fields" => ["percentDone","status","rateDownload","rateUpload"]);
        $this->_client = new Client(
            ['http_errors' => false,
            'timeout' => 10,
            'body' => json_encode($body)]
        );
    }

    public function defaultColour()
    {
        return '#950003';
    }
    public function icon()
    {
        return 'supportedapps/transmission.png';
    }
    public function configDetails()
    {
        return 'transmission';
    }
    public function testConfig()
    {
        $res = $this->sendRequest();
        if ($res == null) {
            echo 'Transmission connection failed';
            return;
        }
        switch($res->getStatusCode()) {
        case 200:
            $data = json_decode($res->getBody());
            echo "Successfully connected with status: ".$data->result."\n";
            break;
        case 401:
            echo 'Failed: Invalid credentials';
            break;
        case 404:
            echo 'Failed: Please make sure your URL is correct and includes the port';
            break;
        case 409:
            echo 'Failed: Incorrect session id';
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
        $res = $this->sendRequest();
        if ($res == null) {
            Log::debug('Transmission connection failed');
            return '';
        }
        $data = json_decode($res->getBody());
        if (! isset($data->arguments)) {
            Log::debug('Failed to fetch data from Transmission');
            return '';
        }
        $torrents = $data->arguments->torrents;
        $torrentCount = count($torrents);
        $rateDownload = $rateUpload = $completedTorrents = 0;
        foreach ($torrents as $thisTorrent) {
            $rateDownload += $thisTorrent->rateDownload;
            $rateUpload += $thisTorrent->rateUpload;
            if ($thisTorrent->percentDone == 1) {
                $completedTorrents += 1;
            }
        }
        if ($torrentCount - $completedTorrents == 0) {
            // Don't poll as frequently if we don't have any active torrents
            $active = 'inactive';
        }

        $html = '
        <ul class="livestats">
            <li><span class="title">Done</span><sub>'.$completedTorrents.' / '.$torrentCount.'</sub></li>
            <li><span class="title">Down</span><sub>'.format_bytes($rateDownload).'</sub></li>
            <li><span class="title">Up</span><sub>'.format_bytes($rateUpload).'</sub></li>
        </ul>
        ';
        return json_encode(['status' => $active, 'html' => $html]);;
    }

    private function sendRequest()
    {
        $optionsSet = $this->setClientOptions();
        if (! $optionsSet) {
            // Pass the failed response back up the chain
            return null;
        }
        $res = $this->torrentGet();
        if ($res->getStatusCode() == 409) {
            $this->setClientOptions();
            $res = $this->torrentGet();
        }
        return $res;
    }

    private function torrentGet()
    {
        $res = null;
        try{
            $res = $this->_client->request(
                'POST',
                $this->getApiUrl(),
                $this->_clientOptions
            );
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            Log::error("Connection to {$e->getRequest()->getUrl()} failed");
            Log::debug($e->getMessage());
            $res = $e->getRequest();
        }catch(\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("Transmission connection refused");
            Log::debug($e->getMessage());
        }
        return $res;
    }

    private function setClientOptions()
    {
        if ($this->config->username != '' || $this->config->password != '') {
            $this->_clientOptions = ['auth'=> [$this->config->username, $this->config->password, 'Basic']];
        }
        try{
            $res = $this->_client->request('HEAD', $this->getApiUrl(), $this->_clientOptions);
            $xtId = $res->getHeaderLine('X-Transmission-Session-Id');
            if ($xtId != null) {
                $this->_clientOptions['headers'] = ['X-Transmission-Session-Id' => $xtId];
            } else {
                Log::error("Unable to get Transmission session information");
                Log::debug("Status Code: ".$res->getStatusCode());
            }
        }catch(\GuzzleHttp\Exception\ConnectException $e){
            Log::error("Failed connection to Transmission");
            return false;
        }
        return true;
    }

    private function getApiUrl()
    {
        $config = $this->config;
        $url = $config->url;

        $url = rtrim($url, '/');
        $api_url = $url.'/transmission/rpc';

        return $api_url;
    }
}
