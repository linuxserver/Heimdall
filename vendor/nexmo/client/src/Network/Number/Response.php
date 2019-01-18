<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Network\Number;
use Nexmo\Client\Response\Response as BaseResponse;
use Nexmo\Client\Response\ResponseInterface;

class Response extends BaseResponse implements ResponseInterface
{
    protected $callbacks = array();

    public function __construct(array $data, $callbacks = array())
    {
        //add expected keys
        $this->expected = array_merge($this->expected, array(
           'request_id', 'number', 'request_price', 'remaining_balance', 'callback_total_parts'
        ));

        parent::__construct($data);

        foreach($callbacks as $callback){
            if(!($callback instanceof Callback)){
                throw new \InvalidArgumentException('callback must be of type: Nexmo\Network\Number\Callback');
            }

            if($callback->getId() !== $this->getId()){
                throw new \InvalidArgumentException('callback id must match request id');
            }
        }

        $this->callbacks = $callbacks;
    }

    public function getCallbackTotal()
    {
        return $this->data['callback_total_parts'];
    }

    public function isComplete()
    {
        return count($this->callbacks) == $this->getCallbackTotal();
    }

    public function getPrice()
    {
        return $this->data['request_price'];
    }

    public function getBalance()
    {
        return $this->data['remaining_balance'];
    }

    public function getNumber()
    {
        return $this->data['number'];
    }

    public function getId()
    {
        return $this->data['request_id'];
    }

    public function getStatus()
    {
        return $this->data['status'];
    }

    public function __call($name, $args)
    {
        if(empty($this->callbacks)){
            throw new \BadMethodCallException('can not check for response data without callback data');
        }

        foreach($this->callbacks as $callback){
            if($last = $callback->$name()){
                return $last;
            }
        }
        return $last;
    }

    public function getCallbacks()
    {
        return $this->callbacks;
    }

    public static function addCallback(Response $response, Callback $callback)
    {
        $callbacks = $response->getCallbacks();
        $callbacks[] = $callback;

        return new static($response->getData(), $callbacks);
    }
}