<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Verify;

use Nexmo\Client\Exception\Request as RequestException;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\Psr7Trait;
use Nexmo\Entity\RequestArrayTrait;

class Verification implements VerificationInterface, \ArrayAccess, \Serializable
{
    use Psr7Trait;
    use RequestArrayTrait;
    use JsonResponseTrait;

    /**
     * Possible verification statuses.
     */
    const FAILED = 'FAILED';
    const SUCCESSFUL = 'SUCCESSFUL';
    const EXPIRED = 'EXPIRED';
    const IN_PROGRESS = 'IN PROGRESS';

    protected $dirty = true;

    /**
     * @var Client;
     */
    protected $client;

    /**
     * Create a verification with a number and brand, or the `request_id` of an existing verification.
     *
     * @param string $idOrNumber The number to verify, or the `request_id` of an existing verification.
     * @param null|string $brand The brand that identifies your application to the user.
     * @param array $additional Additional parameters can be set as keys / values.
     */
    public function __construct($idOrNumber, $brand = null, $additional = [])
    {
        if(is_null($brand)){
            $this->dirty = false;
            $this->requestData['request_id'] = $idOrNumber;
        } else {
            $this->dirty = true;
            $this->requestData['number'] = $idOrNumber;
            $this->requestData['brand']  = $brand;
            $this->requestData = array_merge($this->requestData, $additional);
        }
    }

    /**
     * Allow Verification to have actions.
     *
     * @param Client $client Verify Client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * @return Client
     */
    protected function useClient()
    {
        if(isset($this->client)){
            return $this->client;
        }

        throw new \RuntimeException('can not act on the verification directly unless a verify client has been set');
    }

    /**
     * Check if the code is correct. Unlike the method it proxies, an invalid code does not throw an exception.
     *
     * @uses \Nexmo\Verify\Client::check()
     * @param string $code Numeric code provided by the user.
     * @param null|string $ip IP address to be used for the verification.
     * @return bool Code is valid.
     * @throws RequestException
     */
    public function check($code, $ip = null)
    {
        try {
            $this->useClient()->check($this, $code, $ip);
            return true;
        } catch(RequestException $e) {
            if($e->getCode() == 16 || $e->getCode() == 17){
                return false;
            }

            throw $e;
        }
    }

    /**
     * Cancel the verification.
     *
     * @uses \Nexmo\Verify\Client::cancel()
     */
    public function cancel()
    {
        $this->useClient()->cancel($this);
    }

    /**
     * Trigger the next verification.
     *
     * @uses \Nexmo\Verify\Client::trigger()
     */
    public function trigger()
    {
        $this->useClient()->trigger($this);
    }

    /**
     * Update Verification from the API.
     *
     * @uses \Nexmo\Verify\Client::search()
     */
    public function sync()
    {
        $this->useClient()->search($this);
    }

    /**
     * Check if the user provided data has sent to the API yet.
     *
     * @return bool
     */
    public function isDirty()
    {
        return $this->dirty;
    }

    /**
     * If do not set number in international format or you are not sure if number is correctly formatted, set country
     * with the two-character country code. For example, GB, US. Verify works out the international phone number for
     * you.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $country
     * @return $this
     * @throws \Exception
     */
    public function setCountry($country)
    {
        return $this->setRequestData('country', $country);
    }

    /**
     * An 11 character alphanumeric string to specify the SenderID for SMS sent by Verify. Depending on the destination
     * of the phone number you are applying, restrictions may apply. By default, sender_id is VERIFY.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $id
     * @return $this
     * @throws \Exception
     */
    public function setSenderId($id)
    {
        return $this->setRequestData('sender_id', $id);
    }

    /**
     * The length of the PIN. Possible values are 6 or 4 characters. The default value is 4.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $length
     * @return $this
     * @throws \Exception
     */
    public function setCodeLength($length)
    {
        return $this->setRequestData('code_length', $length);
    }

    /**
     * By default, TTS are generated in the locale that matches number. For example, the TTS for a 33* number is sent in
     * French. Use this parameter to explicitly control the language, accent and gender used for the Verify request. The
     * default language is en-us.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $language
     * @return $this
     * @throws \Exception
     */
    public function setLanguage($language)
    {
        return $this->setRequestData('lg', $language);
    }

    /**
     * Restrict verification to a certain network type. Possible values are:
     * - All (Default)
     * - Mobile
     * - Landline
     *
     * Note: contact support@nexmo.com to enable this feature.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $type
     * @return $this
     * @throws \Exception
     */
    public function setRequireType($type)
    {
        return $this->setRequestData('require_type', $type);
    }

    /**
     * The PIN validity time from generation. This is an integer value between 30 and 3600 seconds. The default is 300
     * seconds. When specified together, pin_expiry must be an integer multiple of next_event_wait. Otherwise,
     * pin_expiry is set to next_event_wait.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $time
     * @return $this
     * @throws \Exception
     */
    public function setPinExpiry($time)
    {
        return $this->setRequestData('pin_expiry', $time);
    }

    /**
     * An integer value between 60 and 900 seconds inclusive that specifies the wait time between attempts to deliver
     * the PIN. Verify calculates the default value based on the average time taken by users to complete verification.
     * @link https://docs.nexmo.com/verify/api-reference/api-reference#vrequest
     *
     * Can only be set before the verification is created.
     * @uses \Nexmo\Entity\RequestArrayTrait::setRequestData
     *
     * @param $time
     * @return $this
     * @throws \Exception
     */
    public function setWaitTime($time)
    {
        return $this->setRequestData('next_event_wait', $time);
    }

