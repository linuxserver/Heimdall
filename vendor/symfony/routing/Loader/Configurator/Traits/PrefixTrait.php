<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Loader\Configurator\Traits;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * @internal
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
trait PrefixTrait
{
    final protected function addPrefix(RouteCollection $routes, $prefix, bool $trailingSlashOnRoot)
    {
        if (\is_array($prefix)) {
            foreach ($prefix as $locale => $localePrefix) {
                $prefix[$locale] = trim(trim($localePrefix), '/');
            }
            foreach ($routes->all() as $name => $route) {
                if (null === $locale = $route->getDefault('_locale')) {
                    $routes->remove($name);
                    foreach ($prefix as $locale => $localePrefix) {
                        $localizedRoute = clone $route;
                        $localizedRoute->setDefault('_locale', $locale);
                        $localizedRoute->setRequirement('_locale', preg_quote($locale));
                        $localizedRoute->setDefault('_canonical_route', $name);
                        $localizedRoute->setPath($localePrefix.(!$trailingSlashOnRoot && '/' === $route->getPath() ? '' : $route->getPath()));
                        $routes->add($name.'.'.$locale, $localizedRoute);
                    }
                } elseif (!isset($prefix[$locale])) {
                    throw new \InvalidArgumentException(sprintf('Route "%s" with locale "%s" is missing a corresponding prefix in its parent collection.', $name, $locale));
                } else {
                    $route->setPath($prefix[$locale].(!$trailingSlashOnRoot && '/' === $route->getPath() ? '' : $route->getPath()));
                    $routes->add($name, $route);
                }
            }

            return;
        }

        $routes->addPrefix($prefix);
        if (!$trailingSlashOnRoot) {
            $rootPath = (new Route(trim(trim($prefix), '/').'/'))->getPath();
            foreach ($routes->all() as $route) {
                if ($route->getPath() === $rootPath) {
                    $route->setPath(rtrim($rootPath, '/'));
                }
            }
        }
    }
}
