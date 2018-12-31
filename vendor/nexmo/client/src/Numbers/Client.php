<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Numbers;

use Http\Client\Common\Exception\ClientErrorException;
use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Psr\Http\Message\ResponseInterface;
use Nexmo\Client\Exception;
use Zend\Diactoros\Request;

class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    public function update($number, $id = null)
    {
        if(!is_null($id)){
            $update = $this->get($id);
        }

        if($number instanceof Number){
            $body = $number->getRequestData();
            if(!isset($update) AND !isset($body['country'])){
                $data = $this->get($number->getId());
                $body['msisdn'] = $data->getId();
                $body['country'] = $data->getCountry();
            }
        } else {
            $body = $number;
        }

        if(isset($update)){
            $body['msisdn'] = $update->getId();
            $body['country'] = $update->getCountry();
        }

        $request = new Request(
            $this->client->getRestUrl() . '/number/update',
            'POST',
            'php://temp',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        $request->getBody()->write(http_build_query($body));
        $response = $this->client->send($request);

        if('200' != $response->getStatusCode()){
            throw $this->getException($response);
        }

        if(isset($update) AND ($number instanceof Number)){
            return $this->get($number);
        }

        if($number instanceof Number){
            return $this->get($number);
        }

        return $this->get($body['msisdn']);
    }

    public function get($number = null)
    {
        $items =  $this->search($number);

        // This is legacy behaviour, so we need to keep it even though
        // it isn't technically the correct message
        if (count($items) != 1) {
            throw new Exception\Request('number not found', 404);
        }

        return $items[0];
    }

    /**
     * @param null|string $number
     * @return array []Number
     * @deprecated Use `searchOwned` instead
     */
    public function search($number = null)
    {
        return $this->searchOwned($number);
    }

    public function searchAvailable($country, $options = [])
    {
        $query = [
            'country' => $country
        ];

        // These are all optional parameters
        $possibleParameters = [
            'pattern',
            'search_pattern',
            'features',
            'size',
            'type',
            'index'
        ];

        foreach ($possibleParameters as $param) {
            if (isset($options[$param])) {
                $query[$param] = $options[$param];
            }
        }

        $request = new Request(
            $this->client->getRestUrl() . '/number/search?' . http_build_query($query),
            'GET',
            'php://temp'
        );

        $response = $this->client->send($request);

        return $this->handleNumberSearchResult($response, null);
    }

    public function searchOwned($number = null, $options = [])
    {
        $query = [];
        if ($number !== null) {
            if($number instanceof Number){
                $query = ['pattern' => $number->getId()];
            } else {
                $query = ['pattern' => $number];
            }

        }

        // These are all optional parameters
        $possibleParameters = [
            'search_pattern',
            'size',
            'index'
        ];

        foreach ($options as $param => $value) {
            if (!in_array($param, $possibleParameters)) {
                throw new Exception\Request("Unknown option: '".$param."'");
            }
            $query[$param] = $value;
        }

        $queryString = http_build_query($query);

        $request = new Request(
            $this->client->getRestUrl() . '/account/numbers?' . $queryString,
            'GET',
            'php://temp'
        );

        $response = $this->client->send($request);
        return $this->handleNumberSearchResult($response, $number);
    }

    private function handleNumberSearchResult($response, $number)
    {
        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }

        $searchResults = json_decode($response->getBody()->getContents(), true);
        if(empty($searchResults)){
            throw new Exception\Request('number not found', 404);
        }

        if(!isset($searchResults['count']) OR !isset($searchResults['numbers'])){
            throw new Exception\Exception('unexpected response format');
        }

        // We're going to return a list of numbers
        $numbers = [];

        // If they provided a number initially, we'll only get one response
        // so let's reuse the object
        if($number instanceof Number){
            $number->jsonUnserialize($searchResults['numbers'][0]);
            $numbers[] = $number;
        } else {
            // Otherwise, we return everything that matches
            foreach ($searchResults['numbers'] as $returnedNumber) {
                $number = new Number();
                $number->jsonUnserialize($returnedNumber);
                $numbers[] = $number;
            }
        }

        return $numbers;
    }

    public function purchase($number, $country = null) {
        // We cheat here and fetch a number using the API so that we have the country code which is required
        // to make a cancel request
        if (!$number instanceof Number) {
            if (!$country) {
                throw new Exception\Exception("You must supply a country in addition to a number to purchase a number");
            }
            $number = new Number($number, $country);
        }

        $body = [
            'msisdn' => $number->getMsisdn(),
            'country' => $number->getCountry()
        ];

        $request = new Request(
            $this->client->getRestUrl() . '/number/buy',
            'POST',
            'php://temp',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        $request->getBody()->write(http_build_query($body));
        $response = $this->client->send($request);

        // Sadly we can't distinguish *why* purchasing fails, just that it
        // has failed. Here are a few of the tests I attempted and their associated
        // error codes + body
        //
        // Mismatch number/country :: 420 :: method failed
        // Already own number :: 420 :: method failed
        // Someone else owns the number :: 420 :: method failed
        if('200' != $response->getStatusCode()){
            throw $this->getException($response);
        }
    }

    public function cancel($number) {
        // We cheat here and fetch a number using the API so that we have the country code which is required
        // to make a cancel request
        if (!$number instanceof Number) {
            $number = $this->get($number);
        }

        $body = [
            'msisdn' => $number->getMsisdn(),
            'country' => $number->getCountry()
        ];

        $request = new Request(
            $this->client->getRestUrl() . '/number/cancel',
            'POST',
            'php://temp',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        );

        $request->getBody()->write(http_build_query($body));
        $response = $this->client->send($request);

        // Sadly we can't distinguish *why* purchasing fails, just that it
        // has failed.
        if('200' != $response->getStatusCode()){
            throw $this->getException($response);
        }
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
