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
class Vcal extends Message
{
    const TYPE = 'vcal';
    
    /**
     * Message Body
     * @var string
     */
    protected $vcal;
    
    /**
     * Create a new SMS text message.
     * 
     * @param string $to
     * @param string $from
     * @param string $vcal
     */
    public function __construct($to, $from, $vcal)
    {
        parent::__construct($to, $from);
        $this->text = (string) $vcal;
    }
    
    /**
     * Get an array of params to use in an API request.
     */
    public function getRequestData($sent = true)
    {
        return array_merge(parent::getRequestData($sent), array(
            'vcal' => $this->vcal
        ));        
    }
}
