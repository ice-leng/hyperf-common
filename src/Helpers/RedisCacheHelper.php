<?php

namespace Lengbin\Hyperf\Common\Helpers;

use Hyperf\Utils\ApplicationContext;
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
    public function getRedis()
    {
        return ApplicationContext::getContainer(Redis::class);
    }

    public function getConfig(): array
    {
        $config = config('redis.default', []);
        $config['mc'] = config('app_name', 'hyperf');
        return $config;
    }
}
