<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Insights;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\Exception;
use Zend\Diactoros\Request;

/**
 * Class Client
 */
class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    public function basic($number)
    {
        $insightsResults = $this->makeRequest('/ni/basic/json', $number);

        $basic = new Basic($insightsResults['national_format_number']);
        $basic->jsonUnserialize($insightsResults);
        return $basic;
    }

    public function standardCNam($number)
    {
        $insightsResults = $this->makeRequest('/ni/standard/json', $number, ['cnam' => 'true']);
        $standard = new StandardCnam($insightsResults['national_format_number']);
        $standard->jsonUnserialize($insightsResults);
        return $standard;
    }

    public function advancedCnam($number)
    {
        $insightsResults = $this->makeRequest('/ni/advanced/json', $number, ['cnam' => 'true']);
        $standard = new AdvancedCnam($insightsResults['national_format_number']);
        $standard->jsonUnserialize($insightsResults);
        return $standard;
    }

    public function standard($number, $useCnam=false)
    {
        $insightsResults = $this->makeRequest('/ni/standard/json', $number);
        $standard = new Standard($insightsResults['national_format_number']);
        $standard->jsonUnserialize($insightsResults);
        return $standard;
    }

    public function advanced($number)
    {
        $insightsResults = $this->makeRequest('/ni/advanced/json', $number);
        $advanced = new Advanced($insightsResults['national_format_number']);
        $advanced->jsonUnserialize($insightsResults);
        return $advanced;
    }

    public function advancedAsync($number, $webhook)
    {
        // This method does not have a return value as it's async. If there is no exception thrown
        // We can assume that everything is fine
        $this->makeRequest('/ni/advanced/async/json', $number, ['callback' => $webhook]);
    }

    public function makeRequest($path, $number, $additionalParams = [])
    {
        if ($number instanceof Number)
        {
            $number = $number->getMsisdn();
        }

        $queryString = http_build_query([
            'number' => $number,
        ] + $additionalParams);

        $request = new Request(
            $this->getClient()->getApiUrl(). $path.'?'.$queryString,
            'GET',
            'php://temp',
            [
                'Accept' => 'application/json'
            ]
        );

        $response = $this->client->send($request);

        $insightsResults = json_decode($response->getBody()->getContents(), true);

        if('200' != $response->getStatusCode()){
            throw $this->getException($response);
        }

        return $insightsResults;
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        if($status >= 400 AND $status < 500) {
            $e = new Exception\Request($body['error-code-label'], $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($body['error-code-label'], $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
            throw $e;
        }

        return $e;
    }
}
