<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Application;
use Nexmo\Entity\FilterInterface;

/**
 * Simple value object for application filtering.
 */
class Filter implements FilterInterface
{
    const FORMAT = 'Y:m:d:H:i:s';

    protected $start;
    protected $end;

    public function __construct(\DateTime $start, \DateTime $end)
    {
        if($start < $end){
            $this->start = $start;
            $this->end = $end;
        } else {
            $this->start = $end;
            $this->end = $start;
        }
    }

    public function getQuery()
    {
        return [
            'date' => $this->start->format(self::FORMAT) . '-' . $this->end->format(self::FORMAT)
        ];
    }
}