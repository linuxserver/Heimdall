<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DeepClone;

if (!\extension_loaded('deepclone')) {
    /**
     * Thrown by {@see \deepclone_from_array()} when the payload references a
     * class that no longer exists in the running PHP process.
     */
    class ClassNotFoundException extends \InvalidArgumentException
    {
    }
}
