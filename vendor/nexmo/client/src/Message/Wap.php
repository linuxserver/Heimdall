<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Message;

/**
 * SMS Binary Message
 */
class Wap extends Message
{
    const TYPE = 'wappush';
    
    /**
     * Message Title
     * @var string
     */
    protected $title;
    
    /**
     * Message URL
     * @var string
     */
    protected $url;
    
    /**
     * Message Timeoupt
     * @var int
     */
    protected $validity;

    /**
     * Create a new SMS text message.
     *
     * @param string $to
     * @param string $from
     * @param string $title
     * @param string $url
     * @param int $validity
     */
    public function __construct($to, $from, $title, $url, $validity)
    {
        parent::__construct($to, $from);
        $this->title    = (string) $title;
        $this->url      =  (string) $url;
        $this->validity = (int) $validity;
    }
    
    /**
     * Get an array of params to use in an API request.
     */
    public function getRequestData($sent = true)
    {
        return array_merge(parent::getRequestData($sent), array(
            'title'      => $this->title,
            'url'        => $this->url,
            'validity'   => $this->validity,
        ));
    }
}
