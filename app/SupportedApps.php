<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

abstract class SupportedApps
{

    protected $jar = false;
    protected $method = 'GET';
    protected $error;

    public function appTest($url, $attrs = [], $overridevars=false)
    {
        if(empty($this->config->url)) {
            return (object)[
                'code' => 404,
                'status' => 'No URL has been specified',
                'response' => 'No URL has been specified',
            ];    
        }
        $res = $this->execute($url, $attrs);
        if($res == null) {
            return (object)[
                'code' => null,
                'status' => $this->error,
                'response' => 'Connection failed',
            ];
        }
        switch($res->getStatusCode()) {
            case 200:
                $status = 'Successfully communicated with the API';
                break;
            case 401:
                $status = 'Failed: Invalid credentials';
                break;
            case 404:
                $status = 'Failed: Please make sure your URL is correct and that there is a trailing slash';
                break;
            default:
                $status = 'Something went wrong... Code: '.$res->getStatusCode();
                break;
        }
        return (object)[
            'code' => $res->getStatusCode(),
            'status' => $status,
            'response' => $res->getBody(),
        ];
    }

    public function execute($url, $attrs = [], $overridevars=false, $overridemethod=false)
    {
        $res = null;

        $vars = ($overridevars !== false) ?
        $overridevars : [
            'http_errors' => false, 
            'timeout' => 15, 
            'connect_timeout' => 15,
        ];

        $client = new Client($vars);

        $method = ($overridemethod !== false) ? $overridemethod : $this->method;

        try {
            return $client->request($method, $url, $attrs);  
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error("Connection refused");
            Log::debug($e->getMessage());
            $this->error = "Connection refused - ".(string) $e->getMessage();
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            Log::debug($e->getMessage());
            $this->error = (string) $e->getResponse()->getBody();
        }
        $this->error = 'General error connecting with API';
        return $res;
    }

    public function login()
    {

    }

    public function normaliseurl($url, $addslash=true)
    {

        $url = rtrim($url, '/');
        if($addslash) $url .= '/';

        return $url;

    }


    public function getLiveStats($status, $data)
    {
        $className = get_class($this);
        $explode = explode('\\', $className);
        $name = end($explode);

        $html = view('SupportedApps::'.$name.'.livestats', $data)->with('data', $data)->render();
        return json_encode(['status' => $status, 'html' => $html]);
        //return 
    }

    public static function getList()
    {
        $list_url = 'https://apps.heimdall.site/list';
        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        return $client->request('GET', $list_url);
    }

    public static function configValue($item=null, $key=null)
    {
        if(isset($item) && !empty($item)) {
            return $item->getconfig()->$key;
        } else return null;
    }

    public static function getFiles($app)
    {
        $zipurl = $app->files;
        $client = new Client(['http_errors' => false, 'timeout' => 60, 'connect_timeout' => 15]);
        $res = $client->request('GET', $zipurl);

        if(!file_exists(app_path('SupportedApps')))  {
            mkdir(app_path('SupportedApps'), 0777, true);
        }

        $src = app_path('SupportedApps/'.className($app->name).'.zip');
        file_put_contents($src, $res->getBody());

        $zip = new \ZipArchive();
        $x = $zip->open($src);  // open the zip file to extract
        if ($x === true) {
            $zip->extractTo(app_path('SupportedApps')); // place in the directory with same name
            $zip->close();
            unlink($src); //Deleting the Zipped file
        }
    }

    public static function saveApp($details, $app)
    {
        if(!file_exists(storage_path('app/public/icons')))  {
            mkdir(storage_path('app/public/icons'), 0777, true);
        }

        $img_src = app_path('SupportedApps/'.className($details->name).'/'.$details->icon);
        $img_dest = storage_path('app/public/icons/'.$details->icon);
        //die("i: ".$img_src);
        @copy($img_src, $img_dest);
        
        $app->appid = $details->appid;
        $app->name = $details->name;
        $app->sha = $details->sha ?? null;
        $app->icon = 'icons/'.$details->icon;
        $app->website = $details->website;
        $app->license = $details->license;
        $app->description = $details->description;

        $appclass = $app->class();
        $application = new $appclass;
        $enhanced = (bool)($application instanceof \App\EnhancedApps);

        $app->enhanced = $enhanced;
        $app->tile_background = $details->tile_background;
        $app->save();
    }

}