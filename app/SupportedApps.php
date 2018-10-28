<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

abstract class SupportedApps
{

    protected $jar = false;
    protected $method = 'GET';

    public function appTest($url, $attrs = [])
    {
        $res = $this->execute($url, $attrs);
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

    public function execute($url, $attrs = [])
    {
        $vars = [
            'http_errors' => false, 
            'timeout' => 15, 
            'connect_timeout' => 15,
        ];
        $client = new Client($vars);

        try {
            return $client->request($this->method, $url, $attrs);  
         } catch (\GuzzleHttp\Exception\ServerException $e) {
             echo (string) $e->getResponse()->getBody();
         }
    }

    public function login()
    {

    }

    public function apiRequest($url)
    {

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

    public static function getFiles($app)
    {
        $zipurl = $app->files;
        $client = new Client(['http_errors' => false, 'timeout' => 60, 'connect_timeout' => 15]);
        $res = $client->request('GET', $zipurl);

        if(!file_exists(app_path('SupportedApps')))  {
            mkdir(app_path('SupportedApps'), 0777, true);
        }

        $src = app_path('SupportedApps/'.$app->name.'.zip');
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
        $img_src = app_path('SupportedApps/'.$details->name.'/'.$details->icon);
        $img_dest = public_path('storage/supportedapps/'.$details->icon);
        //die("i: ".$img_src);
        copy($img_src, $img_dest);
        
        $app->appid = $details->appid;
        $app->name = $details->name;
        $app->sha = $details->sha ?? null;
        $app->icon = 'supportedapps/'.$details->icon;
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