    /**
     * Get the verification request id, if available.
     *
     * @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getRequestId()
    {
        return $this->proxyArrayAccess('request_id');
    }

    /**
     * Get the number verified / to be verified.
     *
     * @see \Nexmo\Verify\Verification::__construct()
     * @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getNumber()
    {
        return $this->proxyArrayAccess('number');
    }

    /**
     * Get the account id, if available.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getAccountId()
    {
        return $this->proxyArrayAccess('account_id');
    }

    /**
     * Get the sender id, if available.
     *
     * @see \Nexmo\Verify\Verification::setSenderId();
     * @see \Nexmo\Verify\Client::search();
     *
     * @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getSenderId()
    {
        return $this->proxyArrayAccess('sender_id');
    }

    /**
     * Get the price of the verification, if available.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->proxyArrayAccess('price');
    }

    /**
     * Get the currency used to price the verification, if available.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->proxyArrayAccess('currency');
    }

    /**
     * Get the status of the verification, if available.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->proxyArrayAccess('status');
    }

    /**
     * Get an array of verification checks, if available. Will return an empty array if no check have been made, or if
     * the data is not available.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @return \Nexmo\Verify\Check[]|\Nexmo\Verify\Check
     */
    public function getChecks()
    {
        $checks = $this->proxyArrayAccess('checks');
        if(!$checks){
            return [];
        }

        foreach($checks as $i => $check) {
            $checks[$i] = new Check($check);
        }

        return $checks;
    }

    /**
     * Get the date the verification started.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccessDate()
     *
     * @return \DateTime|null
     */
    public function getSubmitted()
    {
        return $this->proxyArrayAccessDate('date_submitted');
    }

    /**
     * Get the date the verification stopped.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccessDate()
     *
     * @return \DateTime|null
     */
    public function getFinalized()
    {
        return $this->proxyArrayAccessDate('date_finalized');
    }

    /**
     * Get the date of the first verification event.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccessDate()
     *
     * @return \DateTime|null
     */
    public function getFirstEvent()
    {
        return $this->proxyArrayAccessDate('first_event_date');
    }

    /**
     * Get the date of the last verification event.
     *
     * Only available after a searching for a verification.
     * @see \Nexmo\Verify\Client::search();
     *
     * However still @uses \Nexmo\Verify\Verification::proxyArrayAccessDate()
     *
     * @return \DateTime|null
     */
    public function getLastEvent()
    {
        return $this->proxyArrayAccessDate('last_event_date');
    }

    /**
     * Proxies `proxyArrayAccess()` and returns a DateTime if the parameter is found.
     * @uses \Nexmo\Verify\Verification::proxyArrayAccess()
     *
     * @param string $param Parameter to look for.
     * @return \DateTime
     */
    protected function proxyArrayAccessDate($param)
    {
        $date = $this->proxyArrayAccess($param);
        if($date) {
            return new \DateTime($date);
        }
    }

    /**
     * Simply proxies array access to check for a parameter in the response, request, or user provided data.
     *
     * @uses \Nexmo\Verify\Verification::offsetGet();
     * @uses \Nexmo\Verify\Verification::offsetExists();
     *
     * @param string $param Parameter to look for.
     * @return mixed
     */
    protected function proxyArrayAccess($param)
    {
        if(isset($this[$param])){
            return $this[$param];
        }
    }

    /**
     * Allow the object to access the data from the API response, a sent API request, or the user set data that the
     * request will be created from - in that order.
     *
     * @param mixed $offset
     * @return bool
     * @throws \Exception
     */
    public function offsetExists($offset)
    {
        $response = $this->getResponseData();
        $request  = $this->getRequestData();
        $dirty    = $this->requestData;
        return isset($response[$offset]) || isset($request[$offset]) || isset($dirty[$offset]);
    }

    /**
     * Allow the object to access the data from the API response, a sent API request, or the user set data that the
     * request will be created from - in that order.
     *
     * @param mixed $offset
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($offset)
    {
        $response = $this->getResponseData();
        $request  = $this->getRequestData();
        $dirty    = $this->requestData;

        if(isset($response[$offset])){
            return $response[$offset];
        }

        if(isset($request[$offset])){
            return $request[$offset];
        }

        if(isset($dirty[$offset])){
            return $dirty[$offset];
        }
    }

    /**
     * All properties are read only.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw $this->getReadOnlyException($offset);
    }

    /**
     * All properties are read only.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw $this->getReadOnlyException($offset);
    }

    /**
     * All properties are read only.
     *
     * @param $offset
     * @return \RuntimeException
     */
    protected function getReadOnlyException($offset)
    {
        return new \RuntimeException(sprintf(
            'can not modify `%s` using array access',
            $offset
        ));
    }

    public function serialize()
    {
        $data = [
            'requestData'  => $this->requestData
        ];

        if($request = $this->getRequest()){
            $data['request'] = \Zend\Diactoros\Request\Serializer::toString($request);
        }

        if($response = $this->getResponse()){
            $data['response'] = \Zend\Diactoros\Response\Serializer::toString($response);
        }

        return serialize($data);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->requestData = $data['requestData'];

        if(isset($data['request'])){
            $this->request = \Zend\Diactoros\Request\Serializer::fromString($data['request']);
        }

        if(isset($data['response'])){
            $this->response = \Zend\Diactoros\Response\Serializer::fromString($data['response']);
        }
    }


}