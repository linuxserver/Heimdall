<?php

namespace Nexmo\Redact;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Network;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;


class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    public function transaction($id, $product, $options = [])
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . '/v1/redact/transaction',
            'POST',
            'php://temp',
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        );

        $body = ['id' => $id, 'product' => $product] + $options;

        $request->getBody()->write(json_encode($body));
        $response = $this->client->send($request);

        $rawBody = $response->getBody()->getContents();

        if('204' != $response->getStatusCode()){
            throw $this->getException($response);
        }

        return null;
    }

    protected function getException(ResponseInterface $response)
    {
        $response->getBody()->rewind();
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        $msg = 'Unexpected error';

        // This is an error at the load balancer, likely auth related
        if (isset($body['error_title'])) {
            $msg = $body['error_title'];
        }

        if (isset($body['title'])) {
            $msg = $body['title'];
            if (isset($body['detail'])) {
                $msg .= ' - '.$body['detail'];
            }

            $msg .= '. See '.$body['type'];
        }

        if($status >= 400 AND $status < 500) {
            $e = new Exception\Request($msg, $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($msg, $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
            throw $e;
        }

        return $e;
    }


}
