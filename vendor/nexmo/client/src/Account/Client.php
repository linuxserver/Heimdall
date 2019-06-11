<?php

namespace Nexmo\Account;

use Nexmo\ApiErrorHandler;
use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Network;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;


class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    public function getPrefixPricing($prefix)
    {
        $queryString = http_build_query([
            'prefix' => $prefix
        ]);

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/get-prefix-pricing/outbound?'.$queryString,
            'GET',
            'php://temp'
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();

        $body = json_decode($rawBody, true);

        $codeCategory = (int) ($response->getStatusCode()/100);
        if ($codeCategory != 2) {
            if ($codeCategory == 4) {
                throw new Exception\Request($body['error-code-label']);
            }else if ($codeCategory == 5) {
                throw new Exception\Server($body['error-code-label']);
            }
        }

        if ($body['count'] == 0) {
            return [];
        }

        // Multiple countries can match each prefix
        $prices = [];

        foreach ($body['prices'] as $p) {
            $prefixPrice = new PrefixPrice();
            $prefixPrice->jsonUnserialize($p);
            $prices[] = $prefixPrice;
        }
        return $prices;
    }

    public function getSmsPrice($country)
    {
        $body = $this->makePricingRequest($country, 'sms');
        $smsPrice = new SmsPrice();
        $smsPrice->jsonUnserialize($body);
        return $smsPrice;
    }

    public function getVoicePrice($country)
    {
        $body = $this->makePricingRequest($country, 'voice');
        $voicePrice = new VoicePrice();
        $voicePrice->jsonUnserialize($body);
        return $voicePrice;
    }

    protected function makePricingRequest($country, $pricingType)
    {
        $queryString = http_build_query([
            'country' => $country
        ]);

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/get-pricing/outbound/'.$pricingType.'?'.$queryString,
            'GET',
            'php://temp'
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();

        if ($rawBody === '') {
            throw new Exception\Server('No results found');
        }

        return json_decode($rawBody, true);
    }

    public function getBalance()
    {

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/get-balance',
            'GET',
            'php://temp'
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();

        if ($rawBody === '') {
            throw new Exception\Server('No results found');
        }

        $body = json_decode($rawBody, true);

        $balance = new Balance($body['value'], $body['autoReload']);
        return $balance;
    }

    public function topUp($trx)
    {
        $body = [
            'trx' => $trx
        ];

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/top-up'
            ,'POST'
            , 'php://temp'
            , ['content-type' => 'application/x-www-form-urlencoded']
        );

        $request->getBody()->write(http_build_query($body));
        $response = $this->client->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }
    }

    public function getConfig()
    {

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/settings',
            'POST',
            'php://temp'
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();

        if ($rawBody === '') {
            throw new Exception\Server('Response was empty');
        }

        $body = json_decode($rawBody, true);

        $config = new Config(
            $body['mo-callback-url'],
            $body['dr-callback-url'],
            $body['max-outbound-request'],
            $body['max-inbound-request'],
            $body['max-calls-per-second']
        );
        return $config;
    }

    public function updateConfig($options)
    {
        // supported options are SMS Callback and DR Callback
        $params = [];
        if(isset($options['sms_callback_url'])) {
            $params['moCallBackUrl'] = $options['sms_callback_url'];
        }

        if(isset($options['dr_callback_url'])) {
            $params['drCallBackUrl'] = $options['dr_callback_url'];
        }

        $request = new Request(
            $this->getClient()->getRestUrl() . '/account/settings',
            'POST',
            'php://temp',
            ['content-type' => 'application/x-www-form-urlencoded']
        );

        $request->getBody()->write(http_build_query($params));
        $response = $this->client->send($request);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $rawBody = $response->getBody()->getContents();

        if ($rawBody === '') {
            throw new Exception\Server('Response was empty');
        }

        $body = json_decode($rawBody, true);

        $config = new Config(
            $body['mo-callback-url'],
            $body['dr-callback-url'],
            $body['max-outbound-request'],
            $body['max-inbound-request'],
            $body['max-calls-per-second']
        );
        return $config;
    }

    public function listSecrets($apiKey)
    {
        $body = $this->get( $this->getClient()->getApiUrl() . '/accounts/'.$apiKey.'/secrets');
        return SecretCollection::fromApi($body);
    }

    public function getSecret($apiKey, $secretId)
    {
        $body = $this->get( $this->getClient()->getApiUrl() . '/accounts/'.$apiKey.'/secrets/'. $secretId);
        return Secret::fromApi($body);
    }

    public function createSecret($apiKey, $newSecret)
    {
        $body = [
            'secret' => $newSecret
        ];

        $request = new Request(
            $this->getClient()->getApiUrl() . '/accounts/'.$apiKey.'/secrets'
            ,'POST'
            , 'php://temp'
            , ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->client->send($request);

        $rawBody = $response->getBody()->getContents();
        $responseBody = json_decode($rawBody, true);
        ApiErrorHandler::check($responseBody, $response->getStatusCode());

        return Secret::fromApi($responseBody);
    }

    public function deleteSecret($apiKey, $secretId)
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . '/accounts/'.$apiKey.'/secrets/'. $secretId
            ,'DELETE'
            , 'php://temp'
            , ['content-type' => 'application/json']
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();
        $body = json_decode($rawBody, true);

        // This will throw an exception on any error
        ApiErrorHandler::check($body, $response->getStatusCode());

        // This returns a 204, so no response body
    }

    protected function get($url) {
       $request = new Request(
           $url
           ,'GET'
           , 'php://temp'
           , ['content-type' => 'application/json']
        );

        $response = $this->client->send($request);
        $rawBody = $response->getBody()->getContents();
        $body = json_decode($rawBody, true);

        // This will throw an exception on any error
        ApiErrorHandler::check($body, $response->getStatusCode());

        return $body;
    }

    protected function getException(ResponseInterface $response, $application = null)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        if($status >= 400 AND $status < 500) {
            $e = new Exception\Request($body['error_title'], $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($body['error_title'], $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
        }

        return $e;
    }

}
