<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/craft-psr6/blob/master/LICENSE
 * @link       https://github.com/flipbox/craft-psr6
 */

namespace flipbox\craft\psr6\events;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use Stash\Interfaces\PoolInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterCachePools extends Event
{
    /**
     * @var PoolInterface[]
     */
    protected $pools = [];

    /**
     * @param array $pools
     * @return $this
     */
    public function setPools(array $pools)
    {
        foreach ($pools as $handle => $pool) {
            $this->addPool($handle, $pool);
        }
        return $this;
    }

    /**
     * @param string $handle
     * @param $pool
     * @return $this
     */
    public function addPool(string $handle, $pool)
    {
        $this->pools[$handle] = $pool;
        return $this;
    }

    /**
     * @return PoolInterface[]
     */
    public function getPools()
    {
        $pools = [];
        foreach ($this->pools as $handle => $pool) {
            if (!$pool = $this->resolvePool($pool)) {
                continue;
            }
            $pools[$handle] = $pool;
        }
        return $pools;
    }

    /**
     * @param $pool
     * @return null
     */
    protected function resolvePool($pool)
    {
        if (is_callable($pool)) {
            $pool = $pool();
        }

        if ($pool instanceof PoolInterface) {
            return $pool;
        }

        if (!$class = $this->findClassFromConfig($pool)) {
            Craft::error(
                sprintf(
                    "Could find cache pool class from config: %s",
                    Json::encode($pool)
                ),
                'PSR-6'
            );
            return null;
        }

        // Make sure we have a valid class
        if (!is_subclass_of($class, PoolInterface::class)) {
            Craft::error(
                sprintf(
                    "Cache pool class '%s' is not an instances of %s",
                    (string)$class,
                    PoolInterface::class
                ),
                'PSR-6'
            );
            return null;
        }

        return $class($pool);
    }

    /**
     * Find a class from a config
     *
     * @param $config
     * @param bool $removeClass
     * @return null|string
     */
    private function findClassFromConfig(&$config, bool $removeClass = false)
    {
        if (is_string($config)) {
            // Set as class
            $class = $config;

            // Clear class from config
            $config = '';
        } elseif (is_object($config)) {
            return get_class($config);
        } else {
            // Force Array
            if (!is_array($config)) {
                $config = ArrayHelper::toArray($config, [], false);
            }

            if ($removeClass) {
                if (!$class = ArrayHelper::remove($config, 'class')) {
                    $class = ArrayHelper::remove($config, 'type');
                }
            } else {
                $class = ArrayHelper::getValue(
                    $config,
                    'class',
                    ArrayHelper::getValue($config, 'type')
                );
            }
        }

        return $class;
    }
}
