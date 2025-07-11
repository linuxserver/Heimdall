<?php

/**
 * League.Uri (https://uri.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Uri;

use finfo;
use League\Uri\Exceptions\MissingFeature;
use League\Uri\IPv4\Calculator;

use const PHP_INT_SIZE;

/**
 * Allow detecting features needed to make the packages work.
 */
final class FeatureDetection
{
    public static function supportsFileDetection(): void
    {
        static $isSupported = null;
        $isSupported = $isSupported ?? class_exists(finfo::class);

        if (!$isSupported) {
            throw new MissingFeature('Support for file type detection requires the `fileinfo` extension.');
        }
    }

    public static function supportsIdn(): void
    {
        static $isSupported = null;
        $isSupported = $isSupported ?? (function_exists('\idn_to_ascii') && defined('\INTL_IDNA_VARIANT_UTS46'));

        if (!$isSupported) {
            throw new MissingFeature('Support for IDN host requires the `intl` extension for best performance or run "composer require symfony/polyfill-intl-idn" to install a polyfill.');
        }
    }

    public static function supportsIPv4Conversion(): void
    {
        static $isSupported = null;
        $isSupported = $isSupported ?? (extension_loaded('gmp') || extension_loaded('bcmath') || (4 < PHP_INT_SIZE));

        if (!$isSupported) {
            throw new MissingFeature('A '.Calculator::class.' implementation could not be automatically loaded. To perform IPv4 conversion use a x.64 PHP build or install one of the following extension GMP or BCMath. You can also ship your own implmentation.');
        }
    }
}
