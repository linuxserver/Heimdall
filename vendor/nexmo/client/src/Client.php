<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo;
use Http\Client\HttpClient;
use Nexmo\Client\Credentials\Basic;
use Nexmo\Client\Credentials\Container;
use Nexmo\Client\Credentials\CredentialsInterface;
use Nexmo\Client\Credentials\Keypair;
use Nexmo\Client\Credentials\OAuth;
use Nexmo\Client\Credentials\SignatureSecret;
use Nexmo\Client\Exception\Exception;
use Nexmo\Client\Factory\FactoryInterface;
use Nexmo\Client\Factory\MapFactory;
use Nexmo\Client\Response\Response;
use Nexmo\Client\Signature;
use Nexmo\Entity\EntityInterface;
use Nexmo\Verify\Verification;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Uri;
use Zend\Diactoros\Request;

/**
 * Nexmo API Client, allows access to the API from PHP.
 *
 * @property \Nexmo\Message\Client $message
 * @property \Nexmo\Call\Collection|\Nexmo\Call\Call[] $calls
 *
 * @method \Nexmo\Message\Client message()
 * @method \Nexmo\Verify\Client  verify()
 * @method \Nexmo\Application\Client applications()
 * @method \Nexmo\Call\Collection calls()
 * @method \Nexmo\Numbers\Client numbers()
 */
class Client
{
    const VERSION = '1.2.0';

    const BASE_API  = 'https://api.nexmo.com';
    const BASE_REST = 'https://rest.nexmo.com';

    /**
     * API Credentials
     * @var CredentialsInterface
     */
    protected $credentials;

    /**
     * Http Client
     * @var HttpClient
     */
    protected $client;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create a new API client using the provided credentials.
     */
    public function __construct(CredentialsInterface $credentials, $options = array(), HttpClient $client = null)
    {
        if(is_null($client)){
            $client = new \Http\Adapter\Guzzle6\Client();
        }

        $this->setHttpClient($client);

        //make sure we know how to use the credentials
        if(!($credentials instanceof Container) && !($credentials instanceof Basic) && !($credentials instanceof SignatureSecret) && !($credentials instanceof OAuth) && !($credentials instanceof Keypair)){
            throw new \RuntimeException('unknown credentials type: ' . get_class($credentials));
        }

        $this->credentials = $credentials;

        $this->options = $options;

        // If they've provided an app name, validate it
        if (isset($options['app'])) {
            $this->validateAppOptions($options['app']);
        }

        // Set the default URLs. Keep the constants for
        // backwards compatibility
        $this->apiUrl = static::BASE_API;
        $this->restUrl = static::BASE_REST;

        // If they've provided alternative URLs, use that instead
        // of the defaults
        if (isset($options['base_rest_url'])) {
            $this->restUrl = $options['base_rest_url'];
        }

        if (isset($options['base_api_url'])) {
            $this->apiUrl = $options['base_api_url'];
        }

        $this->setFactory(new MapFactory([
            'account' => 'Nexmo\Account\Client',
            'insights' => 'Nexmo\Insights\Client',
            'message' => 'Nexmo\Message\Client',
            'verify'  => 'Nexmo\Verify\Client',
            'applications' => 'Nexmo\Application\Client',
            'numbers' => 'Nexmo\Numbers\Client',
            'calls' => 'Nexmo\Call\Collection',
            'conversion' => 'Nexmo\Conversion\Client',
            'conversation' => 'Nexmo\Conversations\Collection',
            'user' => 'Nexmo\User\Collection',
            'redact' => 'Nexmo\Redact\Client',
        ], $this));
    }

    public function getRestUrl() {
        return $this->restUrl;
    }

    public function getApiUrl() {
        return $this->apiUrl;
    }

    /**
     * Set the Http Client to used to make API requests.
     *
     * This allows the default http client to be swapped out for a HTTPlug compatible
     * replacement.
     *
     * @param HttpClient $client
     * @return $this
     */
    public function setHttpClient(HttpClient $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the Http Client used to make API requests.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->client;
    }

