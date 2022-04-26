<?php

declare(strict_types=1);

namespace GuzzleHttp\Psr7;

final class Header
{
    /**
     * Parse an array of header values containing ";" separated data into an
     * array of associative arrays representing the header key value pair data
     * of the header. When a parameter does not contain a value, but just
     * contains a key, this function will inject a key with a '' string value.
     *
     * @param string|array $header Header to parse into components.
     */
    public static function parse($header): array
    {
        static $trimmed = "\"'  \n\t\r";
        $params = $matches = [];

        foreach (self::normalize($header) as $val) {
            $part = [];
            foreach (preg_split('/;(?=([^"]*"[^"]*")*[^"]*$)/', $val) as $kvp) {
                if (preg_match_all('/<[^>]+>|[^=]+/', $kvp, $matches)) {
                    $m = $matches[0];
                    if (isset($m[1])) {
                        $part[trim($m[0], $trimmed)] = trim($m[1], $trimmed);
                    } else {
                        $part[] = trim($m[0], $trimmed);
                    }
                }
            }
            if ($part) {
                $params[] = $part;
            }
        }

        return $params;
    }

    /**
     * Converts an array of header values that may contain comma separated
     * headers into an array of headers with no comma separated values.
     *
     * @param string|array $header Header to normalize.
     */
    public static function normalize($header): array
    {
        $result = [];
        foreach ((array) $header as $value) {
            foreach ((array) $value as $v) {
                if (strpos($v, ',') === false) {
                    $trimmed = trim($v);
                    if ($trimmed !== '') {
                        $result[] = $trimmed;
                    }
                    continue;
                }
                foreach (preg_split('/,(?=([^"]*"([^"]|\\\\.)*")*[^"]*$)/', $v) as $vv) {
                    $trimmed = trim($vv);
                    if ($trimmed !== '') {
                        $result[] = $trimmed;
                    }
                }
            }
        }

        return $result;
    }
}
