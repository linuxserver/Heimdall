<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Call;

use Nexmo\Conversations\Conversation;
use Nexmo\Entity\FilterInterface;

class Filter implements FilterInterface
{
    protected $query = [];

    public function getQuery()
    {
        return $this->query;
    }

    public function sortAscending()
    {
        return $this->setOrder('asc');
    }

    public function sortDescending()
    {
        return $this->setOrder('desc');
    }

    public function setStatus($status)
    {
        $this->query['status'] = (string) $status;
        return $this;
    }

    public function setStart(\DateTime $start)
    {
        $start->setTimezone(new \DateTimeZone("UTC"));
        $this->query['date_start'] = $start->format('Y-m-d\TH:i:s\Z');
        return $this;
    }

    public function setEnd(\DateTime $end)
    {
        $end->setTimezone(new \DateTimeZone("UTC"));
        $this->query['date_end'] = $end->format('Y-m-d\TH:i:s\Z');
        return $this;
    }

    public function setSize($size)
    {
        $this->query['page_size'] = (int) $size;
        return $this;
    }

    public function setIndex($index)
    {
        $this->query['record_index'] = (int) $index;
        return $this;
    }

    public function setOrder($order)
    {
        $this->query['order'] = (string) $order;
        return $this;
    }

    public function setConversation($conversation)
    {
        if($conversation instanceof Conversation){
            $conversation = $conversation->getId();
        }

        $this->query['conversation_uuid'] = $conversation;

        return $this;
    }
}