    /**
     * Set the factory used to create API specific clients.
     *
     * @param FactoryInterface $factory
     * @return $this
     */
    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @param RequestInterface $request
     * @param Signature $signature
     * @return RequestInterface
     */
    public static function signRequest(RequestInterface $request, SignatureSecret $credentials)
    {
        switch($request->getHeaderLine('content-type')){
            case 'application/json':
                $body = $request->getBody();
                $body->rewind();
                $content = $body->getContents();
                $params = json_decode($content, true);
                $params['api_key'] = $credentials['api_key'];
                $signature = new Signature($params, $credentials['signature_secret'], $credentials['signature_method']);
                $body->rewind();
                $body->write(json_encode($signature->getSignedParams()));
                break;
            case 'application/x-www-form-urlencoded':
                $body = $request->getBody();
                $body->rewind();
                $content = $body->getContents();
                $params = [];
                parse_str($content, $params);
                $params['api_key'] = $credentials['api_key'];
                $signature = new Signature($params, $credentials['signature_secret'], $credentials['signature_method']);
                $params = $signature->getSignedParams();
                $body->rewind();
                $body->write(http_build_query($params, null, '&'));
                break;
            default:
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $query['api_key'] = $credentials['api_key'];
                $signature = new Signature($query, $credentials['signature_secret'], $credentials['signature_method']);
                $request = $request->withUri($request->getUri()->withQuery(http_build_query($signature->getSignedParams())));
                break;
        }

        return $request;
    }

    public static function authRequest(RequestInterface $request, Basic $credentials)
    {
        switch($request->getHeaderLine('content-type')) {
            case 'application/json':
            if (static::requiresBasicAuth($request)) {
                $c = $credentials->asArray();
                $request = $request->withHeader('Authorization', 'Basic ' . base64_encode($c['api_key'] . ':' . $c['api_secret']));
            } else if (static::requiresAuthInUrlNotBody($request)) {
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $query = array_merge($query, $credentials->asArray());
                $request = $request->withUri($request->getUri()->withQuery(http_build_query($query)));
            } else {
                $body = $request->getBody();
                $body->rewind();
                $content = $body->getContents();
                $params = json_decode($content, true);
                $params = array_merge($params, $credentials->asArray());
                $body->rewind();
                $body->write(json_encode($params));
            }
                break;
            case 'application/x-www-form-urlencoded':
                $body = $request->getBody();
                $body->rewind();
                $content = $body->getContents();
                $params = [];
                parse_str($content, $params);
                $params = array_merge($params, $credentials->asArray());
                $body->rewind();
                $body->write(http_build_query($params, null, '&'));
                break;
            default:
                $query = [];
                parse_str($request->getUri()->getQuery(), $query);
                $query = array_merge($query, $credentials->asArray());
                $request = $request->withUri($request->getUri()->withQuery(http_build_query($query)));
                break;
        }

        return $request;
    }

    /**
     * @param array $claims
     * @return \Lcobucci\JWT\Token
     */
    public function generateJwt($claims = [])
    {
        if (method_exists($this->credentials, "generateJwt")) {
            return $this->credentials->generateJwt($claims);
        }
        throw new Exception(get_class($this->credentials).' does not support JWT generation');
    }
    
