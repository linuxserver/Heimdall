<?php

declare(strict_types=1);

/*
 * This file is part of the league/config package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Config;

use Dflydev\DotAccessData\Data;
use Dflydev\DotAccessData\Exception\DataException;
use Dflydev\DotAccessData\Exception\InvalidPathException;
use Dflydev\DotAccessData\Exception\MissingPathException;
use League\Config\Exception\UnknownOptionException;
use League\Config\Exception\ValidationException;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\Schema;
use Nette\Schema\ValidationException as NetteValidationException;

final class Configuration implements ConfigurationBuilderInterface, ConfigurationInterface
{
    /** @psalm-readonly */
    private Data $userConfig;

    /**
     * @var array<string, Schema>
     *
     * @psalm-allow-private-mutation
     */
    private array $configSchemas = [];

    /** @psalm-allow-private-mutation */
    private ?Data $finalConfig = null;

    /**
     * @var array<string, mixed>
     *
     * @psalm-allow-private-mutation
     */
    private array $cache = [];

    /** @psalm-readonly */
    private ConfigurationInterface $reader;

    /**
     * @param array<string, Schema> $baseSchemas
     */
    public function __construct(array $baseSchemas = [])
    {
        $this->configSchemas = $baseSchemas;
        $this->userConfig    = new Data();

        $this->reader = new ReadOnlyConfiguration($this);
    }

    /**
     * Registers a new configuration schema at the given top-level key
     *
     * @psalm-allow-private-mutation
     */
    public function addSchema(string $key, Schema $schema): void
    {
        $this->invalidate();

        $this->configSchemas[$key] = $schema;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function merge(array $config = []): void
    {
        $this->invalidate();

        $this->userConfig->import($config, Data::REPLACE);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function set(string $key, $value): void
    {
        $this->invalidate();

        try {
            $this->userConfig->set($key, $value);
        } catch (DataException $ex) {
            throw new UnknownOptionException($ex->getMessage(), $key, (int) $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function get(string $key)
    {
        if ($this->finalConfig === null) {
            $this->finalConfig = $this->build();
        } elseif (\array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        try {
            return $this->cache[$key] = $this->finalConfig->get($key);
        } catch (InvalidPathException | MissingPathException $ex) {
            throw new UnknownOptionException($ex->getMessage(), $key, (int) $ex->getCode(), $ex);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-external-mutation-free
     */
    public function exists(string $key): bool
    {
        if ($this->finalConfig === null) {
            $this->finalConfig = $this->build();
        } elseif (\array_key_exists($key, $this->cache)) {
            return true;
        }

        try {
            return $this->finalConfig->has($key);
        } catch (InvalidPathException $ex) {
            return false;
        }
    }

    /**
     * @psalm-mutation-free
     */
    public function reader(): ConfigurationInterface
    {
        return $this->reader;
    }

    /**
     * @psalm-external-mutation-free
     */
    private function invalidate(): void
    {
        $this->cache       = [];
        $this->finalConfig = null;
    }

    /**
     * Applies the schema against the configuration to return the final configuration
     *
     * @throws ValidationException
     *
     * @psalm-allow-private-mutation
     */
    private function build(): Data
    {
        try {
            $schema    = Expect::structure($this->configSchemas);
            $processor = new Processor();
            $processed = $processor->process($schema, $this->userConfig->export());

            $this->raiseAnyDeprecationNotices($processor->getWarnings());

            return $this->finalConfig = new Data(self::convertStdClassesToArrays($processed));
        } catch (NetteValidationException $ex) {
            throw new ValidationException($ex);
        }
    }

    /**
     * Recursively converts stdClass instances to arrays
     *
     * @param mixed $data
     *
     * @return mixed
     *
     * @psalm-pure
     */
    private static function convertStdClassesToArrays($data)
    {
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }

        if (\is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::convertStdClassesToArrays($v);
            }
        }

        return $data;
    }

    /**
     * @param string[] $warnings
     */
    private function raiseAnyDeprecationNotices(array $warnings): void
    {
        foreach ($warnings as $warning) {
            @\trigger_error($warning, \E_USER_DEPRECATED);
        }
    }
}
