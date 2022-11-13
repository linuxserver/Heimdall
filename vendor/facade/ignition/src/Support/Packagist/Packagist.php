<?php

namespace Facade\Ignition\Support\Packagist;

class Packagist
{
    /**
     * @param string $className
     *
     * @return \Facade\Ignition\Support\Packagist\Package[]
     */
    public static function findPackagesForClassName(string $className): array
    {
        $parts = explode('\\', $className);
        $queryParts = array_splice($parts, 0, 2);

        $url = 'https://packagist.org/search.json?q='.implode(' ', $queryParts);

        try {
            $packages = json_decode(file_get_contents($url));
        } catch (\Exception $e) {
            return [];
        }

        return array_map(function ($packageProperties) {
            return new Package((array) $packageProperties);
        }, $packages->results);
    }
}
