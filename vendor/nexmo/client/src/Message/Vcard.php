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
class Vcard extends Message
{
    const TYPE = 'vcard';

    /**
     * Message Body
     * @var string
     */
    protected $vcard;

    /**
     * Create a new SMS text message.
     *
     * @param string $to
     * @param string $from
     * @param string $vcard
     */
    public function __construct($to, $from, $vcard)
    {
        parent::__construct($to, $from);
        $this->vcard = (string) $vcard;
    }

    /**
     * Get an array of params to use in an API request.
     */
    public function getRequestData($sent = true)
    {
        return array_merge(parent::getRequestData($sent), array(
            'vcard' => $this->vcard
        ));
    }
}
