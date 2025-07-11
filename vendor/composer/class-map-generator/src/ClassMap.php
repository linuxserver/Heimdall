<?php declare(strict_types=1);

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\ClassMapGenerator;

use Composer\Pcre\Preg;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class ClassMap implements \Countable
{
    /**
     * @var array<class-string, non-empty-string>
     */
    public $map = [];

    /**
     * @var array<class-string, array<non-empty-string>>
     */
    private $ambiguousClasses = [];

    /**
     * @var array<string, array<array{warning: string, className: string}>>
     */
    private $psrViolations = [];

    /**
     * Returns the class map, which is a list of paths indexed by class name
     *
     * @return array<class-string, non-empty-string>
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * Returns warning strings containing details about PSR-0/4 violations that were detected
     *
     * Violations are for ex a class which is in the wrong file/directory and thus should not be
     * found using psr-0/psr-4 autoloading but was found by the ClassMapGenerator as it scans all files.
     *
     * This is only happening when scanning paths using psr-0/psr-4 autoload type. Classmap type
     * always accepts every class as it finds it.
     *
     * @return string[]
     */
    public function getPsrViolations(): array
    {
        if (\count($this->psrViolations) === 0) {
            return [];
        }

        return array_map(static function (array $violation): string {
            return $violation['warning'];
        }, array_merge(...array_values($this->psrViolations)));
    }

    /**
     * A map of class names to their list of ambiguous paths
     *
     * This occurs when the same class can be found in several files
     *
     * To get the path the class is being mapped to, call getClassPath
     *
     * By default, paths that contain test(s), fixture(s), example(s) or stub(s) are ignored
     * as those are typically not problematic when they're dummy classes in the tests folder.
     * If you want to get these back as well you can pass false to $duplicatesFilter. Or
     * you can pass your own pattern to exclude if you need to change the default.
     *
     * @param non-empty-string|false $duplicatesFilter
     *
     * @return array<class-string, array<non-empty-string>>
     */
    public function getAmbiguousClasses($duplicatesFilter = '{/(test|fixture|example|stub)s?/}i'): array
    {
        if (false === $duplicatesFilter) {
            return $this->ambiguousClasses;
        }

        if (true === $duplicatesFilter) {
            throw new \InvalidArgumentException('$duplicatesFilter should be false or a string with a valid regex, got true.');
        }

        $ambiguousClasses = [];
        foreach ($this->ambiguousClasses as $class => $paths) {
            $paths = array_filter($paths, function ($path) use ($duplicatesFilter) {
                return !Preg::isMatch($duplicatesFilter, strtr($path, '\\', '/'));
            });
            if (\count($paths) > 0) {
                $ambiguousClasses[$class] = array_values($paths);
            }
        }

        return $ambiguousClasses;
    }

    /**
     * Sorts the class map alphabetically by class names
     */
    public function sort(): void
    {
        ksort($this->map);
    }

    /**
     * @param class-string $className
     * @param non-empty-string $path
     */
    public function addClass(string $className, string $path): void
    {
        unset($this->psrViolations[strtr($path, '\\', '/')]);

        $this->map[$className] = $path;
    }

    /**
     * @param class-string $className
     * @return non-empty-string
     */
    public function getClassPath(string $className): string
    {
        if (!isset($this->map[$className])) {
            throw new \OutOfBoundsException('Class '.$className.' is not present in the map');
        }

        return $this->map[$className];
    }

    /**
     * @param class-string $className
     */
    public function hasClass(string $className): bool
    {
        return isset($this->map[$className]);
    }

    public function addPsrViolation(string $warning, string $className, string $path): void
    {
        $path = rtrim(strtr($path, '\\', '/'), '/');

        $this->psrViolations[$path][] = ['warning' => $warning, 'className' => $className];
    }

    public function clearPsrViolationsByPath(string $pathPrefix): void
    {
        $pathPrefix = rtrim(strtr($pathPrefix, '\\', '/'), '/');

        foreach ($this->psrViolations as $path => $violations) {
            if ($path === $pathPrefix || 0 === \strpos($path, $pathPrefix.'/')) {
                unset($this->psrViolations[$path]);
            }
        }
    }

    /**
     * @param class-string $className
     * @param non-empty-string $path
     */
    public function addAmbiguousClass(string $className, string $path): void
    {
        $this->ambiguousClasses[$className][] = $path;
    }

    public function count(): int
    {
        return \count($this->map);
    }

    /**
     * Get the raw psr violations
     *
     * This is a map of filepath to an associative array of the warning string
     * and the offending class name.
     * @return array<string, array<array{warning: string, className: string}>>
     */
    public function getRawPsrViolations(): array
    {
        return $this->psrViolations;
    }
}
