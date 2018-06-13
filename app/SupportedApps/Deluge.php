<?php namespace App\SupportedApps;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
class Deluge implements Contracts\Applications, Contracts\Livestats {
    public function defaultColour()
    {
        return '#357';
    }
    public function icon()
    {
        return 'supportedapps/deluge.png';
    }

    public function configDetails()
    {
        return 'deluge';
    }

    public function testConfig()
    {
        $res = $this->login()[0];
        switch($res->getStatusCode()) {
            case 200:
                $data = json_decode($res->getBody());
                if(!isset($data->result) || is_null($data->result) || $data->result == false) {
                    echo 'Failed: Invalid Credentials';
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
        $jar = $this->login()[1];
        $res = $this->getDetails($jar);
        $data = json_decode($res->getBody());
        $download_rate = $data->result->stats->download_rate;
        $upload_rate = $data->result->stats->upload_rate;
        $seed_count = $data->result->filters->state[2];
        $leech_count = $data->result->filters->state[1];
        $html = '
        <ul class="livestats">
            <li><span class="title"><i class="fas fa-arrow-down"></i> '.$this->formatBytes($download_rate).'</span></li>
            <li><span class="title"><i class="fas fa-arrow-up"></i> '.$this->formatBytes($upload_rate).'</span></li>
        </ul>
        <ul class="livestats">
            <li><span class="title">Leech: '.$leech_count[1].'</span></li>
            <li><span class="title">Seed: '.$seed_count[1].'</span></li>
        </ul>
        ';
        return json_encode(['status' => $active, 'html' => $html]);
    }
    public function getDetails($jar)
    {
        $config = $this->config;
        $url = $config->url;
        $url = rtrim($url, '/');
        $api_url = $url.'/json';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('POST', $api_url, [
            'body' => '{"method": "web.update_ui", "params": [["none"], {}], "id": 1}',
            'cookies' => $jar,
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);
        return $res;
    }
    public function login()
    {
        $config = $this->config;
        $url = $config->url;
        $password = $config->password;
        $url = rtrim($url, '/');
        $api_url = $url.'/json';
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        $res = $client->request('POST', $api_url, [
            'body' => '{"method": "auth.login", "params": ["'.$password.'"], "id": 1}',
            'cookies' => $jar,
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json']
        ]);
        return array($res,$jar);
    }

    function formatBytes($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 

        return round($bytes, $precision) . ' ' . $units[$pow] . 'ps'; 
    }
}
