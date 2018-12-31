<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;

interface CollectionAwareInterface
{
    /**
     * @param CollectionInterface $collection
     * @return mixed
     */
    public function setCollection(CollectionInterface $collection);

    /**
     * @return CollectionInterface
     */
    public function getCollection();
}