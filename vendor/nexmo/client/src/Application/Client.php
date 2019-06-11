<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;

use Nexmo\ApiErrorHandler;
use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\CollectionInterface;
use Nexmo\Entity\ModernCollectionTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;

class Client implements ClientAwareInterface, CollectionInterface
{
    use ClientAwareTrait;
    use ModernCollectionTrait;

    public static function getCollectionName()
    {
        return 'applications';
    }

    public static function getCollectionPath()
    {
        return '/v2/' . self::getCollectionName();
    }

    public function hydrateEntity($data, $id)
    {
        $application = new Application($id);
        $application->jsonUnserialize($data);
        return $application;
    }

    public function get($application)
    {
        if(!($application instanceof Application)){
            $application = new Application($application);
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $application->getId()
            ,'GET',
            'php://memory',
            ['Content-Type' => 'application/json']
        );

        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function create($application)
    {
        return $this->post($application);
    }

    public function post($application)
    {
        if(!($application instanceof Application)){
            $application = $this->createFromArray($application);
        }

        $body = $application->getRequestData(false);

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath()
            ,'POST',
            'php://temp',
            ['Content-Type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '201'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function update($application, $id = null)
    {
        return $this->put($application, $id);
    }

    public function put($application, $id = null)
    {
        if(!($application instanceof Application)){
            $application = $this->createFromArray($application);
        }

        if(is_null($id)){
            $id = $application->getId();
        }

        $body = $application->getRequestData(false);

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $id,
            'PUT',
            'php://temp',
            ['Content-Type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $application->setRequest($request);
        $response = $this->client->send($request);
        $application->setResponse($response);

        if($response->getStatusCode() != '200'){
            throw $this->getException($response, $application);
        }

        return $application;
    }

    public function delete($application)
    {
        if(($application instanceof Application)){
            $id = $application->getId();
        } else {
            $id = $application;
        }

        $request = new Request(
            $this->getClient()->getApiUrl(). $this->getCollectionPath() . '/' . $id
            ,'DELETE',
            'php://temp',
            ['Content-Type' => 'application/json']
        );

        if($application instanceof Application){
            $application->setRequest($request);
        }

        $response = $this->client->send($request);

        if($application instanceof Application){
            $application->setResponse($response);
        }

        if($response->getStatusCode() != '204'){
            throw $this->getException($response, $application);
        }

        return true;
    }

    protected function getException(ResponseInterface $response, $application = null)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // Handle new style errors
        $e = null;
        try {
            ApiErrorHandler::check($body, $status);
        } catch (Exception\Exception $e) {
            //todo use interfaces here
            if(($application instanceof Application) AND (($e instanceof Exception\Request) OR ($e instanceof Exception\Server))){
                $e->setEntity($application);
            }
        }

        return $e;
    }

    protected function createFromArray($array)
    {
        if (isset($array['answer_url']) || isset($array['event_url'])) {
            return $this->createFromArrayV1($array);
        }

        return $this->createFromArrayV2($array);
    }

    protected function createFromArrayV1($array) {
        if(!is_array($array)){
            throw new \RuntimeException('application must implement `' . ApplicationInterface::class . '` or be an array`');
        }

        foreach(['name',] as $param){
            if(!isset($array[$param])){
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $application = new Application();
        $application->setName($array['name']);

        // Public key?
        if (isset($array['public_key'])) {
            $application->setPublicKey($array['public_key']);
        }

        // Voice
        foreach(['event', 'answer'] as $type){
            if(isset($array[$type . '_url'])){
                $method = isset($array[$type . '_method']) ? $array[$type . '_method'] : null;
                $application->getVoiceConfig()->setWebhook($type . '_url', new Webhook($array[$type . '_url'], $method));
            }
        }

        // Messages
        foreach(['status', 'inbound'] as $type){
            if(isset($array[$type . '_url'])){
                $method = isset($array[$type . '_method']) ? $array[$type . '_method'] : null;
                $application->getMessagesConfig()->setWebhook($type . '_url', new Webhook($array[$type . '_url'], $method));
            }
        }

        // RTC
        foreach(['event'] as $type){
            if(isset($array[$type . '_url'])){
                $method = isset($array[$type . '_method']) ? $array[$type . '_method'] : null;
                $application->getRtcConfig()->setWebhook($type . '_url', new Webhook($array[$type . '_url'], $method));
            }
        }

        // VBC
        if (isset($array['vbc']) && $array['vbc']) {
            $application->getVbcConfig()->enable();
        }

        return $application;
    }

    protected function createFromArrayV2($array) {
        if(!is_array($array)){
            throw new \RuntimeException('application must implement `' . ApplicationInterface::class . '` or be an array`');
        }

        foreach(['name',] as $param){
            if(!isset($array[$param])){
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $application = new Application();
        $application->setName($array['name']);

        // Is there a public key?
        if (isset($array['keys']['public_key'])) {
            $application->setPublicKey($array['keys']['public_key']);
        }

        // How about capabilities?
        if (!isset($array['capabilities'])) {
            return $application;
        }

        $capabilities = $array['capabilities'];

        // Handle voice
        if (isset($capabilities['voice'])) {
            $voiceCapabilities = $capabilities['voice']['webhooks'];

            foreach(['answer', 'event'] as $type)
            $application->getVoiceConfig()->setWebhook($type.'_url', new Webhook(
                $voiceCapabilities[$type.'_url']['address'],
                $voiceCapabilities[$type.'_url']['http_method']
            ));
        }

        // Handle messages
        if (isset($capabilities['messages'])) {
            $messagesCapabilities = $capabilities['messages']['webhooks'];

            foreach(['status', 'inbound'] as $type)
            $application->getMessagesConfig()->setWebhook($type.'_url', new Webhook(
                $messagesCapabilities[$type.'_url']['address'],
                $messagesCapabilities[$type.'_url']['http_method']
            ));
        }

        // Handle RTC
        if (isset($capabilities['rtc'])) {
            $rtcCapabilities = $capabilities['rtc']['webhooks'];

            foreach(['event'] as $type)
            $application->getRtcConfig()->setWebhook($type.'_url', new Webhook(
                $rtcCapabilities[$type.'_url']['address'],
                $rtcCapabilities[$type.'_url']['http_method']
            ));
        }

        // Handle VBC
        if (isset($capabilities['vbc'])) {
            $application->getVbcConfig()->enable();
        }

        return $application;
    }
}
