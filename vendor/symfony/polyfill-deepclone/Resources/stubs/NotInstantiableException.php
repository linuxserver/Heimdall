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
     * Thrown by {@see \deepclone_to_array()} when the input contains a value
     * that has no meaningful array representation: a resource, an anonymous
     * class, a Reflection object, an IteratorIterator subclass, or any other
     * type the extension cannot round-trip.
     */
    class NotInstantiableException extends \InvalidArgumentException
    {
    }
}
