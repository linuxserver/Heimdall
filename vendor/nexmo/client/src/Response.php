<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo;

use Nexmo\Response\Message;

/**
 * Wrapper for Nexmo API Response, provides access to the count and status of 
 * the messages.
 */
class Response implements \Countable, \Iterator
{
    protected $data;

    protected $messages = array();

    protected $position = 0;
    
    public function __construct($data)
    {
        if(!is_string($data)){
            throw new \InvalidArgumentException('expected response data to be a string');
        }

        $this->data = json_decode($data, true);
    }

    public function getMessages()
    {
        if(!isset($this->data['messages'])){
            return array();
        }

        return $this->data['messages'];
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->data['message-count'];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return \Nexmo\Response\Message
     */
    public function current()
    {
        if(!isset($this->messages[$this->position])){
            $this->messages[$this->position] = new Message($this->data['messages'][$this->position]);
        }

        return $this->messages[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return isset($this->data['messages'][$this->position]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function toArray()
    {
        return $this->data;
    }
}