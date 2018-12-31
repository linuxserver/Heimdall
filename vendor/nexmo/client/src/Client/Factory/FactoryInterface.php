<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Client\Factory;

/**
 * Interface FactoryInterface
 *
 * Factor create API clients (clients specific to single API, that leverages Nexmo\Client for HTTP communication and
 * common functionality).
 */
interface FactoryInterface
{
    /**
     * @param $api
     * @return bool
     */
    public function hasApi($api);

    /**
     * @param $api
     * @return mixed
     */
    public function getApi($api);
}