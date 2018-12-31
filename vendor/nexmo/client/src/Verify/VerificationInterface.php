<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Verify;


interface VerificationInterface extends \Nexmo\Entity\EntityInterface
{
    public function getNumber();
    public function setCountry($country);
    public function setSenderId($id);
    public function setCodeLength($length);
    public function setLanguage($language);
    public function setRequireType($type);
    public function setPinExpiry($time);
    public function setWaitTime($time);
}