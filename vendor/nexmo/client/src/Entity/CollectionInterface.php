<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Entity;


interface CollectionInterface extends \Countable, \Iterator
{
    /**
     * @return string
     */
    public static function getCollectionName();

    /**
     * @return string
     */
    public static function getCollectionPath();

    /**
     * @param $data
     * @param $idOrEntity
     * @return mixed
     */
    public function hydrateEntity($data, $idOrEntity);
}