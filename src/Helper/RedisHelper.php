<?php

namespace Lengbin\Hyperf\Common\Helper;

use Lengbin\Helper\Util\RedisCacheHelper;

/**
 * Class RedisHelper
 * @package Lengbin\Hyperf\Common\Helper
 * eg:
 * $data = RedisHelper::getInstance()->getCacheByKeys(['test2', 'test'], function ($results) {
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
class RedisHelper extends RedisCacheHelper
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
