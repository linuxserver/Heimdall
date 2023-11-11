<?php

use Illuminate\Support\Str;

/**
 * @param $bytes
 * @param bool $is_drive_size
 * @param string $beforeunit
 * @param string $afterunit
 * @return string
 */
function format_bytes($bytes, bool $is_drive_size = true, string $beforeunit = '', string $afterunit = ''): string
{
    $btype = ($is_drive_size === true) ? 1000 : 1024;
    $labels = ['B', 'KB', 'MB', 'GB', 'TB'];
    // use 1000 rather than 1024 to simulate HD size not real size
    for ($x = 0; $bytes >= $btype && $x < (count($labels) - 1); $bytes /= $btype, $x++) ;
    if ($labels[$x] == 'TB') {
        return round($bytes, 3) . $beforeunit . $labels[$x] . $afterunit;
    } elseif ($labels[$x] == 'GB') {
        return round($bytes, 2) . $beforeunit . $labels[$x] . $afterunit;
    } elseif ($labels[$x] == 'MB') {
        return round($bytes, 2) . $beforeunit . $labels[$x] . $afterunit;
    } else {
        return round($bytes, 0) . $beforeunit . $labels[$x] . $afterunit;
    }
}

/**
 * @param $title
 * @param string $separator
 * @param string $language
 * @return string
 */
function str_slug($title, string $separator = '-', string $language = 'en'): string
{
    return Str::slug($title, $separator, $language);
}

if (!function_exists('str_is')) {
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|array $pattern
     * @param string $value
     * @return bool
     *
     * @deprecated Str::is() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function str_is($pattern, string $value): bool
    {
        return Str::is($pattern, $value);
    }
}

/**
 * @param $hex
 * @return float|int
 */
function get_brightness($hex)
{
    // returns brightness value from 0 to 255
    // strip off any leading #
    // $hex = str_replace('#', '', $hex);
    $hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}

/**
 * @param $hex
 * @return string
 */
function title_color($hex): string
{
    if (get_brightness($hex) > 130) {
        return ' black';
    } else {
        return ' white';
    }
}

/**
 * @return string
 */
function getLinkTargetAttribute(): string
{
    $target = \App\Setting::fetch('window_target');

    if ($target === 'current') {
        return '';
    } else {
        return ' target="' . $target . '"';
    }
}

/**
 * @param $name
 * @return array|string|string[]|null
 */
function className($name)
{
    return preg_replace('/[^\p{L}\p{N}]/u', '', $name);
}

/**
 * @param string $file
 * @param string $extension
 * @return bool
 */
function isImage(string $file, string $extension): bool
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp'];

    if (!in_array($extension, $allowedExtensions)) {
        return false;
    }

    $tempFileName = @tempnam("/tmp", "image-check-");
    $handle = fopen($tempFileName, "w");

    fwrite($handle, $file);
    fclose($handle);

    if ($extension == 'svg') {
        return 'image/svg+xml' === mime_content_type($tempFileName);
    }

    $size = @getimagesize($tempFileName);
    return is_array($size) && str_starts_with($size['mime'], 'image');
}
