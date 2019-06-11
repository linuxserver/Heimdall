<?php

namespace UpdateHelper;

use Composer\Composer;
use Composer\EventDispatcher\Event;
use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Script\Event as ScriptEvent;
use Composer\Semver\Semver;

class UpdateHelper
{
    /** @var Event */
    private $event;
    /** @var IOInterface */
    private $io;
    /** @var Composer */
    private $composer;
    /** @var array */
    private $dependencies = array();
    /** @var string */
    private $composerFilePath;
    /** @var JsonFile */
    private $file;

    protected static function appendConfig(&$classes, $directory, $key = null)
    {
        $file = $directory.DIRECTORY_SEPARATOR.'composer.json';
        $json = new JsonFile($file);
        $key = $key ? $key : 'update-helper';

        try {
            $dependencyConfig = $json->read();
        } catch (\RuntimeException $e) {
            $dependencyConfig = null;
        }

        if (is_array($dependencyConfig) && isset($dependencyConfig['extra'], $dependencyConfig['extra'][$key])) {
            $classes[$file] = $dependencyConfig['extra'][$key];
        }
    }

    protected static function getUpdateHelperConfig(Composer $composer, $key = null)
    {
        $vendorDir = $composer->getConfig()->get('vendor-dir');

        $npm = array();

        foreach (scandir($vendorDir) as $namespace) {
            if ($namespace === '.' || $namespace === '..' || !is_dir($directory = $vendorDir.DIRECTORY_SEPARATOR.$namespace)) {
                continue;
            }

            foreach (scandir($directory) as $dependency) {
                if ($dependency === '.' || $dependency === '..' || !is_dir($subDirectory = $directory.DIRECTORY_SEPARATOR.$dependency)) {
                    continue;
                }

                static::appendConfig($npm, $subDirectory, $key);
            }
        }

        static::appendConfig($npm, dirname($vendorDir), $key);

        return $npm;
    }

    public static function check(Event $event)
    {
        if ($event instanceof ScriptEvent || $event instanceof PackageEvent) {
            $io = $event->getIO();
            $composer = $event->getComposer();
            $autoload = __DIR__.'/../../../../autoload.php';

            if (file_exists($autoload)) {
                include_once $autoload;
            }

            $classes = static::getUpdateHelperConfig($composer);

            foreach ($classes as $file => $class) {
                $error = null;

                if (is_string($class) && class_exists($class)) {
                    try {
                        $helper = new $class();
                    } catch (\Exception $e) {
                        $error = $e->getMessage()."\nFile: ".$e->getFile()."\nLine:".$e->getLine()."\n\n".$e->getTraceAsString();
                    } catch (\Throwable $e) {
                        $error = $e->getMessage()."\nFile: ".$e->getFile()."\nLine:".$e->getLine()."\n\n".$e->getTraceAsString();
                    }

                    if (!$error && $helper instanceof UpdateHelperInterface) {
                        $helper->check(new static($event, $io, $composer));

                        continue;
                    }
                }

                if (!$error) {
                    $error = JsonFile::encode($class).' is not an instance of UpdateHelperInterface.';
                }

                $io->writeError('UpdateHelper error in '.$file.":\n".$error);
            }
        }
    }

    public function __construct(Event $event, IOInterface $io = null, Composer $composer = null)
    {
        $this->event = $event;
        $this->io = $io ?: (method_exists($event, 'getIO') ? $event->getIO() : null);
        $this->composer = $composer ?: (method_exists($event, 'getComposer') ? $event->getComposer() : null);

        if ($this->composer &&
            ($directory = $this->composer->getConfig()->get('archive-dir')) &&
            file_exists($file = $directory.'/composer.json')
        ) {
            $this->composerFilePath = $file;
            $this->file = new JsonFile($this->composerFilePath);
            $this->dependencies = $this->file->read();
        }
    }

    /**
     * @return JsonFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getComposerFilePath()
    {
        return $this->composerFilePath;
    }

    /**
     * @return Composer
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return IOInterface
     */
    public function getIo()
    {
        return $this->io;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * @return array
     */
    public function getDevDependencies()
    {
        return isset($this->dependencies['require-dev']) ? $this->dependencies['require-dev'] : array();
    }

    /**
     * @return array
     */
    public function getProdDependencies()
    {
        return isset($this->dependencies['require']) ? $this->dependencies['require'] : array();
    }

    /**
     * @return array
     */
    public function getFlattenDependencies()
    {
        return array_merge($this->getDevDependencies(), $this->getProdDependencies());
    }

    /**
     * @param string $dependency
     *
     * @return bool
     */
    public function hasAsDevDependency($dependency)
    {
        return isset($this->dependencies['require-dev'][$dependency]);
    }

    /**
     * @param string $dependency
     *
     * @return bool
     */
    public function hasAsProdDependency($dependency)
    {
        return isset($this->dependencies['require'][$dependency]);
    }

    /**
     * @param string $dependency
     *
     * @return bool
     */
    public function hasAsDependency($dependency)
    {
        return $this->hasAsDevDependency($dependency) || $this->hasAsProdDependency($dependency);
    }

    /**
     * @param string $dependency
     * @param string $version
     *
     * @return bool
     */
    public function isDependencyAtLeast($dependency, $version)
    {
        if ($this->hasAsProdDependency($dependency)) {
            return Semver::satisfies($version, $this->dependencies['require'][$dependency]);
        }

        if ($this->hasAsDevDependency($dependency)) {
            return Semver::satisfies($version, $this->dependencies['require-dev'][$dependency]);
        }

        return false;
    }

    /**
     * @param string $dependency
     * @param string $version
     *
     * @return bool
     */
    public function isDependencyLesserThan($dependency, $version)
    {
        return !$this->isDependencyAtLeast($dependency, $version);
    }

    /**
     * @param string $dependency
     * @param string $version
     * @param array  $environments
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setDependencyVersion($dependency, $version, $environments = array('require', 'require-dev'))
    {
        return $this->setDependencyVersions(array($dependency => $version), $environments);
    }

    /**
     * @param array $dependencies
     * @param array $environments
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setDependencyVersions($dependencies, $environments = array('require', 'require-dev'))
    {
        if (!$this->composerFilePath) {
            throw new \RuntimeException('No composer instance detected.');
        }

        $touched = false;

        foreach ($environments as $environment) {
            foreach ($dependencies as $dependency => $version) {
                if (isset($this->dependencies[$environment], $this->dependencies[$environment][$dependency])) {
                    $this->dependencies[$environment][$dependency] = $version;
                    $touched = true;
                }
            }
        }

        if ($touched) {
            if (!$this->composerFilePath) {
                throw new \RuntimeException('composer.json not found (custom vendor-dir are not yet supported).');
            }

            $file = new JsonFile($this->composerFilePath);
            $file->write($this->dependencies);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function update()
    {
        $output = shell_exec('composer update --no-scripts');

        if (!empty($output)) {
            $this->write($output);
        }

        return $this;
    }

    /**
     * @param string|array $text
     */
    public function write($text)
    {
        if ($this->io) {
            $this->io->write($text);

            return;
        }

        if (is_array($text)) {
            $text = implode("\n", $text);
        }

        echo $text;
    }

    /**
     * @return bool
     */
    public function isInteractive()
    {
        return $this->io && $this->io->isInteractive();
    }
}
