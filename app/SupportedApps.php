<?php

namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

abstract class SupportedApps
{
    protected $jar = false;

    protected $method = 'GET';

    protected $error;

    /**
     * @param $url
     * @param array $attrs
     * @return object
     * @throws GuzzleException
     */
    public function appTest($url, array $attrs = []): object
    {
        if (empty($this->config->url)) {
            return (object) [
                'code' => 404,
                'status' => 'No URL has been specified',
                'response' => 'No URL has been specified',
            ];
        }
        $res = $this->execute($url, $attrs);
        if ($res == null) {
            return (object) [
                'code' => null,
                'status' => $this->error,
                'response' => 'Connection failed',
            ];
        }
        switch ($res->getStatusCode()) {
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

        return (object) [
            'code' => $res->getStatusCode(),
            'status' => $status,
            'response' => $res->getBody(),
        ];
    }

    /**
     * @param $url
     * @param array $attrs
     * @param array|bool|null $overridevars
     * @param string|bool|null $overridemethod
     * @return ResponseInterface|null
     * @throws GuzzleException
     */
    public function execute(
        $url,
        array $attrs = [],
        $overridevars = null,
        $overridemethod = null
    ): ?ResponseInterface {
        $res = null;

        $vars = ($overridevars === null || $overridevars === false) ?
         [
            'http_errors' => false,
            'timeout' => 15,
            'connect_timeout' => 15,
        ] : $overridevars;

        $client = new Client($vars);

        $method = ($overridemethod === null || $overridemethod === false) ? $this->method : $overridemethod;


        try {
            return $client->request($method, $url, $attrs);
        } catch (ConnectException $e) {
            Log::error('Connection refused');
            Log::debug($e->getMessage());
            $this->error = 'Connection refused - '.(string) $e->getMessage();
        } catch (ServerException $e) {
            Log::debug($e->getMessage());
            $this->error = (string) $e->getResponse()->getBody();
        }
        $this->error = 'General error connecting with API';

        return $res;
    }

    /**
     * @return void
     */
    public function login()
    {
    }

    /**
     * @param string $url
     * @param bool $addslash
     * @return string
     */
    public function normaliseurl(string $url, bool $addslash = true): string
    {
        $url = rtrim($url, '/');
        if ($addslash) {
            $url .= '/';
        }

        return $url;
    }

    /**
     * @param $status
     * @param $data
     * @return false|string
     */
    public function getLiveStats($status, $data)
    {
        $className = $this::class;
        $explode = explode('\\', $className);
        $name = end($explode);

        $html = view('SupportedApps::'.$name.'.livestats', $data)->with('data', $data)->render();

        return json_encode(['status' => $status, 'html' => $html]);
        //return
    }

    /**
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public static function getList(): ResponseInterface
    {
        // $list_url = 'https://apps.heimdall.site/list';
        $list_url = config('app.appsource').'list.json';
        $client = new Client(['http_errors' => false, 'verify' => false, 'timeout' => 15, 'connect_timeout' => 15]);

        return $client->request('GET', $list_url);
    }

    public static function configValue($item = null, $key = null)
    {
        if (isset($item) && ! empty($item)) {
            return $item->getconfig()->$key;
        } else {
            return null;
        }
    }

    /**
     * @param $app
     * @return bool|false
     * @throws GuzzleException
     */
    public static function getFiles($app): bool
    {
        Log::debug("Download triggered for ".print_r($app, true));

        $zipurl = config('app.appsource').'files/'.$app->sha.'.zip';

        $client = new Client(['http_errors' => false, 'timeout' => 60, 'connect_timeout' => 15, 'verify' => false]);
        $res = $client->request('GET', $zipurl);

        // Something went wrong?
        if ($res->getStatusCode() !== 200) {
            return false;
        }

        if (! file_exists(app_path('SupportedApps'))) {
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
        } else {
            var_dump($x);
            return false;
        }
        return true;
    }

    /**
     * @param $details
     * @param $app
     * @return mixed
     */
    public static function saveApp($details, $app)
    {
        $app->appid = $details->appid;
        $app->name = $details->name;
        $app->sha = $details->sha ?? null;
        $app->icon = 'icons/'.$details->icon;
        $app->website = $details->website;
        $app->license = $details->license;

        $appclass = $app->class();
        $application = new $appclass;
        $enhanced = (bool) ($application instanceof \App\EnhancedApps);
        $app->class = $appclass;
        $app->enhanced = $enhanced;
        $app->tile_background = $details->tile_background;
        $app->save();

        return $app;
    }
}
