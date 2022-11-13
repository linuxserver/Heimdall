<?php

namespace Facade\Ignition\Support;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ComposerClassMap
{
    /** @var \Composer\Autoload\ClassLoader|FakeComposer */
    protected $composer;

    /** @var string */
    protected $basePath;

    public function __construct(?string $autoloaderPath = null)
    {
        $autoloaderPath = $autoloaderPath ?? base_path('/vendor/autoload.php');

        if (file_exists($autoloaderPath)) {
            $this->composer = require $autoloaderPath;
        } else {
            $this->composer = new FakeComposer();
        }
        $this->basePath = app_path();
    }

    public function listClasses(): array
    {
        $classes = $this->composer->getClassMap();

        return array_merge($classes, $this->listClassesInPsrMaps());
    }

    public function searchClassMap(string $missingClass): ?string
    {
        foreach ($this->composer->getClassMap() as $fqcn => $file) {
            $basename = basename($file, '.php');

            if ($basename === $missingClass) {
                return $fqcn;
            }
        }

        return null;
    }

    public function listClassesInPsrMaps(): array
    {
        // TODO: This is incorrect. Doesnt list all fqcns. Need to parse namespace? e.g. App\LoginController is wrong

        $prefixes = array_merge(
            $this->composer->getPrefixes(),
            $this->composer->getPrefixesPsr4()
        );

        $classes = [];

        foreach ($prefixes as $namespace => $directories) {
            foreach ($directories as $directory) {
                if (file_exists($directory)) {
                    $files = (new Finder())
                        ->in($directory)
                        ->files()
                        ->name('*.php');

                    foreach ($files as $file) {
                        if ($file instanceof SplFileInfo) {
                            $fqcn = $this->getFullyQualifiedClassNameFromFile($namespace, $file);

                            $classes[$fqcn] = $file->getRelativePathname();
                        }
                    }
                }
            }
        }

        return $classes;
    }

    public function searchPsrMaps(string $missingClass): ?string
    {
        $prefixes = array_merge(
            $this->composer->getPrefixes(),
            $this->composer->getPrefixesPsr4()
        );

        foreach ($prefixes as $namespace => $directories) {
            foreach ($directories as $directory) {
                if (file_exists($directory)) {
                    $files = (new Finder())
                        ->in($directory)
                        ->files()
                        ->name('*.php');

                    foreach ($files as $file) {
                        if ($file instanceof SplFileInfo) {
                            $basename = basename($file->getRelativePathname(), '.php');

                            if ($basename === $missingClass) {
                                return $namespace.basename($file->getRelativePathname(), '.php');
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    protected function getFullyQualifiedClassNameFromFile(string $rootNamespace, SplFileInfo $file): string
    {
        $class = trim(str_replace($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return $rootNamespace.$class;
    }
}
