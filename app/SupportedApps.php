<?php namespace App;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

abstract class SupportedApps
{
    public $config;

    public function test($url, $requiresLoginFirst=false)
    {

    }

    public function execute($url, $requiresLoginFirst=false)
    {
        
    }

    public function login()
    {

    }

    public function apiRequest($url)
    {

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
        $app->icon = 'storage/supportedapps/'.$details->icon;
        $app->website = $details->website;
        $app->license = $details->license;
        $app->description = $details->description;
        $app->enhanced = $details->enhanced;
        $app->tile_background = $details->tile_background;
        $app->save();
    }

}