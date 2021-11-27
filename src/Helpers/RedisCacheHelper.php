<?php

namespace Lengbin\Hyperf\Common\Helpers;

use EasySwoole\HttpAnnotation\AnnotationTag\Inject;
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
    /**
     * @Inject()
     * @var Redis
     */
    protected Redis $redis;

    public function getRedis()
    {
        return $this->redis;
    }

    public function getConfig(): array
    {
        $config = config('redis.default', []);
        $config['mc'] = config('app_name', 'hyperf');
        return $config;
    }
}
