<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;


use Nexmo\Entity\JsonUnserializableInterface;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\Psr7Trait;

class Application implements EntityInterface, \JsonSerializable, JsonUnserializableInterface
{
    use JsonSerializableTrait;
    use Psr7Trait;
    use JsonResponseTrait;

    protected $voiceConfig;

    protected $name;

    protected $keys = [];

    protected $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setVoiceConfig(VoiceConfig $config)
    {
        $this->voiceConfig = $config;
        return $this;
    }

    /**
     * @return VoiceConfig
     */
    public function getVoiceConfig()
    {
        if(!isset($this->voiceConfig)){
            $this->setVoiceConfig(new VoiceConfig());
            $data = $this->getResponseData();
            if(isset($data['voice']) AND isset($data['voice']['webhooks'])){
                foreach($data['voice']['webhooks'] as $webhook){
                    $this->voiceConfig->setWebhook($webhook['endpoint_type'], $webhook['endpoint'], $webhook['http_method']);
                }
            }
        }

        return $this->voiceConfig;
    }

    public function getPublicKey()
    {
        if(isset($this->keys['public_key'])){
            return $this->keys['public_key'];
        }
    }

    public function getPrivateKey()
    {
        if(isset($this->keys['private_key'])){
            return $this->keys['private_key'];
        }
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function jsonUnserialize(array $json)
    {
        $this->name = $json['name'];
        $this->id   = $json['id'];
        $this->keys = $json['keys'];

        //todo: make voice  hydrate-able
        $this->voiceConfig = new VoiceConfig();
        if(isset($json['voice']) AND isset($json['voice']['webhooks'])){
            foreach($json['voice']['webhooks'] as $webhook){
                $this->voiceConfig->setWebhook($webhook['endpoint_type'], new Webhook($webhook['endpoint'], $webhook['http_method']));
            }
        }
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->getName(),
            //currently, the request data does not match the response data
            'event_url' => (string) $this->getVoiceConfig()->getWebhook(VoiceConfig::EVENT),
            'answer_url' => (string) $this->getVoiceConfig()->getWebhook(VoiceConfig::ANSWER),
            'type' => 'voice' //currently the only type
        ];
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}