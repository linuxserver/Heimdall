<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\DeepClone as p;

if (extension_loaded('deepclone')) {
    return;
}

if (\PHP_VERSION_ID >= 80100) {
    require __DIR__.'/bootstrap81.php';
}