    /**
     * Takes a URL and a key=>value array to generate a GET PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to use as the query string
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, array $params = [])
    {
       $queryString = '?' . http_build_query($params);

       $url = $url . $queryString;

       $request = new Request(
            $url,
            'GET'
        );

        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a POST PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function post($url, array $params)
    {
        $request = new Request(
            $url,
            'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a POST PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function postUrlEncoded($url, array $params)
    {
        $request = new Request(
            $url,
            'POST',
            'php://temp',
            ['content-type' => 'application/x-www-form-urlencoded']
        );

        $request->getBody()->write(http_build_query($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a PUT PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @param array $params Key=>Value array of data to send
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function put($url, array $params)
    {
        $request = new Request(
            $url,
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        return $this->send($request);
    }

    /**
     * Takes a URL and a key=>value array to generate a DELETE PSR-7 request object
     *
     * @param string $url The URL to make a request to
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete($url)
    {
        $request = new Request(
            $url,
            'DELETE'
        );

        return $this->send($request);
    }

     /**
     * Wraps the HTTP Client, creates a new PSR-7 request adding authentication, signatures, etc.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function send(\Psr\Http\Message\RequestInterface $request)
    {
        if($this->credentials instanceof Container) {
            if ($this->needsKeypairAuthentication($request)) {
                $request = $request->withHeader('Authorization', 'Bearer ' . $this->credentials->get(Keypair::class)->generateJwt());
            } else {
                $request = self::authRequest($request, $this->credentials->get(Basic::class));
            }
        } elseif($this->credentials instanceof Keypair){
            $request = $request->withHeader('Authorization', 'Bearer ' . $this->credentials->generateJwt());
        } elseif($this->credentials instanceof SignatureSecret){
            $request = self::signRequest($request, $this->credentials);
        } elseif($this->credentials instanceof Basic){
            $request = self::authRequest($request, $this->credentials);
        }

        //todo: add oauth support

        //allow any part of the URI to be replaced with a simple search
        if(isset($this->options['url'])){
            foreach($this->options['url'] as $search => $replace){
                $uri = (string) $request->getUri();

                $new = str_replace($search, $replace, $uri);
                if($uri !== $new){
                    $request = $request->withUri(new Uri($new));
                }
            }
        }

        // The user agent must be in the following format:
        // LIBRARY-NAME/LIBRARY-VERSION LANGUAGE-NAME/LANGUAGE-VERSION [APP-NAME/APP-VERSION]
        // See https://github.com/Nexmo/client-library-specification/blob/master/SPECIFICATION.md#reporting
        $userAgent = [];

        // Library name
        $userAgent[] = 'nexmo-php/'.self::VERSION;

        // Language name
        $userAgent[] = 'php/'.PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;

        // If we have an app set, add that to the UA
        if (isset($this->options['app'])) {
            $app = $this->options['app'];
            $userAgent[] = $app['name'].'/'.$app['version'];
        }

        // Set the header. Build by joining all the parts we have with a space
        $request = $request->withHeader('User-Agent', implode(" ", $userAgent));

        $response = $this->client->sendRequest($request);
        return $response;
    }

    protected function validateAppOptions($app) {
        $disallowedCharacters = ['/', ' ', "\t", "\n"];
        foreach (['name', 'version'] as $key) {
            if (!isset($app[$key])) {
                throw new \InvalidArgumentException('app.'.$key.' has not been set');
            }

            foreach ($disallowedCharacters as $char) {
                if (strpos($app[$key], $char) !== false) {
                    throw new \InvalidArgumentException('app.'.$key.' cannot contain the '.$char.' character');
                }
            }
        }
    }

    public function serialize(EntityInterface $entity)
    {
        if($entity instanceof Verification){
            return $this->verify()->serialize($entity);
        }

        throw new \RuntimeException('unknown class `' . get_class($entity) . '``');
    }

    public function unserialize($entity)
    {
        if(is_string($entity)){
            $entity = unserialize($entity);
        }

        if($entity instanceof Verification){
            return $this->verify()->unserialize($entity);
        }

        throw new \RuntimeException('unknown class `' . get_class($entity) . '``');
    }

    public function __call($name, $args)
    {
        if(!$this->factory->hasApi($name)){
            throw new \RuntimeException('no api namespace found: ' . $name);
        }

        $collection = $this->factory->getApi($name);

        if(empty($args)){
            return $collection;
        }

        return call_user_func_array($collection, $args);
    }

    public function __get($name)
    {
        if(!$this->factory->hasApi($name)){
            throw new \RuntimeException('no api namespace found: ' . $name);
        }

        return $this->factory->getApi($name);
    }

    protected static function requiresBasicAuth(\Psr\Http\Message\RequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $isSecretManagementEndpoint = strpos($path, '/accounts') === 0 && strpos($path, '/secrets') !== false;

        return $isSecretManagementEndpoint;
    }

    protected static function requiresAuthInUrlNotBody(\Psr\Http\Message\RequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $isRedactEndpoint = strpos($path, '/v1/redact') === 0;

        return $isRedactEndpoint;
    }

    protected function needsKeypairAuthentication(\Psr\Http\Message\RequestInterface $request)
    {
        $path = $request->getUri()->getPath();
        $isCallEndpoint = strpos($path, '/v1/calls') === 0;
        $isRecordingUrl = strpos($path, '/v1/files') === 0;
        $isStitchEndpoint = strpos($path, '/beta/conversation') === 0;
        $isUserEndpoint = strpos($path, '/beta/users') === 0;

        return $isCallEndpoint || $isRecordingUrl || $isStitchEndpoint || $isUserEndpoint;
    }
}
