<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;

/**
 * Logs to a Redis key using rpush
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $redis = new RedisHandler(new Predis\Client("tcp://localhost:6379"), "logs", "prod");
 *   $log->pushHandler($redis);
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 *
 * @phpstan-import-type FormattedRecord from AbstractProcessingHandler
 */
class RedisHandler extends AbstractProcessingHandler
{
    /** @var \Predis\Client|\Redis */
    private $redisClient;
    /** @var string */
    private $redisKey;
    /** @var int */
    protected $capSize;

    /**
     * @param \Predis\Client|\Redis $redis   The redis instance
     * @param string                $key     The key name to push records to
     * @param int                   $capSize Number of entries to limit list size to, 0 = unlimited
     */
    public function __construct($redis, string $key, $level = Logger::DEBUG, bool $bubble = true, int $capSize = 0)
    {
        if (!(($redis instanceof \Predis\Client) || ($redis instanceof \Redis))) {
            throw new \InvalidArgumentException('Predis\Client or Redis instance required');
        }

        $this->redisClient = $redis;
        $this->redisKey = $key;
        $this->capSize = $capSize;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record): void
    {
        if ($this->capSize) {
            $this->writeCapped($record);
        } else {
            $this->redisClient->rpush($this->redisKey, $record["formatted"]);
        }
    }

    /**
     * Write and cap the collection
     * Writes the record to the redis list and caps its
     *
     * @phpstan-param FormattedRecord $record
     */
    protected function writeCapped(array $record): void
    {
        if ($this->redisClient instanceof \Redis) {
            $mode = defined('\Redis::MULTI') ? \Redis::MULTI : 1;
            $this->redisClient->multi($mode)
                ->rpush($this->redisKey, $record["formatted"])
                ->ltrim($this->redisKey, -$this->capSize, -1)
                ->exec();
        } else {
            $redisKey = $this->redisKey;
            $capSize = $this->capSize;
            $this->redisClient->transaction(function ($tx) use ($record, $redisKey, $capSize) {
                $tx->rpush($redisKey, $record["formatted"]);
                $tx->ltrim($redisKey, -$capSize, -1);
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter();
    }
}
