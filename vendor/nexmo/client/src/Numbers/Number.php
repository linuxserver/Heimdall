<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Numbers;


use Nexmo\Application\Application;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableInterface;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\JsonUnserializableInterface;
use Nexmo\Entity\NoRequestResponseTrait;

class Number implements EntityInterface, JsonSerializableInterface, JsonUnserializableInterface
{
    use JsonSerializableTrait;
    use NoRequestResponseTrait;
    use JsonResponseTrait;

    const TYPE_MOBILE = 'mobile-lvn';
    const TYPE_FIXED  = 'landline';

    const FEATURE_VOICE = 'VOICE';
    const FEATURE_SMS   = 'SMS';

    const WEBHOOK_MESSAGE      = 'moHttpUrl';
    const WEBHOOK_VOICE_STATUS = 'voiceStatusCallbackUrl';

    const ENDPOINT_SIP  = 'sip';
    const ENDPOINT_TEL  = 'tel';
    const ENDPOINT_VXML = 'vxml';
    const ENDPOINT_APP  = 'app';

    protected $data = [];

    public function __construct($number = null, $country = null)
    {
        $this->data['msisdn'] = $number;
        $this->data['country'] = $country;
    }

    public function getId()
    {
        return $this->fromData('msisdn');
    }

    public function getMsisdn()
    {
        return $this->getId();
    }

    public function getNumber()
    {
        return $this->getId();
    }

    public function getCountry()
    {
        return $this->fromData('country');
    }

    public function getType()
    {
        return $this->fromData('type');
    }

    public function getCost()
    {
        return $this->fromData('cost');
    }

    public function hasFeature($feature)
    {
        if(!isset($this->data['features'])){
            return false;
        }

        return in_array($feature, $this->data['features']);
    }

    public function getFeatures()
    {
        return $this->fromData('features');
    }

    public function setWebhook($type, $url)
    {
        if(!in_array($type, [self::WEBHOOK_MESSAGE, self::WEBHOOK_VOICE_STATUS])){
            throw new \InvalidArgumentException("invalid webhook type `$type`");
        }

        $this->data[$type] = $url;
        return $this;
    }

    public function getWebhook($type)
    {
        return $this->fromData($type);
    }

    public function hasWebhook($type)
    {
        return isset($this->data[$type]);
    }

    public function setVoiceDestination($endpoint, $type = null)
    {
        if(is_null($type)){
            $type = $this->autoType($endpoint);
        }

        if(self::ENDPOINT_APP == $type AND !($endpoint instanceof Application)){
            $endpoint = new Application($endpoint);
        }

        $this->data['voiceCallbackValue'] = $endpoint;
        $this->data['voiceCallbackType'] = $type;

        return $this;
    }

    protected function autoType($endpoint)
    {
        if($endpoint instanceof Application){
            return self::ENDPOINT_APP;
        }

        if(false !== strpos($endpoint, '@')){
            return self::ENDPOINT_SIP;
        }

        if(0 === strpos(strtolower($endpoint), 'http')){
            return self::ENDPOINT_VXML;
        }

        if(preg_match('#[a-z]+#', $endpoint)){
            return self::ENDPOINT_APP;
        }

        return self::ENDPOINT_TEL;
    }

    public function getVoiceDestination()
    {
        return $this->fromData('voiceCallbackValue');
    }

    public function getVoiceType()
    {
        if(!isset($this->data['voiceCallbackType'])){
            return null;
        }

        return $this->data['voiceCallbackType'];
    }

    protected function fromData($name)
    {
        if(!isset($this->data[$name])){
            throw new \RuntimeException("`{$name}` has not been set");
        }

        return $this->data[$name];
    }

    public function jsonUnserialize(array $json)
    {
        $this->data = $json;
    }

    function jsonSerialize()
    {
        $json = $this->data;
        if(isset($json['voiceCallbackValue']) AND ($json['voiceCallbackValue'] instanceof Application)){
            $json['voiceCallbackValue'] = $json['voiceCallbackValue']->getId();
        }

        return $json;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}