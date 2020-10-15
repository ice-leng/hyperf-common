<?php

namespace Lengbin\Hyperf\Common\Helper;

use Lengbin\Common\Component\RedisCache;

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
 *
 *
 */
class RedisCacheHelper extends RedisCache
{
    public function getRedis()
    {
        return CommonHelper::getRedis();
    }

    public function getConfig(): array
    {
        $config = CommonHelper::getConfig()->get('redis', []);
        $config['mc'] = CommonHelper::getConfig()->get('app_name');
        return $config;
    }
}
