<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message;

/**
 * SMS Text Message
 */
class AutoDetect extends Message
{
    const TYPE = 'text';
    
    /**
     * Message Body
     * @var string
     */
    protected $text;
    
    /**
     * Create a new SMS text message.
     * 
     * @param string $to
     * @param string $from
     * @param string $text
     * @param array  $additional
     */
    public function __construct($to, $from, $text, $additional = [])
    {
        parent::__construct($to, $from, $additional);
        $this->enableEncodingDetection();
        $this->requestData['text'] = (string) $text;
    }
}
