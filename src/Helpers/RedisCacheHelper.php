<?php

namespace Lengbin\Hyperf\Common\Helpers;

use Hyperf\Di\Annotation\Inject;
use Lengbin\Common\AbstractRedisCache;
use Hyperf\Redis\Redis;

/**
 * Class RedisHelper
 * @package Lengbin\Hyperf\Common\Helper
 * eg:
 * $data = RedisCacheHelper::getInstance()->getCacheByKeys(['test2', 'test'], function ($results) {
 *              $data = xxxx::findALl($results);
 *              foreach ($results as $key => $result) {
 *                  $results[$key] = $data[$result];
 *              }
 *              return $results;
 *        });
 *
 */
class RedisCacheHelper extends AbstractRedisCache
{
    #[Inject]
    protected Redis $redis;

    public function getRedis()
    {
        return $this->redis;
    }

    public function getConfig(): array
    {
        $config = \Hyperf\Support\config('redis.default', []);
        $config['mc'] = \Hyperf\Config\config('app_name', 'hyperf');
        return $config;
    }
}
