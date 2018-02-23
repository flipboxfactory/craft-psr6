<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/craft-psr6/blob/master/LICENSE
 * @link       https://github.com/flipbox/craft-psr6
 */

namespace flipbox\craft\psr6;

use Craft;
use Flipbox\Stash\Pool;
use flipbox\craft\psr6\events\RegisterCachePools;
use Psr\Log\LoggerInterface;
use Stash\Driver\BlackHole;
use Stash\Driver\FileSystem;
use Stash\Interfaces\DriverInterface;
use Stash\Interfaces\PoolInterface;
use yii\base\Component;
use yii\caching\FileCache;
use yii\log\Logger;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Cache extends Component
{
    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * The event name
     */
    const EVENT_REGISTER_CACHE_POOLS = 'registerCachePools';

    /**
     * @var DriverInterface
     */
    public $applicationDriver;

    /**
     * @return PoolInterface[]
     */
    public function findAll()
    {
        $cacheDrivers = [
            'default' => $this->getApplicationPool(),
            'dummy' => $this->createDummyPool(),
        ];

        $event = new RegisterCachePools([
            'pools' => $cacheDrivers
        ]);

        Craft::$app->trigger(
            self::EVENT_REGISTER_CACHE_POOLS,
            $event
        );

        return $event->getPools();
    }

    /**
     * @param string $handle
     * @return PoolInterface
     */
    public function get(string $handle = 'default')
    {
        // Get all
        $pools = $this->findAll();

        if (!array_key_exists($handle, $pools)) {
            Craft::error(
                sprintf(
                    "Cache pool does not exist: '%s'.",
                    $handle
                ),
                'PSR-6'
            );
            return $this->createDummyPool();
        }

        return $pools[$handle];
    }

    /**
     * @return Pool
     */
    protected function getApplicationPool()
    {
        // New cache pool
        $pool = new Pool(
            $this->getApplicationDriver()
        );

        // Set default duration
        $pool->setItemDuration(
            Craft::$app->getConfig()->getGeneral()->cacheDuration
        );

        // Add logging
        $this->setLogger($pool);

        return $pool;
    }

    /**
     * @return DriverInterface
     */
    protected function getApplicationDriver()
    {
        if ($this->applicationDriver instanceof DriverInterface) {
            return $this->applicationDriver;
        }

        // Nothing set
        if ($this->applicationDriver === null) {
            $this->applicationDriver = $this->resolveApplicationDriver();
        }

        // Config
        if (is_array($this->applicationDriver)) {
            $this->applicationDriver = $this->createDriver(
                $this->applicationDriver
            );
        }

        return $this->applicationDriver;
    }

    /**
     * @return BlackHole|FileSystem
     */
    private function resolveApplicationDriver()
    {
        $cacheInterface = Craft::$app->getCache();

        // Todo - support other drivers
        if ($cacheInterface instanceof FileCache) {
            return new FileSystem([
                'path' => Craft::getAlias($cacheInterface->cachePath)
            ]);
        }

        Craft::getLogger()->log(
            sprintf(
                "Cache method '{cacheMethod}' is not supported.",
                get_class($cacheInterface)
            ),
            Logger::LEVEL_WARNING
        );

        return new BlackHole();
    }

    /**
     * @param array $config
     * @return object|DriverInterface
     * @throws \yii\base\InvalidConfigException
     */
    private function createDriver(array $config): DriverInterface
    {
        return Craft::createObject(
            $config
        );
    }

    /**
     * @return Pool
     */
    protected function createDummyPool()
    {
        // New cache pool
        $pool = new Pool(
            new BlackHole()
        );

        $this->setLogger($pool);

        return new Pool();
    }

    /**
     * @param \Stash\Pool $pool
     */
    protected function setLogger(\Stash\Pool $pool)
    {
        if (null === $this->logger) {
            $this->logger = $this->applicationLogger(false);
        }

        if ($this->isLoggerValid($this->logger)) {
            $pool->setLogger($this->logger);
        }
    }

    /**
     * @param $logger
     * @return bool
     */
    private function isLoggerValid($logger)
    {
        return $logger && $logger instanceof LoggerInterface;
    }

    /**
     * @param null $default
     * @return null|object
     */
    private function applicationLogger($default = null)
    {
        if (!Craft::$app->has('psr3-logger')) {
            return $default;
        }
        return Craft::$app->get('psr3-logger');
    }
}
