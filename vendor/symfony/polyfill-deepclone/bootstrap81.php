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

if (!function_exists('deepclone_to_array')) {
    function deepclone_to_array(mixed $value, ?array $allowed_classes = null, bool $allow_named_closures = false): array { return p\DeepClone::deepclone_to_array($value, $allowed_classes, $allow_named_closures); }
}
if (!function_exists('deepclone_from_array')) {
    function deepclone_from_array(array $data, ?array $allowed_classes = null, bool $allow_named_closures = false): mixed { return p\DeepClone::deepclone_from_array($data, $allowed_classes, $allow_named_closures); }
}
if (!defined('DEEPCLONE_HYDRATE_CALL_HOOKS')) {
    define('DEEPCLONE_HYDRATE_CALL_HOOKS', 1 << 0);
}
if (!defined('DEEPCLONE_HYDRATE_NO_LAZY_INIT')) {
    define('DEEPCLONE_HYDRATE_NO_LAZY_INIT', 1 << 1);
}
if (!defined('DEEPCLONE_HYDRATE_PRESERVE_REFS')) {
    define('DEEPCLONE_HYDRATE_PRESERVE_REFS', 1 << 2);
}
if (!function_exists('deepclone_hydrate')) {
    function deepclone_hydrate(object|string $object_or_class, array $vars = [], int $flags = 0): object { return p\DeepClone::deepclone_hydrate($object_or_class, $vars, $flags); }
}
