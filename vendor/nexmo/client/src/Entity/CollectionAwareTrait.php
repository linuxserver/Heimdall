<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

trait CollectionAwareTrait
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    public function setCollection(CollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        if(!isset($this->collection)){
            throw new \RuntimeException('missing collection');
        }

        return $this->collection;
    }
}