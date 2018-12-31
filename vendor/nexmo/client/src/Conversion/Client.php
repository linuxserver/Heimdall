<?php

namespace Nexmo\Conversion;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\Exception;


class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    public function sms($message_id, $delivered, $timestamp=null)
    {
        return $this->sendConversion('sms', $message_id, $delivered, $timestamp);
    }

    public function voice($message_id, $delivered, $timestamp=null)
    {
        return $this->sendConversion('voice', $message_id, $delivered, $timestamp);
    }

    protected function sendConversion($type, $message_id, $delivered, $timestamp=null)
    {
        $params = [
            'message-id' => $message_id,
            'delivered' => $delivered
        ];

        if ($timestamp) {
            $params['timestamp'] = $timestamp;
        }

        $response = $this->client->postUrlEncoded(
            $this->getClient()->getApiUrl() . '/conversions/'.$type.'?'.http_build_query($params),
            []
        );

        if($response->getStatusCode() != '200'){
            throw $this->getException($response);
        }
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        if($status === 402) {
            $e = new Exception\Request("This endpoint may need activating on your account. Please email support@nexmo.com for more information", $status);
        } elseif($status >= 400 AND $status < 500) {
            $e = new Exception\Request($body['error_title'], $status);
        } elseif($status >= 500 AND $status < 600) {
            $e = new Exception\Server($body['error_title'], $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
        }

        return $e;
    }

}
