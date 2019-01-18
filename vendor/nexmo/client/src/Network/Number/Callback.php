<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Network\Number;
use Nexmo\Client\Callback\CallbackInterface;
use Nexmo\Client\Callback\Callback as BaseCallback;

/**
 * @method null|string getType()
 * @method bool hasType()
 * @method null|string getNetwork()
 * @method bool hasNetwork()
 * @method null|string getNetworkName()
 * @method bool hasNetworkName()
 * @method null|string getValid()
 * @method bool hasValid()
 * @method null|string getPorted()
 * @method bool hasPorted()
 * @method null|string getReachable()
 * @method bool hasReachable()
 * @method null|string getRoaming()
 * @method bool hasRoaming()
 * @method null|string getRoamingCountry()
 * @method bool hasRoamingCountry()
 * @method null|string getRoamingNetwork()
 * @method bool hasRoamingNetwork()
 */
class Callback extends BaseCallback implements CallbackInterface
{
    protected $expected = array('request_id', 'callback_part', 'callback_total_parts', 'number', 'status');
    protected $optional = array(
        'Type' => 'number_type',
        'Network' => 'carrier_network_code',
        'NetworkName' => 'carrier_network_name',
        'Valid' => 'valid',
        'Ported' => 'ported',
        'Reachable' => 'reachable',
        'Roaming' => 'roaming',
        'RoamingCountry' => 'roaming_country_code',
        'RoamingNetwork' => 'roaming_network_code',
    );

    public function getId()
    {
        return $this->data['request_id'];
    }

    public function getCallbackTotal()
    {
        return $this->data['callback_total_parts'];
    }

    public function getCallbackIndex()
    {
        return $this->data['callback_part'];
    }

    public function getNumber()
    {
        return $this->data['number'];
    }

    public function __call($name, $args)
    {
        $type = substr($name, 0, 3);
        $property = substr($name, 3);

        if(!isset($this->optional[$property])){
            throw new \BadMethodCallException('property does not exist: ' . $property);
        }

        $property = $this->optional[$property];

        switch($type){
            case 'get':
                if(isset($this->data[$property])){
                    return $this->data[$property];
                } else {
                    return null;
                }
                break;
            case 'has':
                return isset($this->data[$property]);
                break;
        }

        throw new \BadMethodCallException('method does not exist: ' . $name);
    }
}