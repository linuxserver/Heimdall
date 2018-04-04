<?php namespace App\SupportedApps;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class CouchPotato implements Contracts\Applications, Contracts\Livestats
{

    private $_client;

    public function __construct()
    {
        $this->_client = new Client(
            ['http_errors' => false,
            'timeout' => 10]
        );
    }

    public function defaultColour()
    {
        return '#363840';
    }
    public function icon()
    {
        return 'supportedapps/couchpotato.png';
    }
    public function configDetails()
    {
        return 'couchpotato';
    }
    public function testConfig()
    {
        $res = $this->sendRequest();
        if ($res == null) {
            echo 'CouchPotato connection failed';
            return;
        }
        switch($res->getStatusCode()) {
        case 200:
            echo "Successfully connected to CouchPotato";
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
        $res = $this->sendRequest();
        if ($res == null) {
            Log::debug('CouchPotato connection failed');
            return '';
        }
        $data = json_decode($res->getBody());
        if (! isset($data->movies)) {
            Log::debug('Failed to fetch data from CouchPotato');
            return '';
        }
        $movies = $data->movies;
        $wantedMovies = $availableMovies = 0;
        foreach ($movies as $v) {
            switch ($v->status) {
            case 'active':
                $wantedMovies++;
                break;
            case 'done':
                $availableMovies++;
                break;
            default:
                Log::warning('Unexpected CouchPotato status received: '.$v['status']);
                break;
            }
        }

        $html = '
        <ul class="livestats">
            <li><span class="title">Wanted</span><sub>'.$wantedMovies.'</sub></li>
            <li><span class="title">Available</span><sub>'.$availableMovies.'</sub></li>
        </ul>
        ';
        return json_encode(['status' => 'inactive', 'html' => $html]);
    }

    private function sendRequest()
    {
        $res = null;
        try{
            $res = $this->_client->request(
                'GET',
                $this->getApiUrl()
            );
        }catch(\GuzzleHttp\Exception\BadResponseException $e){
            Log::error("Connection to {$e->getRequest()->getUrl()} failed");
            Log::debug($e->getMessage());
            $res = $e->getRequest();
        }catch(\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("CouchPotato connection refused");
            Log::debug($e->getMessage());
        }
        return $res;
    }

    private function getApiUrl()
    {
        $url = $this->config->url;
        $url = rtrim($url, '/');
        $apiUrl = $url.'/api/'.$this->config->apikey.'/movie.list';
        return $apiUrl;
    }
}
