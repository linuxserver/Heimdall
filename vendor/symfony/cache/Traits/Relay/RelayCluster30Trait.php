<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Traits\Relay;

if (version_compare(phpversion('relay'), '0.30.0', '>=')) {
    /**
     * @internal
     */
    trait RelayCluster30Trait
    {
        public function increx($key, $value = null, $options = null): \Relay\Cluster|array|false
        {
            return $this->initializeLazyObject()->increx(...\func_get_args());
        }

        public function xnack($key, $group, $mode, $ids, $options = null): \Relay\Cluster|false|int
        {
            return $this->initializeLazyObject()->xnack(...\func_get_args());
        }
    }
} else {
    /**
     * @internal
     */
    trait RelayCluster30Trait
    {
    }
}
