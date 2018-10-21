<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

abstract class SupportedApps
{

    public function test($url, $requiresLoginFirst=false)
    {

    }

    public function execute($url, $requiresLoginFirst=false)
    {
        if($requiresLoginFirst) {

        }

        $client = new Client(['http_errors' => false, 'timeout' => 15, 'connect_timeout' => 15]);
        return $client->request('GET', $url);
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
        copy($img_src, $img_dest);
        
        $app->name = $details->name;
        $app->sha = $details->sha;
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