<?php

namespace Lengbin\Hyperf\Common\Helper;

use Lengbin\Helper\YiiSoft\Arrays\ArrayHelper;
use Lengbin\Helper\YiiSoft\StringHelper;

class RedisHelper
{
    /**
     * 获得 时间
     *
     * @param int|null $ttl 过期时间， 如果为 0 表示 不设置过期时间， null 表示redis配置的过期时间
     *
     * @return int|null
     */
    private static function getRedisTtl(?int $ttl = null): ?int
    {
        if (is_null($ttl)) {
            $config = CommonHelper::getConfig()->get('redis', []);
            $ttl = ArrayHelper::get($config, 'ttl', '3600');
            [$min, $max] = ArrayHelper::get($config, 'random', [1, 60]);
            return $ttl + rand($min, $max);
        }

        if ($ttl === 0) {
            return null;
        }
        return $ttl;
    }

    /**
     * @param string      $key
     * @param string|null $prefix
     *
     * @return string
     */
    private static function getCacheKey(string $key, ?string $prefix = null): string
    {
        $mc = CommonHelper::getConfig()->get('app_name', 'hyperf-common');
        if (!is_null($prefix)) {
            $key = $prefix . ':' . $key;
        }
        return sprintf('mc:%s:%s', $mc, $key);
    }

    /**
     * 通过key获得缓存
     *
     * @param string        $key
     * @param callable|null $call
     * @param string|null   $prefix
     * @param int|null      $ttl 过期时间， 如果为 0 表示 不设置过期时间， null 表示redis配置的过期时间
     *
     * @return mixed
     * @throws Throwable
     */
    public static function getCacheByKey(string $key, ?callable $call = null, ?string $prefix = null, ?int $ttl = null)
    {
        $redis = CommonHelper::getRedis();
        $k = self::getCacheKey($key, $prefix);
        $data = $redis->get($k);
        if (!is_null($data)) {
            return $data;
        }
        if (!is_null($call)) {
            $ttl = self::getRedisTtl($ttl);
            $data = call_user_func($call, $key);
        }

        if (StringHelper::isEmpty($data)) {
            $data = [];
            $ttl = 60;
        }
        $data = is_object($data) ? $data->toArray() : $data;
        $redis->set($k, json_encode($data), $ttl);
        return $data;
    }

    /**
     * 获得 多缓存
     *
     * @param array         $keys
     * @param callable|null $call
     * @param string|null   $prefix
     * @param int|null      $ttl
     *
     * @return array
     */
    public static function getCacheByKeys(array $keys, ?callable $call = null, ?string $prefix = null, ?int $ttl = null): array
    {
        if (count($keys) === 0) {
            return [];
        }

        $redis = CommonHelper::getRedis();

        $ks = [];
        foreach ($keys as $key) {
            $ks[] = self::getCacheKey($key, $prefix);
        }
        /* @var array $data */
        $data = $redis->mGet($ks);

        $output = [];
        $missed = [];
        foreach ($data as $index => $item) {
            if (is_null($item)) {
                $key = $keys[$index];
                $missed[$index] = $key;
                continue;
            }
            $output[$index] = json_decode($item, true);
        }
        $models = [];
        if (!is_null($call)) {
            $models = call_user_func($call, $missed);
            $models = $models ?? [];
        }

        foreach ($models as $index => $model) {
            $model = is_object($model) ? $model->toArray() : $model;
            $output[$index] = $model;
            $targetIntersectKey = self::getCacheKey($keys[$index], $prefix);
            $redis->set($targetIntersectKey, json_encode($model), $ttl);
        }
        return $output;
    }

    /**
     * remove
     *
     * @param             $keys
     * @param string|null $prefix
     *
     * @return int
     */
    public static function removeCacheByKey($keys, ?string $prefix = null): int
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $ks = [];
        foreach ($keys as $key) {
            $ks[] = self::getCacheKey($key, $prefix);
        }
        return CommonHelper::getRedis()->del($ks);
    }

}
