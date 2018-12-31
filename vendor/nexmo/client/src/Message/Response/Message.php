<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message\Response;
use Nexmo\Client\Response\Response;
use Nexmo\Client\Response\ResponseInterface;
use Nexmo\Message\Callback\Receipt;

class Message extends Response implements ResponseInterface
{
    /**
     * @var Receipt
     */
    protected $receipt;

    public function __construct(Array $data, Receipt $receipt = null)
    {
        $this->expected = array(
            'status',
            'message-id',
            'to',
            'message-price',
            'network'
        );

        //default value
        $data = array_merge(array('client-ref' => null, 'remaining-balance' => null), $data);

        $return = parent::__construct($data);

        //validate receipt
        if(!$receipt){
            return $return;
        }

        if($receipt->getId() != $this->getId()){
            throw new \UnexpectedValueException('receipt id must match message id');
        }

        $this->receipt = $receipt;

        return $receipt;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->data['status'];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return (string) $this->data['message-id'];
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return (string) $this->data['to'];
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return (string) $this->data['remaining-balance'];
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return (string) $this->data['message-price'];
    }

    /**
     * @return string
     */
    public function getNetwork()
    {
        return (string) $this->data['network'];
    }

    /**
     * @return string
     */
    public function getClientRef()
    {
        return (string) $this->data['client-ref'];
    }

    /**
     * @return Receipt|null
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @return bool
     */
    public function hasReceipt()
    {
        return $this->receipt instanceof Receipt;
    }
}