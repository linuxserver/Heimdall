<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\Exception;
use Zend\Diactoros\Request;

/**
 * Class Client
 * @method Text sendText(string $to, string $from, string $text, array $additional = []) Send a Test Message
 */
class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    /**
     * @param Message|array $message
     * @return Message
     * @throws Exception\Exception
     * @throws Exception\Request
     * @throws Exception\Server
     */
    public function send($message)
    {
        if(!($message instanceof MessageInterface)){
            $message = $this->createMessageFromArray($message);
        }

        $params = $message->getRequestData(false);
        
        $request = new Request(
            $this->getClient()->getRestUrl() . '/sms/json'
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        $message->setRequest($request);
        $response = $this->client->send($request);
        $message->setResponse($response);

        //check for valid data, as well as an error response from the API
        $data = $message->getResponseData();
        if(!isset($data['messages'])){
            throw new Exception\Exception('unexpected response from API');
        }

        //normalize errors (client vrs server)
        foreach($data['messages'] as $part){
            switch($part['status']){
                case '0':
                    break; //all okay
                case '1':
                    if(preg_match('#\[\s+(\d+)\s+\]#', $part['error-text'], $match)){
                        usleep($match[1] + 1);
                    } else {
                        sleep(1);
                    }

                    return $this->send($message);
                case '5':
                    $e = new Exception\Server($part['error-text'], $part['status']);
                    $e->setEntity($message);
                    throw $e;
                default:
                    $e = new Exception\Request($part['error-text'], $part['status']);
                    $e->setEntity($message);
                    throw $e;
            }
        }

        return $message;
    }

    public function sendShortcode($message) {
        if(!($message instanceof Shortcode)){
            $message = Shortcode::createMessageFromArray($message);
        }

        $params = $message->getRequestData();

        $request = new Request(
            $this->getClient()->getRestUrl() . '/sc/us/'.$message->getType().'/json'
            ,'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($params));
        $response = $this->client->send($request);

        $body = json_decode($response->getBody(), true);

        foreach ($body['messages'] as $m) {
            if ($m['status'] != '0') {
                $e = new Exception\Request($m['error-text'], $m['status']);
                $e->setEntity($message);
                throw $e;
            }
        }

        return $body;

    }

    /**
     * @param $query
     * @return MessageInterface[]
     * @throws Exception\Exception
     * @throws Exception\Request
     */
    public function get($query)
    {
        if($query instanceof Query){
            $params = $query->getParams();
        } else if($query instanceof MessageInterface){
            $params = ['ids' => [$query->getMessageId()]];
        } else if(is_string($query)) {
            $params = ['ids' => [$query]];
        } else if(is_array($query)){
            $params = ['ids' => $query];
        } else {
            throw new \InvalidArgumentException('query must be an instance of Query, MessageInterface, string ID, or array of IDs.');
        }

        $request = new Request(
            $this->getClient()->getRestUrl() . '/search/messages?' . http_build_query($params),
            'GET',
            'php://temp',
            ['Accept' => 'application/json']
        );

        $response = $this->client->send($request);
        $response->getBody()->rewind();
        $data = json_decode($response->getBody()->getContents(), true);

        if($response->getStatusCode() != '200' && isset($data['error-code'])){
            throw new Exception\Request($data['error-code-label'], $data['error-code']);
        } elseif($response->getStatusCode() != '200'){
            throw new Exception\Request('error status from API', $response->getStatusCode());
        }

        if(!isset($data['items'])){
            throw new Exception\Exception('unexpected response from API');
        }

        if(count($data['items']) == 0){
            return [];
        }

        $collection = [];

        foreach($data['items'] as $index => $item){
            switch($item['type']){
                case 'MT':
                    $new = new Message($item['message-id']);
                    break;
                case 'MO':
                    $new = new InboundMessage($item['message-id']);
                    break;
                default:
                    throw new Exception\Exception('unexpected response from API');
            }

            $new->setResponse($response);
            $new->setIndex($index);
            $collection[] = $new;

        }

        return $collection;
    }

    /**
     * @param string|MessageInterface $idOrMessage
     */
    public function search($idOrMessage)
    {
        if($idOrMessage instanceof MessageInterface){
            $id = $idOrMessage->getMessageId();
            $message = $idOrMessage;
        } else {
            $id = $idOrMessage;
        }

        $request = new Request(
            $this->getClient()->getRestUrl() . '/search/message?' . http_build_query(['id' => $id]),
            'GET',
            'php://temp',
            ['Accept' => 'application/json']
        );

        $response = $this->client->send($request);

        $response->getBody()->rewind();

        $data = json_decode($response->getBody()->getContents(), true);

        if($response->getStatusCode() != '200' && isset($data['error-code'])){
            throw new Exception\Request($data['error-code-label'], $data['error-code']);
        } elseif($response->getStatusCode() == '429'){
            throw new Exception\Request('too many concurrent requests', $response->getStatusCode());
        } elseif($response->getStatusCode() != '200'){
            throw new Exception\Request('error status from API', $response->getStatusCode());
        }

        if(!$data){
            throw new Exception\Request('no message found for `' . $id . '`');
        }

        switch($data['type']){
            case 'MT':
                $new = new Message($data['message-id']);
                break;
            case 'MO':
                $new = new InboundMessage($data['message-id']);
                break;
            default:
                throw new Exception\Exception('unexpected response from API');
        }

        if(isset($message) && !($message instanceof $new)){
            throw new Exception\Exception(sprintf(
                'searched for message with type `%s` but message of type `%s`',
                get_class($message),
                get_class($new)
            ));
        }

        if(!isset($message)){
            $message = $new;
        }

        $message->setResponse($response);
        return $message;
    }

    public function searchRejections(Query $query) {

        $params = $query->getParams();
        $request = new Request(
            $this->getClient()->getRestUrl() . '/search/rejections?' . http_build_query($params),
            'GET',
            'php://temp',
            ['Accept' => 'application/json']
        );

        $response = $this->client->send($request);
        $response->getBody()->rewind();
        $data = json_decode($response->getBody()->getContents(), true);

        if($response->getStatusCode() != '200' && isset($data['error-code'])){
            throw new Exception\Request($data['error-code-label'], $data['error-code']);
        } elseif($response->getStatusCode() != '200'){
            throw new Exception\Request('error status from API', $response->getStatusCode());
        }

        if(!isset($data['items'])){
            throw new Exception\Exception('unexpected response from API');
        }

        if(count($data['items']) == 0){
            return [];
        }

        $collection = [];

        foreach($data['items'] as $index => $item){
            switch($item['type']){
                case 'MT':
                    $new = new Message($item['message-id']);
                    break;
                case 'MO':
                    $new = new InboundMessage($item['message-id']);
                    break;
                default:
                    throw new Exception\Exception('unexpected response from API');
            }

            $new->setResponse($response);
            $new->setIndex($index);
            $collection[] = $new;
        }

        return $collection;
    }

    /**
     * @param array $message
     * @return Message
     */
    protected function createMessageFromArray($message)
    {
        if(!is_array($message)){
            throw new \RuntimeException('message must implement `' . MessageInterface::class . '` or be an array`');
        }

        foreach(['to', 'from'] as $param){
            if(!isset($message[$param])){
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $to = $message['to'];
        $from = $message['from'];

        unset($message['to']);
        unset($message['from']);

        return new Message($to, $from, $message);
    }
    
    /**
     * Convenience feature allowing messages to be sent without creating a message object first.
     *
     * @param $name
     * @param $arguments
     * @return MessageInterface
     */
    public function __call($name, $arguments)
    {
        if(!(strstr($name, 'send') !== 0)){
            throw new \RuntimeException(sprintf(
                '`%s` is not a valid method on `%s`',
                $name,
                get_class($this)
            ));
        }

        $class = substr($name, 4);
        $class = 'Nexmo\\Message\\' . ucfirst(strtolower($class));

        if(!class_exists($class)){
            throw new \RuntimeException(sprintf(
                '`%s` is not a valid method on `%s`',
                $name,
                get_class($this)
            ));
        }

        $reflection = new \ReflectionClass($class);
        $message = $reflection->newInstanceArgs($arguments);

        return $this->send($message);
    }
}
